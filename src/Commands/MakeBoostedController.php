<?php
namespace GGuney\Boosters\Commands;

use Illuminate\Console\Command;

class MakeBoostedController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:boostedController {modelName} {--g}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It creates Boosted Controller from given model name.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $controllerPath = 'Http/Controllers/';

        $isGeneral = $this->option('g');
        $modelName = ucfirst($this->argument('modelName'));
        $boostedControllerName = $modelName.'Controller';

        if(!$isGeneral){
            $DMName = ucfirst(str_plural($modelName));
            $txt = file_get_contents(__DIR__.'/BoostedController.stub') or die("Unable to open file!");
            $txt = str_replace('{DM_NAME}', $DMName, $txt);
            $txt = str_replace('{MODEL_NAME}', $modelName, $txt);
        }
        else
            $txt = file_get_contents(__DIR__.'/GeneralController.stub') or die("Unable to open file!");

        $txt = str_replace('{BOOSTED_CONTROLLER_NAME}', $boostedControllerName, $txt);

        $path = app_path($controllerPath.$boostedControllerName.'.php');
        $myfile = fopen($path, "w") or die("Unable to open file!");
        fwrite($myfile, $txt);
        fclose($myfile);

        $this->info($boostedControllerName.' named Controller created with boosters included in '.$controllerPath.'.');
    }
}
