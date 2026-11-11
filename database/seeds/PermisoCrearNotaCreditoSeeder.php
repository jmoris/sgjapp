<?php

namespace Database\Seeders;

use App\Permiso;
use App\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Crea el permiso `crear-nota-credito` (emisión de notas de crédito) en la BD del tenant actual.
 *
 * Seeder PROTEGIDO (solo agrega, nunca modifica ni elimina):
 *  - Si el permiso `crear-nota-credito` ya existe, no hace nada (no lo edita ni cambia sus roles).
 *  - Nunca edita ni borra otros permisos, roles ni asignaciones existentes.
 *  - Solo inserta: 1 fila en `permisos` y filas nuevas en `permisos_roles`.
 *  - Todo corre en una transacción: si algo falla, no queda nada a medias.
 *
 * Roles que reciben el permiso: por defecto solo el rol 1 (administrador). Para otros roles,
 * defina NC_PERMISO_ROLES con ids separados por coma, por ejemplo NC_PERMISO_ROLES=1,2,3.
 * Los ids que no existan como rol se ignoran.
 *
 * Ejecución en todos los tenants:
 *   php artisan tenants:artisan 'db:seed --class="Database\Seeders\PermisoCrearNotaCreditoSeeder" --force'
 */
class PermisoCrearNotaCreditoSeeder extends Seeder
{
    private const TAG = 'crear-nota-credito';

    public function run(): void
    {
        if (Permiso::where('tag', self::TAG)->exists()) {
            $this->command?->warn('El permiso ' . self::TAG . ' ya existe: no se realizaron cambios.');
            return;
        }

        $modulo = optional(Permiso::where('tag', 'ver-nota-credito')->first())->modulo ?? 'notas_credito';
        $ids = array_filter(array_map('intval', explode(',', (string) env('NC_PERMISO_ROLES', '1'))));

        DB::connection((new Permiso())->getConnectionName())->transaction(function () use ($modulo, $ids) {
            $permiso = new Permiso();
            $permiso->tag = self::TAG;
            $permiso->nombre = 'Crear Nota de Crédito';
            $permiso->modulo = $modulo;
            $permiso->save();
            $this->command?->info('Permiso ' . self::TAG . ' creado.');

            foreach (Role::whereIn('id', $ids)->get() as $rol) {
                // syncWithoutDetaching: agrega la fila nueva sin quitar ni tocar las demás asignaciones del rol
                $rol->permisos()->syncWithoutDetaching([$permiso->id]);
                $this->command?->info("Permiso asignado al rol #{$rol->id}.");
            }
        });
    }
}
