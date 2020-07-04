<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Utils;

class LarexInitCommand extends Command
{
    private const FILE = 'resources/lang/localization.csv';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larex:init {--base : Init the CSV file with default Laravel entries }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Init the CSV file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $stub = 'base';

        if($this->option('base')) {
            $stub = 'laravel';
        }

        if(File::exists(self::FILE)) {
            $this->error(self::FILE . ' already exists.');
            return;
        }

        File::put(self::FILE, Utils::getStub($stub));

        $this->info(self::FILE . ' created successfully.');
    }
}