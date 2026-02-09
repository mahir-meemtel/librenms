<?php
namespace ObzoraNMS\Snmptrap\Handlers;

class JnxDomLaneAlarmId
{
    public static function getLaneAlarms(string $currentAlarm): string
    {
        $alarmBin = preg_split(
            '//',
            sprintf('%024s', decbin(hexdec(str_replace(' ', '', $currentAlarm)))),
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        $alarmDescr = [
            'input signal high',
            'input signal low',
            'output bias high',
            'output bias low',
            'output signal high',
            'output signal low',
            'lane laser temp high',
            'lane laster temp low',
        ];

        $descr = [];
        $index = 0;
        foreach ($alarmBin as $syntax) {
            if ($syntax == '1') {
                $descr[$index] = $alarmDescr[$index];
            }
            $index++;
        }

        return implode(', ', $descr);
    }
}
