<?php

namespace App\Services;

use App\Models\AnalisisNlp;
use App\Models\CasoAtencion;
use App\Models\Evaluacion;
use App\Models\Psicologo;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CasoAtencionService
{
    public function forEvaluation(Evaluacion $evaluation, bool $notify = true): CasoAtencion
    {
        return DB::transaction(function () use ($evaluation, $notify) {
            $source = Evaluacion::whereKey($evaluation->id)->lockForUpdate()->firstOrFail();
            $case = CasoAtencion::firstOrCreate(['evaluacion_id' => $source->id]);
            if ($case->wasRecentlyCreated) {
                $diagnosis = $source->diagnostico;
                if ($diagnosis) {
                    $case->update(['estado' => 'seguimiento', 'psicologo_id' => $diagnosis->psicologo_id]);
                }
                $case->seguimientos()->create(['tipo' => 'apertura', 'nota' => null]);
                $this->syncAlert($case);
                if ($notify) {
                    app(AvisoService::class)->newCase($case);
                }
            }

            return $case;
        });
    }

    public function forAnalysis(AnalisisNlp $analysis, bool $notify = true): ?CasoAtencion
    {
        return DB::transaction(function () use ($analysis, $notify) {
            $source = AnalisisNlp::whereKey($analysis->id)->lockForUpdate()->firstOrFail();
            if (! $source->requiere_atencion) {
                return null;
            }
            $case = CasoAtencion::firstOrCreate(['analisis_nlp_id' => $source->id]);
            if ($case->wasRecentlyCreated) {
                $case->seguimientos()->create(['tipo' => 'apertura']);
                if ($notify) {
                    app(AvisoService::class)->newCase($case);
                }
            }

            // Un reanálisis no cierra ni reabre decisiones profesionales anteriores.
            return $case;
        });
    }

    public function authorize(CasoAtencion $case, User $user): Psicologo
    {
        $psych = $user->persona?->psicologo;
        abort_unless($user->hasRole('psicologo') && $psych && $user->can('diagnosticos.crear'), 403);
        abort_unless(CasoAtencion::visibleTo($user)->whereKey($case->id)->exists(), 403);

        return $psych;
    }

    private function syncAlert(CasoAtencion $case): void
    {
        if ($case->evaluacion_id) {
            $case->evaluacion->alerta()->update(['estado' => match ($case->estado) {
                'cerrado' => 'atendida', 'pendiente' => 'generada', default => 'asignada_psicologo',
            }]);
        }
    }

    public function change(CasoAtencion $case, User $user, int $version, string $action, ?string $note = null, ?int $responsible = null): CasoAtencion
    {
        return DB::transaction(function () use ($case, $user, $version, $action, $note, $responsible) {
            $locked = CasoAtencion::whereKey($case->id)->lockForUpdate()->firstOrFail();
            $psych = $this->authorize($locked, $user);
            $this->require(is_string($note) && trim($note) !== '' && mb_strlen($note) <= 3000, 'Registra una nota o motivo de hasta 3000 caracteres.');
            if ($locked->version !== $version) {
                throw ValidationException::withMessages(['version' => 'El caso cambió. Actualiza la página antes de continuar.']);
            }
            abort_unless($locked->psicologo_id === null || (int) $locked->psicologo_id === $psych->id, 403, 'Solo el responsable puede modificar el caso.');
            if ($action === 'asignar') {
                $this->require($locked->estado !== 'cerrado', 'Reabre el caso antes de asignarlo.');
                $target = Psicologo::with('persona.user')->findOrFail($responsible);
                $targetUser = $target->persona?->user;
                $this->require($targetUser && $targetUser->hasRole('psicologo') && $targetUser->can('diagnosticos.crear')
                    && CasoAtencion::visibleTo($targetUser)->whereKey($locked->id)->exists(), 'El responsable no tiene acceso al origen del caso.');
                $this->require((int) $locked->psicologo_id !== $target->id, 'El caso ya tiene ese responsable.');
                $locked->psicologo_id = $target->id;
                $locked->asignado_at = now();
                if ($locked->estado === 'pendiente') {
                    $locked->estado = 'asignado';
                }
            } elseif ($action === 'reabrir') {
                $this->require($locked->estado === 'cerrado', 'Solo se reabren casos cerrados.');
                if ($locked->psicologo_id === null) {
                    $locked->psicologo_id = $psych->id;
                    $locked->asignado_at = now();
                }
                $locked->estado = 'seguimiento';
                $locked->cerrado_at = null;
            } else {
                $this->require($locked->estado !== 'cerrado' && $locked->psicologo_id !== null, 'Primero asigna un responsable a un caso abierto.');
                if ($action === 'cerrar') {
                    $this->require($locked->estado === 'seguimiento', 'Registra una valoración o nota de seguimiento antes del cierre.');
                    $locked->estado = 'cerrado';
                    $locked->cerrado_at = now();
                } else {
                    $this->require($action === 'nota', 'Acción no válida.');
                    $locked->estado = 'seguimiento';
                }
            }
            $locked->version++;
            $locked->save();
            $locked->seguimientos()->create(['autor_id' => $user->id, 'responsable_id' => $locked->psicologo_id, 'tipo' => $action, 'nota' => $note]);
            $this->syncAlert($locked);

            return $locked;
        });
    }

    public function registerAssessment(CasoAtencion $case, User $user): void
    {
        $locked = CasoAtencion::whereKey($case->id)->lockForUpdate()->firstOrFail();
        $psych = $this->authorize($locked, $user);
        abort_unless($locked->psicologo_id === null || (int) $locked->psicologo_id === $psych->id, 403);
        $this->require($locked->estado !== 'cerrado', 'Reabre el caso antes de registrar una valoración.');
        $locked->update(['psicologo_id' => $psych->id, 'asignado_at' => $locked->asignado_at ?? now(), 'estado' => 'seguimiento', 'version' => $locked->version + 1]);
        $locked->seguimientos()->create(['autor_id' => $user->id, 'responsable_id' => $psych->id, 'tipo' => 'valoracion']);
        $this->syncAlert($locked);
    }

    private function require(bool $condition, string $message): void
    {
        if (! $condition) {
            throw ValidationException::withMessages(['caso' => $message]);
        }
    }
}
