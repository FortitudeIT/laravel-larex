<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Support\Facades\File;

class LarexExportTest extends TestCase
{
    public function test_larex_export_without_entries(): void
    {
        $this->artisan('larex:init')->run();
        
        $result = $this->artisan('larex:export')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput('No entries found.')
            ->run();
        
        self::assertEquals(2, $result);
    }
    
    public function test_larex_export_fail_when_localization_file_not_exists(): void
    {
        $result = $this->artisan('larex:export')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("The '$this->file' does not exists.")
            ->expectsOutput('Please create it with: php artisan larex:init')
            ->run();
        
        self::assertEquals(1, $result);
    }
    
    public function test_larex_export_fail_when_include_exclude_are_together(): void
    {
        $this->initFromStub('export/larex-input');
        
        $result = $this->artisan('larex:export --include= --exclude=')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("The --include and --exclude options can be used only one at a time.")
            ->run();
        
        self::assertEquals(1, $result);
    }
    
    /** @dataProvider providerWarning
     * @param string $stub
     * @param string $output
     */
    public function test_larex_export_with_warning(string $stub, string $output): void
    {
        $this->initFromStub($stub);
        
        $result = $this->artisan('larex:export -v')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput($output)
            ->expectsOutput('resources/lang/en/app.php created successfully.')
            ->run();
        
        self::assertFileExists(resource_path('lang/en/app.php'));
        
        self::assertEquals(
            $this->getTestStub('export/warning-output'),
            File::get(resource_path('lang/en/app.php'))
        );
        
        self::assertEquals(0, $result);
    }
    
    public function test_larex_export(): void
    {
        $this->initFromStub('export/larex-input');
        
        $result = $this->artisan('larex:export')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en/app.php created successfully.")
            ->expectsOutput("resources/lang/en/another.php created successfully.")
            ->expectsOutput("resources/lang/it/app.php created successfully.")
            ->expectsOutput("resources/lang/it/another.php created successfully.")
            ->run();
        
        self::assertFileExists(resource_path('lang/en/app.php'));
        self::assertFileExists(resource_path('lang/en/another.php'));
        self::assertFileExists(resource_path('lang/it/app.php'));
        self::assertFileExists(resource_path('lang/it/another.php'));
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-en-app'),
            File::get(resource_path('lang/en/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-en-another'),
            File::get(resource_path('lang/en/another.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-it-app'),
            File::get(resource_path('lang/it/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-it-another'),
            File::get(resource_path('lang/it/another.php'))
        );
        
        self::assertEquals(0, $result);
    }
    
    public function test_larex_export_with_watch(): void
    {
        $this->initFromStub('export/larex-input');
        
        $result = $this->artisan('larex:export --watch')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en/app.php created successfully.")
            ->expectsOutput("resources/lang/en/another.php created successfully.")
            ->expectsOutput("resources/lang/it/app.php created successfully.")
            ->expectsOutput("resources/lang/it/another.php created successfully.")
            ->expectsOutput('Waiting for changes...')
            ->run();
        
        self::assertFileExists(resource_path('lang/en/app.php'));
        self::assertFileExists(resource_path('lang/en/another.php'));
        self::assertFileExists(resource_path('lang/it/app.php'));
        self::assertFileExists(resource_path('lang/it/another.php'));
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-en-app'),
            File::get(resource_path('lang/en/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-en-another'),
            File::get(resource_path('lang/en/another.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-it-app'),
            File::get(resource_path('lang/it/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-it-another'),
            File::get(resource_path('lang/it/another.php'))
        );
        
        self::assertEquals(0, $result);
    }
    
    public function test_larex_export_with_numeric_keys(): void
    {
        $this->initFromStub('export/numeric/input');
        
        $result = $this->artisan('larex:export')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en/app.php created successfully.")
            ->expectsOutput("resources/lang/it/app.php created successfully.")
            ->run();
        
        self::assertFileExists(resource_path('lang/en/app.php'));
        self::assertFileExists(resource_path('lang/it/app.php'));
        
        self::assertEquals(
            $this->getTestStub('export/numeric/output-en'),
            File::get(resource_path('lang/en/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/numeric/output-it'),
            File::get(resource_path('lang/it/app.php'))
        );
        
        self::assertEquals(0, $result);
    }
    
    public function test_larex_export_with_empty_values(): void
    {
        $this->initFromStub('export/empty/input');
        
        $result = $this->artisan('larex:export')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en/app.php created successfully.")
            ->expectsOutput("resources/lang/it/app.php created successfully.")
            ->run();
        
        self::assertFileExists(resource_path('lang/en/app.php'));
        self::assertFileExists(resource_path('lang/it/app.php'));
        
        self::assertEquals(
            $this->getTestStub('export/empty/output-en'),
            File::get(resource_path('lang/en/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/empty/output-it'),
            File::get(resource_path('lang/it/app.php'))
        );
        
        self::assertEquals(0, $result);
    }
    
    public function test_larex_export_with_enclosures(): void
    {
        $this->initFromStub('export/enclosure/input');
        
        $result = $this->artisan('larex:export')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en/app.php created successfully.")
            ->run();
        
        self::assertFileExists(resource_path('lang/en/app.php'));
        
        self::assertEquals(
            $this->getTestStub('export/enclosure/output-en'),
            File::get(resource_path('lang/en/app.php'))
        );
        
        self::assertEquals(0, $result);
    }
    
    public function test_larex_export_with_space(): void
    {
        $this->initFromStub('export/space/input');
        
        $result = $this->artisan('larex:export')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en/app.php created successfully.")
            ->expectsOutput("resources/lang/it/app.php created successfully.")
            ->run();
        
        self::assertFileExists(resource_path('lang/en/app.php'));
        self::assertFileExists(resource_path('lang/it/app.php'));
        
        self::assertEquals(
            $this->getTestStub('export/space/output-en'),
            File::get(resource_path('lang/en/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/space/output-it'),
            File::get(resource_path('lang/it/app.php'))
        );
        
        self::assertEquals(0, $result);
    }
    
    public function test_larex_export_with_novalue_verbose(): void
    {
        $this->initFromStub('export/novalue/input');
        
        $result = $this->artisan('larex:export -v')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput('app.2 at line 3, column 4 (it) is missing. It will be skipped.')
            ->expectsOutput("resources/lang/en/app.php created successfully.")
            ->expectsOutput("resources/lang/it/app.php created successfully.")
            ->run();
        
        self::assertFileExists(resource_path('lang/en/app.php'));
        self::assertFileExists(resource_path('lang/it/app.php'));
        
        self::assertEquals(
            $this->getTestStub('export/novalue/output-en'),
            File::get(resource_path('lang/en/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/novalue/output-it'),
            File::get(resource_path('lang/it/app.php'))
        );
        
        self::assertEquals(0, $result);
    }
    
    public function test_larex_export_with_novalue(): void
    {
        $this->initFromStub('export/novalue/input');
        
        $result = $this->artisan('larex:export -v')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en/app.php created successfully.")
            ->expectsOutput("resources/lang/it/app.php created successfully.")
            ->run();
        
        self::assertFileExists(resource_path('lang/en/app.php'));
        self::assertFileExists(resource_path('lang/it/app.php'));
        
        self::assertEquals(
            $this->getTestStub('export/novalue/output-en'),
            File::get(resource_path('lang/en/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/novalue/output-it'),
            File::get(resource_path('lang/it/app.php'))
        );
        
        self::assertEquals(0, $result);
    }
    
    public function test_larex_export_with_include_empty(): void
    {
        $this->initFromStub('export/larex-input');
        
        $result = $this->artisan('larex:export --include=')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput('No entries found.')
            ->run();
        
        self::assertEquals(2, $result);
    }
    
    public function test_larex_export_with_include(): void
    {
        $this->initFromStub('export/larex-input');
        
        $result = $this->artisan('larex:export --include=it')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/it/app.php created successfully.")
            ->expectsOutput("resources/lang/it/another.php created successfully.")
            ->run();
        
        self::assertFileExists(resource_path('lang/it/app.php'));
        self::assertFileExists(resource_path('lang/it/another.php'));
        self::assertFalse(File::exists(resource_path('lang/en/app.php')));
        self::assertFalse(File::exists(resource_path('lang/en/another.php')));
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-it-app'),
            File::get(resource_path('lang/it/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-it-another'),
            File::get(resource_path('lang/it/another.php'))
        );
        
        self::assertEquals(0, $result);
    }
    
    public function test_larex_export_with_exclude_empty(): void
    {
        $this->initFromStub('export/larex-input');
        
        $result = $this->artisan('larex:export --exclude=')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en/app.php created successfully.")
            ->expectsOutput("resources/lang/en/another.php created successfully.")
            ->expectsOutput("resources/lang/it/app.php created successfully.")
            ->expectsOutput("resources/lang/it/another.php created successfully.")
            ->run();
        
        self::assertFileExists(resource_path('lang/en/app.php'));
        self::assertFileExists(resource_path('lang/en/another.php'));
        self::assertFileExists(resource_path('lang/it/app.php'));
        self::assertFileExists(resource_path('lang/it/another.php'));
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-en-app'),
            File::get(resource_path('lang/en/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-en-another'),
            File::get(resource_path('lang/en/another.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-it-app'),
            File::get(resource_path('lang/it/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-it-another'),
            File::get(resource_path('lang/it/another.php'))
        );
        
        self::assertEquals(0, $result);
    }
    
    public function test_larex_export_with_exclude(): void
    {
        $this->initFromStub('export/larex-input');
        
        $result = $this->artisan('larex:export --exclude=it')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en/app.php created successfully.")
            ->expectsOutput("resources/lang/en/another.php created successfully.")
            ->run();
        
        self::assertFileExists(resource_path('lang/en/app.php'));
        self::assertFileExists(resource_path('lang/en/another.php'));
        self::assertFalse(File::exists(resource_path('lang/it/app.php')));
        self::assertFalse(File::exists(resource_path('lang/it/another.php')));
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-en-app'),
            File::get(resource_path('lang/en/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-en-another'),
            File::get(resource_path('lang/en/another.php'))
        );
        
        self::assertEquals(0, $result);
    }
    
    public function providerWarning(): array
    {
        return [
            'blank line' => ['export/warning-input-1', 'Invalid row at line 3. The row will be skipped.'],
            'missing key' => ['export/warning-input-2', 'Missing key name at line 3. The row will be skipped.'],
            'missing column' => [
                'export/warning-input-3',
                'app.second at line 3, column 3 (en) is missing. It will be skipped.'
            ],
        ];
    }
}