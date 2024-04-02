<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MakeWebModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:webmodel {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $modelName = $this->argument('model');
        Artisan::call('make:model '.$modelName.' -mc');
        // Se crean las carpetas y ficheros correspondientes.
        File::makeDirectory('./resources/views/pages/'.strtolower($modelName).'s');

        return Command::SUCCESS;
    }
}
