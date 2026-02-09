<?php
namespace ObzoraNMS\Enum;

enum MaintenanceBehavior: int
{
    case SKIP_ALERTS = 1;
    case MUTE_ALERTS = 2;
    case RUN_ALERTS = 3;
}
