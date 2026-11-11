<?php

namespace App\Console\Commands;

use App\Tenant;
use Illuminate\Console\Command;

class AsignarTokenFacturapi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facturapi:set-token {rut} {token} {facturapi_tenant_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asigna las credenciales de FacturAPI v3 (Bearer token y X-Tenant) a una empresa existente.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenant = Tenant::whereRut($this->argument('rut'))->first();
        if ($tenant == null) {
            $this->error("No existe una empresa con el RUT " . $this->argument('rut'));

            return 1;
        }

        $tenant->facturapi_token = $this->argument('token');
        $tenant->facturapi_tenant_id = $this->argument('facturapi_tenant_id');
        $tenant->save();

        $this->info("Credenciales de FacturAPI v3 asignadas a " . $tenant->name);

        return 0;
    }
}
