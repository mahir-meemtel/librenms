<?php
namespace ObzoraNMS\Tests;

use ObzoraNMS\Tests\Traits\RequiresDatabase;

abstract class DBTestCase extends TestCase
{
    use RequiresDatabase;
}
