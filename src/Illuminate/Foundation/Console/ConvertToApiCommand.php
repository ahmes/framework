<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ConvertToApiCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'convert-to-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare the application for API usage';

    /**
     * Indicates whether the command should be shown in the Artisan command list.
     *
     * @var bool
     */
    protected $hidden = true;

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        $files = new Filesystem;

        // Remove unnecessary middleware...
        $files->delete(app_path('Http/Middleware/Authenticate.php'));
        $files->delete(app_path('Http/Middleware/RedirectIfAuthenticated.php'));

        // Remove frontend asset related files and directories...
        $files->delete(base_path('package.json'));
        $files->delete(base_path('webpack.mix.js'));

        $files->deleteDirectory(base_path('resources/css'));
        $files->deleteDirectory(base_path('resources/js'));

        // Remove "web" routes file...
        $files->delete(base_path('routes/web.php'));

        // Adjust "views" directory...
        $files->delete(resource_path('views/welcome.blade.php'));
        $files->put(resource_path('views/.gitkeep'), PHP_EOL);

        // Fix tests...
        $files->put(
            base_path('tests/Feature/ExampleTest.php'),
            str_replace("'/'", "'/api'", $files->get(base_path('tests/Feature/ExampleTest.php')))
        );

        // Install stubs...
        $files->copy(__DIR__.'/stubs/convert-to-api/AlwaysAcceptJsonResponses.stub', app_path('Http/Middleware/AlwaysAcceptJsonResponses.php'));
        $files->copy(__DIR__.'/stubs/convert-to-api/Kernel.stub', app_path('Http/Kernel.php'));
        $files->copy(__DIR__.'/stubs/convert-to-api/RouteServiceProvider.stub', app_path('Providers/RouteServiceProvider.php'));
        $files->copy(__DIR__.'/stubs/convert-to-api/api.stub', base_path('routes/api.php'));
    }
}