<?php
namespace ObzoraNMS\Tests\Unit;

use Artisan;
use Illuminate\Database\QueryException;
use ObzoraNMS\Tests\TestCase;

class SqliteTest extends TestCase
{
    private $connection = 'testing_persistent';

    public function testMigrationsRunWithoutError(): void
    {
        try {
            $result = Artisan::call('migrate:fresh', ['--database' => $this->connection, '--seed' => true]);
            $output = Artisan::output();

            $this->assertEquals(0, $result, "SQLite migration failed:\n$output");
            $this->assertNotEmpty($output, 'Migrations not run');
        } catch (QueryException $queryException) {
            $migrationOutput = Artisan::output();
            preg_match('/\s+(\w+) \.+ [\w.]+ FAIL$/', $migrationOutput, $matches);
            $migration = $matches[1] ?? '?';
            $output = isset($matches[1]) ? '' : "\n\n" . $migrationOutput;
            $this->fail($queryException->getMessage() . $output . "\n\nCould not run migration {$migration} on SQLite");
        }

        $count = \DB::connection($this->connection)->table('alert_templates')->count();
        $this->assertGreaterThan(0, $count, 'Database content check failed.');
    }
}
