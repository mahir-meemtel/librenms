<?php
namespace ObzoraNMS\Tests;

trait SnmpsimHelpers
{
    public function requireSnmpsim(): void
    {
        if (! getenv('SNMPSIM')) {
            $this->markTestSkipped('Snmpsim required for this test. Start snmpsim in another console first with lnms dev:simulate and set SNMPSIM=127.1.6.1:1161');
        }
    }

    public function getSnmpsimIp(): ?string
    {
        $snmpsim = explode(':', getenv('SNMPSIM'));

        return $snmpsim[0] ?? null;
    }

    public function getSnmpsimPort(): int
    {
        $snmpsim = explode(':', getenv('SNMPSIM'));

        return (int) ($snmpsim[1] ?? 161);
    }
}
