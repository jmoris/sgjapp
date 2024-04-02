<?php

namespace Database\Seeders;

use App\Permiso;
use App\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesPermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisosCsv = "id;nombre;modulo;tag
        1;Ver Usuarios;usuarios;ver-usuario
        2;Crear Usuarios;usuarios;crear-usuario
        3;Editar Usuarios;usuarios;editar-usuario
        4;Eliminar Usuarios;usuarios;eliminar-usuario
        5;Crear Proveedor;proveedores;crear-proveedor
        6;Ver Proveedor;proveedores;ver-proveedor
        7;Editar Proveedor;proveedores;editar-proveedor
        8;Eliminar Proveedor;proveedores;eliminar-proveedor
        9;Ver Productos;productos;ver-producto
        10;Editar Producto;productos;editar-producto
        11;Crear Producto;productos;crear-producto
        12;Eliminar Producto;productos;eliminar-producto
        13;Ver Orden de Compra;ordenes_compra;ver-orden-compra
        14;Editar Orden de Compra;ordenes_compra;editar-orden-compra
        15;Crear Orden de Compra;ordenes_compra;crear-orden-compra
        16;Eliminar Orden de Compra;ordenes_compra;eliminar-orden-compra
        17;Ver Administracion;administracion;ver-administracion
        18;Editar Administracion;administracion;editar-administracion";

        $rol = new Role();
        $rol->nombre = "Administrador";
        $rol->save();

        $permisos = explode(PHP_EOL, $permisosCsv);
        array_shift($permisos);
        foreach ($permisos as $line) {
            $data = str_getcsv($line, ';');

            Permiso::create([
                'nombre' => $data[1],
                'modulo' => $data[2],
                'tag' => $data[3]
            ]);
        }
        $fullPermisos = Permiso::all()->pluck('id');
        $rol->permisos()->sync($fullPermisos);
    }
}
