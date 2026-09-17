<?php

namespace App\Http\Controllers;

use App\Models\Evaluacion;
use App\Models\Instrumento;
use App\Services\Dass21CoverageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeReport($request);
        [$query, $from, $to] = $this->query($request);
        $rows = $query->paginate(30)->withQueryString();
        $options = [];
        foreach (['grupo', 'carrera', 'ciclo'] as $dimension) {
            $options[$dimension] = Evaluacion::whereNotNull($dimension.'_aplicacion_id')
                ->select($dimension.'_aplicacion_id as id', $dimension.'_aplicacion_nombre as nombre')
                ->distinct()->orderBy('nombre')->get();
        }
        $instrumentos = Instrumento::orderBy('acronimo')->get();

        return view('reportes.index', compact('rows', 'options', 'instrumentos', 'from', 'to'));
    }

    private function authorizeReport(Request $request): void
    {
        abort_unless($request->user()->hasAnyRole(['admin', 'psicologo', 'control_escolar'])
            && $request->user()->can('reportes_globales.ver'), 403);
    }

    private function query(Request $request): array
    {
        [$from, $to] = app(Dass21CoverageService::class)->period($request);
        $data = $request->validate([
            'grupo' => ['nullable', 'integer', 'min:1'], 'carrera' => ['nullable', 'integer', 'min:1'],
            'ciclo' => ['nullable', 'integer', 'min:1'], 'instrumento' => ['nullable', 'integer', 'min:1'],
            'contexto' => ['nullable', 'in:registrado,historico,sin_grupo'],
        ]);
        $query = DB::table('evaluaciones as e')->join('instrumentos as i', 'i.id', '=', 'e.instrumento_id')
            ->where('e.estado', 'completada')->whereBetween('e.created_at', [$from, $to]);
        foreach (['grupo', 'carrera', 'ciclo'] as $dimension) {
            if (! empty($data[$dimension])) {
                $query->where("e.{$dimension}_aplicacion_id", $data[$dimension]);
            }
        }
        if (! empty($data['instrumento'])) {
            $query->where('e.instrumento_id', $data['instrumento']);
        }
        if (($data['contexto'] ?? '') === 'historico') {
            $query->whereNull('e.contexto_registrado_at');
        }
        if (in_array($data['contexto'] ?? '', ['registrado', 'sin_grupo'])) {
            $query->whereNotNull('e.contexto_registrado_at');
        }
        if (($data['contexto'] ?? '') === 'sin_grupo') {
            $query->whereNull('e.grupo_aplicacion_id');
        }
        $context = "CASE WHEN e.contexto_registrado_at IS NULL THEN 'Asignación histórica no registrada' WHEN e.grupo_aplicacion_id IS NULL THEN 'Sin grupo al responder' ELSE 'Registrada al responder' END";
        $columns = ['i.id', 'i.acronimo'];
        foreach (['grupo', 'carrera', 'ciclo'] as $dimension) {
            $columns[] = "e.{$dimension}_aplicacion_id";
            $columns[] = "e.{$dimension}_aplicacion_nombre";
        }
        $query->select($columns)->selectRaw("$context as contexto, COUNT(*) as aplicaciones, COUNT(DISTINCT e.codigo_anonimo) as participantes")
            ->groupBy($columns)->groupByRaw($context)->orderBy('i.acronimo')->orderBy('e.grupo_aplicacion_id')
            ->orderBy('e.carrera_aplicacion_id')->orderBy('e.ciclo_aplicacion_id')->orderBy('contexto');

        return [$query, $from, $to];
    }

    public function export(Request $request)
    {
        $this->authorizeReport($request);
        [$query, $from, $to] = $this->query($request);

        return response()->streamDownload(function () use ($query, $from, $to) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Desde', 'Hasta', 'Instrumento', 'Carrera', 'Grupo', 'Ciclo', 'Contexto', 'Aplicaciones', 'Participantes'], ',', '"', '');
            foreach ($query->cursor() as $row) {
                $values = [$from->toDateString(), $to->toDateString(), $row->acronimo,
                    $row->carrera_aplicacion_nombre ?? 'Sin registro', $row->grupo_aplicacion_nombre ?? 'Sin registro',
                    $row->ciclo_aplicacion_nombre ?? 'Sin registro', $row->contexto, $row->aplicaciones, $row->participantes];
                $values = array_map(fn ($v) => preg_match('/^[\s]*[=+\-@\t\r\n]/u', (string) $v) ? "'".$v : $v, $values);
                fputcsv($out, $values, ',', '"', '');
            }
            fclose($out);
        }, 'participacion-'.$from->toDateString().'-'.$to->toDateString().'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
