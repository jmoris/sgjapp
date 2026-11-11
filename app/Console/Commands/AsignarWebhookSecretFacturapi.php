<?php

namespace App\Console\Commands;

use App\Tenant;
use Illuminate\Console\Command;

class AsignarWebhookSecretFacturapi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facturapi:set-webhook-secret {rut} {secret}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asigna el secreto HMAC del webhook de FacturAPI v3 (entregado por su panel al configurar la URL) a una empresa existente.';

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

        $tenant->facturapi_webhook_secret = $this->argument('secret');
        $tenant->save();

        $this->info("Secreto de webhook de FacturAPI v3 asignado a " . $tenant->name);

        return 0;
    }
}
