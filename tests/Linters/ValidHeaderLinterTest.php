<?php

namespace Lukasss93\Larex\Tests\Linters;

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\ValidHeaderLinter;
use Lukasss93\Larex\Tests\TestCase;

class ValidHeaderLinterTest extends TestCase
{
    public function test_successful(): void
    {
        config(['larex.linters' => [
            ValidHeaderLinter::class,
        ]]);
        
        $this->initFromStub('linters/valid-header/success');
        
        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput('OK (1 linter)')
            ->run();
        
        self::assertEquals(0, $result);
    }
    
    public function test_missing_first_column(): void
    {
        config(['larex.linters' => [
            ValidHeaderLinter::class,
        ]]);
        
        $this->initFromStub('linters/valid-header/missing-first-column');
        
        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput(' FAIL  First header column is missing.')
            ->expectsOutput('FAILURES!')
            ->expectsOutput('Linters: 1, Failures: 1')
            ->run();
        
        self::assertEquals(1, $result);
    }
    
    public function test_invalid_first_column(): void
    {
        config(['larex.linters' => [
            ValidHeaderLinter::class,
        ]]);
        
        $this->initFromStub('linters/valid-header/invalid-first-column');
        
        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput(' FAIL  First header column value is invalid. Must be "group".')
            ->expectsOutput('FAILURES!')
            ->expectsOutput('Linters: 1, Failures: 1')
            ->run();
        
        self::assertEquals(1, $result);
    }
    
    public function test_missing_second_column(): void
    {
        config(['larex.linters' => [
            ValidHeaderLinter::class,
        ]]);
        
        $this->initFromStub('linters/valid-header/missing-second-column');
        
        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput(' FAIL  Second header column is missing.')
            ->expectsOutput('FAILURES!')
            ->expectsOutput('Linters: 1, Failures: 1')
            ->run();
        
        self::assertEquals(1, $result);
    }
    
    public function test_invalid_second_column(): void
    {
        config(['larex.linters' => [
            ValidHeaderLinter::class,
        ]]);
        
        $this->initFromStub('linters/valid-header/invalid-second-column');
        
        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput(' FAIL  Second header column value is invalid. Must be "key".')
            ->expectsOutput('FAILURES!')
            ->expectsOutput('Linters: 1, Failures: 1')
            ->run();
        
        self::assertEquals(1, $result);
    }
    
    public function test_no_language_columns(): void
    {
        config(['larex.linters' => [
            ValidHeaderLinter::class,
        ]]);
        
        $this->initFromStub('linters/valid-header/no-language-columns');
        
        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput(' FAIL  No language columns found.')
            ->expectsOutput('FAILURES!')
            ->expectsOutput('Linters: 1, Failures: 1')
            ->run();
        
        self::assertEquals(1, $result);
    }
}