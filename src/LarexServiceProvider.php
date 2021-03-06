<?php

namespace Lukasss93\Larex;

use Illuminate\Support\ServiceProvider;
use Lukasss93\Larex\Console\LarexCommand;
use Lukasss93\Larex\Console\LarexExportCommand;
use Lukasss93\Larex\Console\LarexImportCommand;
use Lukasss93\Larex\Console\LarexInitCommand;
use Lukasss93\Larex\Console\LarexInsertCommand;
use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Console\LarexSortCommand;

class LarexServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPublishables();
    }
    
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/larex.php', 'larex');
        
        $this->registerCommands();
    }
    
    protected function registerCommands(): void
    {
        $this->commands([
            LarexInitCommand::class,
            LarexCommand::class,
            LarexExportCommand::class,
            LarexImportCommand::class,
            LarexSortCommand::class,
            LarexInsertCommand::class,
            LarexLintCommand::class,
        ]);
    }
    
    protected function registerPublishables(): void
    {
        $this->publishes([
            __DIR__ . '/config/larex.php' => config_path('larex.php'),
        ], 'larex-config');
    }
}
