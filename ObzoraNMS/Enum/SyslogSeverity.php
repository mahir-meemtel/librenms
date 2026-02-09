<?php
namespace ObzoraNMS\Enum;

class SyslogSeverity
{
    const EMERGENCY = 'emerg';
    const ALERT = 'alert';
    const ERROR = 'err';
    const WARNING = 'warning';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';
    const CRITICAL = 'crit';

    const LEVELS = [
        0 => self::EMERGENCY,
        1 => self::ALERT,
        2 => self::CRITICAL,
        3 => self::ERROR,
        4 => self::WARNING,
        5 => self::NOTICE,
        6 => self::INFO,
        7 => self::DEBUG,
    ];
}
