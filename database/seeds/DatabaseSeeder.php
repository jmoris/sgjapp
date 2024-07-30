<?php

use App\Categoria;
use App\Config;
use App\Permiso;
use App\Role;
use App\Unidad;
use App\User;
use Database\Seeders\ComunasRegionesSeeder;
use Database\Seeders\RolesPermisosSeeder;
use Illuminate\Database\Seeder;
use Spatie\Multitenancy\Models\Tenant;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Tenant::checkCurrent()
           ? $this->runTenantSpecificSeeders()
           : $this->runLandlordSpecificSeeders();

    }

    public function runTenantSpecificSeeders()
    {
        $tenant = Tenant::current();
        $this->call(RolesPermisosSeeder::class);
        $this->call(ComunasRegionesSeeder::class);
        if(User::count() == 0){
            $user = new User();
            $user->name = "Jesus";
            $user->lastname = "Moris Hernandez";
            $user->cargo = 'Ing. Civil en Computación';
            $user->email = "jesus@soluciontotal.cl";
            $user->role_id = 1;
            $user->password = bcrypt("Moris.234");
            $user->save();
        }
        if(Unidad::count() == 0){
            $unidad = new Unidad();
            $unidad->nombre = "Unidad";
            $unidad->abreviacion = "Und";
            $unidad->save();
        }

        if(Categoria::count() == 0){
            $categoria = new Categoria();
            $categoria->codigo_interno = "GRAL";
            $categoria->nombre = "General";
            $categoria->descripcion = "Productos de caracter general o sin categoria especifica";
            $categoria->save();
        }

        if(Config::count() == 0){
            $config = new Config();
            $config->key = "emisor_razonsocial";
            $config->value = $tenant->name;
            $config->save();
            $config = new Config();
            $config->key = "emisor_rut";
            $config->value = $tenant->rut;
            $config->save();
            $config = new Config();
            $config->key = "emisor_direccion";
            $config->value = "BELLAVISTA 519";
            $config->save();
            $config = new Config();
            $config->key = "emisor_comuna";
            $config->value = "177";
            $config->save();
            $config = new Config();
            $config->key = "emisor_telefono";
            $config->value = "75 2 412060";
            $config->save();
            $config = new Config();
            $config->key = "emisor_email";
            $config->value = "contacto@joremet.cl";
            $config->save();
            $config = new Config();
            $config->key = "emisor_web";
            $config->value = "www.joremet.cl";
            $config->save();
            $config = new Config();
            $config->key = "emisor_giro";
            $config->value = "FABRICACION DE PRODUCTOS METALICOS PARA USO
            ESTRUCTURAL";
            $config->save();
            $config = new Config();
            $config->key = "emisor_acteco";
            $config->value = 251100;
            $config->save();
        }
    }

    public function runLandlordSpecificSeeders()
    {

    }
}
