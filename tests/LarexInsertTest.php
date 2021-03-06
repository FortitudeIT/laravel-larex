<?php

namespace Lukasss93\Larex\Tests;

class LarexInsertTest extends TestCase
{
    public function test_insert_command_if_file_does_not_exists(): void
    {
        $result = $this->artisan('larex:insert')
            ->expectsOutput("The '$this->file' does not exists.")
            ->expectsOutput('Please create it with: php artisan larex:init')
            ->run();
        
        self::assertEquals(1, $result);
    }
    
    public function test_insert_command(): void
    {
        $this->initFromStub('insert/input');
        
        $result = $this->artisan('larex:insert')
            ->expectsQuestion('Enter the group', 'app')
            ->expectsQuestion('Enter the key', 'uncle')
            ->expectsQuestion('[1/2] Enter the value for [en] language', 'Uncle')
            ->expectsQuestion('[2/2] Enter the value for [it] language', 'Zio')
            ->expectsOutput('Item added successfully.')
            ->run();
        
        self::assertEquals(0, $result);
    }
    
    public function test_insert_command_with_export(): void
    {
        $this->initFromStub('insert/input');
        
        $result = $this->artisan('larex:insert --export')
            ->expectsQuestion('Enter the group', 'app')
            ->expectsQuestion('Enter the key', 'uncle')
            ->expectsQuestion('[1/2] Enter the value for [en] language', 'Uncle')
            ->expectsQuestion('[2/2] Enter the value for [it] language', 'Zio')
            ->expectsOutput('Item added successfully.')
            ->expectsOutput('')
            ->expectsOutput("Processing the '" . config('larex.csv.path') . "' file...")
            ->expectsOutput('resources/lang/en/app.php created successfully.')
            ->expectsOutput('resources/lang/it/app.php created successfully.')
            ->run();
        
        self::assertEquals(0, $result);
    }
}