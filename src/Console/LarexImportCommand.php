<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Utils;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class LarexImportCommand extends Command
{
    /**
     * Localization file path
     *
     * @var string
     */
    protected $file;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larex:import {--f|force : Overwrite csv file if already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import entries from resources/lang files';

    /**
     * Create a new console command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->file = config('larex.csv.path');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $languages = collect([]);
        $rawValues = collect([]);

        $this->warn('Importing entries...');

        //get all php files
        $files = File::glob(resource_path('lang/**/*.php'));

        foreach ($files as $file) {
            $array = include $file;
            $group = pathinfo($file, PATHINFO_FILENAME);
            $lang = basename(dirname($file));

            if (!$languages->contains($lang)) {
                $languages->push($lang);
            }

            //loop through array recursive
            $iterator = new RecursiveIteratorIterator(
                new RecursiveArrayIterator($array),
                RecursiveIteratorIterator::SELF_FIRST
            );
            $path = [];
            foreach ($iterator as $key => $value) {
                $path[$iterator->getDepth()] = $key;
                if (!is_array($value)) {
                    $rawValues->push([
                        'group' => $group,
                        'key' => implode('.', array_slice($path, 0, $iterator->getDepth() + 1)),
                        'lang' => $lang,
                        'value' => $value,
                    ]);
                }
            }
        }

        //creating the csv file
        $header = collect(['group', 'key'])->merge($languages);
        $headerCount = $header->count();
        $data = collect([]);

        foreach ($rawValues as $rawValue) {
            $index = $data->search(function ($item) use ($rawValue) {
                return $item[0] === $rawValue['group'] && $item[1] === $rawValue['key'];
            });

            if ($index === false) {
                $output = [
                    $rawValue['group'],
                    $rawValue['key'],
                ];

                for ($i = 2; $i < $headerCount; $i++) {
                    $real = $rawValue['lang'] === $header->get($i) ? $rawValue['value'] : '';
                    $output[$i] = $real;
                }

                $data->push($output);
            } else {
                for ($i = 2; $i < $headerCount; $i++) {
                    $code = $rawValue['lang'] === $header->get($i) ? $rawValue['value'] : null;

                    if ($code !== null) {
                        $new = $data->get($index);
                        $new[$i] = $rawValue['value'];
                        $data->put($index, $new);
                    }
                }
            }
        }

        //add header
        $data->prepend($header->toArray());
        $data = $data->values();

        $force = $this->option('force');

        //check file exists
        if (!$force && File::exists(base_path($this->file))) {
            $this->error("The '{$this->file}' already exists.");
            return 1;
        }

        Utils::collectionToCsv($data, base_path($this->file));
        $this->info('Files imported successfully.');
        
        return 0;
    }
}
