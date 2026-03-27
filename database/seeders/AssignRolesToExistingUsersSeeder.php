<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class AssignRolesToExistingUsersSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $estudianteIds = DB::table('estudiantes')
            ->join('personas', 'estudiantes.persona_id', '=', 'personas.id')
            ->pluck('personas.user_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $tutorIds = DB::table('tutores')
            ->join('personas', 'tutores.persona_id', '=', 'personas.id')
            ->pluck('personas.user_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $psicologoIds = DB::table('psicologos')
            ->join('personas', 'psicologos.persona_id', '=', 'personas.id')
            ->pluck('personas.user_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $adminEmails = [
            'imanol10albiter@gmail.com',
        ];

        User::query()->each(function (User $user) use ($estudianteIds, $tutorIds, $psicologoIds, $adminEmails) {
            $roles = [];

            if (in_array($user->id, $estudianteIds, true)) {
                $roles[] = 'estudiante';
            }

            if (in_array($user->id, $tutorIds, true)) {
                $roles[] = 'tutor';
            }

            if (in_array($user->id, $psicologoIds, true)) {
                $roles[] = 'psicologo';
            }

            if (in_array($user->email, $adminEmails, true)) {
                $roles[] = 'admin';
            }

            if (!empty($roles)) {
                $user->syncRoles($roles);
            }
        });
    }
}
