<?php
namespace ObzoraNMS\Tests\Traits;

trait RequiresDatabase
{
    public static function setUpBeforeClass(): void
    {
        if (! getenv('DBTEST')) {
            static::markTestSkipped('Database tests not enabled.  Set DBTEST=1 to enable.');
        }

        parent::setUpBeforeClass();
    }
}
