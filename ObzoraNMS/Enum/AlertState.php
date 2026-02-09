<?php
namespace ObzoraNMS\Enum;

abstract class AlertState
{
    const CLEAR = 0;
    const ACTIVE = 1;
    const ACKNOWLEDGED = 2;
    const WORSE = 3;
    const BETTER = 4;
    const CHANGED = 5;
    const RECOVERED = 0;
}
