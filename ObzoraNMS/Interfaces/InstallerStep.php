<?php
namespace ObzoraNMS\Interfaces;

interface InstallerStep
{
    public function enabled(): bool;

    public function complete(): bool;

    public function icon(): string;
}
