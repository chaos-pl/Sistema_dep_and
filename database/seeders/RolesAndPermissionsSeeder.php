<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // perfil
            'perfil.ver',
            'perfil.editar',

            // institucional
            'carreras.ver',
            'carreras.crear',
            'carreras.editar',
            'carreras.eliminar',

            'grupos.ver',
            'grupos.crear',
            'grupos.editar',
            'grupos.eliminar',
            'grupos.ver.asignados',

            // usuarios / personas
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',
            'usuarios.ver.grupo',
            'usuarios.asignar_roles',

            'personas.ver',
            'personas.crear',
            'personas.editar',
            'personas.eliminar',

            // roles
            'roles.ver',
            'roles.crear',
            'roles.editar',
            'roles.eliminar',
            'roles.asignar_permisos',

            // permisos
            'permisos.ver',
            'permisos.crear',
            'permisos.editar',
            'permisos.eliminar',

            // instrumentos
            'instrumentos.ver',
            'instrumentos.crear',
            'instrumentos.editar',
            'instrumentos.eliminar',

            // evaluaciones
            'evaluaciones.realizar',
            'evaluaciones.historial.propio',
            'evaluaciones.historial.global',
            'evaluaciones.respuestas.detalle',

            // alertas
            'alertas.ver.general',
            'alertas.ver.clinicas',
            'alertas.asignar',
            'alertas.atender',

            // diagnosticos
            'diagnosticos.ver',
            'diagnosticos.crear',
            'diagnosticos.editar',
            'diagnosticos.derivar',

            // retroalimentacion
            'retroalimentacion.ver.propia',
            'retroalimentacion.crear',
            'retroalimentacion.editar',

            // diario ia
            'diario_ia.crear',
            'diario_ia.ver.propio',

            // resultados ia
            'resultados_ia.ver',

            // reportes
            'reportes_globales.ver',

            // consentimiento / privacidad
            'consentimiento.ver',
            'consentimiento.aceptar',
            'aviso_privacidad.ver',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $estudiante = Role::firstOrCreate([
            'name' => 'estudiante',
            'guard_name' => 'web',
        ]);

        $tutor = Role::firstOrCreate([
            'name' => 'tutor',
            'guard_name' => 'web',
        ]);

        $psicologo = Role::firstOrCreate([
            'name' => 'psicologo',
            'guard_name' => 'web',
        ]);

        $admin->syncPermissions([
            'perfil.ver',
            'perfil.editar',

            'carreras.ver',
            'carreras.crear',
            'carreras.editar',
            'carreras.eliminar',

            'grupos.ver',
            'grupos.crear',
            'grupos.editar',
            'grupos.eliminar',

            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',
            'usuarios.asignar_roles',

            'personas.ver',
            'personas.crear',
            'personas.editar',
            'personas.eliminar',

            'roles.ver',
            'roles.crear',
            'roles.editar',
            'roles.eliminar',
            'roles.asignar_permisos',

            'permisos.ver',
            'permisos.crear',
            'permisos.editar',
            'permisos.eliminar',

            'instrumentos.ver',
            'instrumentos.crear',
            'instrumentos.editar',
            'instrumentos.eliminar',

            'reportes_globales.ver',

            'consentimiento.ver',
            'aviso_privacidad.ver',
        ]);

        $estudiante->syncPermissions([
            'perfil.ver',
            'perfil.editar',

            'evaluaciones.realizar',
            'evaluaciones.historial.propio',

            'retroalimentacion.ver.propia',

            'diario_ia.crear',
            'diario_ia.ver.propio',

            'consentimiento.ver',
            'consentimiento.aceptar',
            'aviso_privacidad.ver',
        ]);

        $tutor->syncPermissions([
            'perfil.ver',
            'perfil.editar',

            'grupos.ver.asignados',
            'usuarios.ver.grupo',

            'alertas.ver.general',

            'consentimiento.ver',
            'consentimiento.aceptar',
            'aviso_privacidad.ver',
        ]);

        $psicologo->syncPermissions([
            'perfil.ver',
            'perfil.editar',

            'instrumentos.ver',

            'evaluaciones.historial.global',
            'evaluaciones.respuestas.detalle',

            'alertas.ver.clinicas',
            'alertas.asignar',
            'alertas.atender',

            'diagnosticos.ver',
            'diagnosticos.crear',
            'diagnosticos.editar',
            'diagnosticos.derivar',

            'retroalimentacion.crear',
            'retroalimentacion.editar',

            'resultados_ia.ver',

            'reportes_globales.ver',

            'consentimiento.ver',
            'consentimiento.aceptar',
            'aviso_privacidad.ver',
        ]);
    }
}
