<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ModuleRouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mapApiRoutes();
    }

    protected function mapApiRoutes(): void
    {
        $modulesPath = base_path('app/Modules');

        if (! is_dir($modulesPath)) {
            return;
        }

        $modules = File::directories($modulesPath);

        foreach ($modules as $modulePath) {
            $moduleName = basename($modulePath);
            $apiRouteFile = $modulePath.'/Routes/api.php';

            if (file_exists($apiRouteFile)) {
                $routeModuleName = Str::lower($moduleName);
                Route::prefix("api/{$routeModuleName}")
                    ->middleware(['api'])
                    ->group($apiRouteFile);
            }
        }
    }
}
