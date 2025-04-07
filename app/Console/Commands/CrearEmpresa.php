<?php

namespace App\Console\Commands;

use App\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CrearEmpresa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:empresa {rut} {razon_social}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para crear una nueva empresa en el sistema de Tenancy.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $check = Tenant::whereRut($this->argument('rut'))->first();
        if($check != null){
            $this->error("El RUT de la empresa ya existe en la base de datos.");
            return;
        }
        $count = Tenant::count()+1;
        $tenant = Tenant::create([
            'rut' => $this->argument('rut'),
            'name' => $this->argument('razon_social'),
            'domain' => 'app_'.$count,
            'database' => 'sgjapp_app'.$count
        ]);

        $this->info("Empresa dada de alta satisfactoriamente");
        $this->info(json_encode($tenant));

        Artisan::call("tenants:artisan \"migrate --database=tenant --seed\" --tenant=".$tenant->id);
        // run landlord specific seeders
        return 0;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if ( ! $input->getArgument('rut')) {
            $input->setArgument('rut', $this->ask('Ingrese el RUT'));
            $input->setArgument('razon_social', $this->ask('Ingrese Razon Social'));
        }
    }
}
