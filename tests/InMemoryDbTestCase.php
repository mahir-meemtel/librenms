<?php
namespace ObzoraNMS\Tests;

class InMemoryDbTestCase extends TestCase
{
    /** @var string */
    protected $connection = 'testing_memory';

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh', ['--database' => $this->connection]);

        $current = config('database.default');
        config(['database.default' => $this->connection]);
        \DB::purge($current);
    }
}
