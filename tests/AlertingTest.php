<?php
namespace ObzoraNMS\Tests;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class AlertingTest extends TestCase
{
    public function testJsonAlertCollection(): void
    {
        $rules = get_rules_from_json();
        $this->assertIsArray($rules);
        foreach ($rules as $rule) {
            $this->assertIsArray($rule);
        }
    }

    public function testTransports(): void
    {
        foreach ($this->getTransportFiles() as $file => $_unused) {
            $parts = explode('/', $file);
            $transport = ucfirst(str_replace('.php', '', array_pop($parts)));
            $class = 'ObzoraNMS\\Alert\\Transport\\' . $transport;
            $this->assertTrue(class_exists($class), "The transport $transport does not exist");
            $this->assertInstanceOf(\ObzoraNMS\Interfaces\Alert\Transport::class, new $class);
        }
    }

    private function getTransportFiles(): RegexIterator
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('ObzoraNMS/Alert/Transport'));

        return new RegexIterator($iterator, '/^.+\.php$/i', RegexIterator::GET_MATCH);
    }
}
