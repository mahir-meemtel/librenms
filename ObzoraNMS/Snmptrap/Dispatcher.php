<?php
namespace ObzoraNMS\Snmptrap;

use App\Facades\ObzoraConfig;
use App\Models\Eventlog;
use ObzoraNMS\Alert\AlertRules;
use ObzoraNMS\Snmptrap\Handlers\Fallback;
use Log;

class Dispatcher
{
    /**
     * Instantiate the correct handler for this trap and call it's handle method
     */
    public static function handle(Trap $trap): bool
    {
        if (empty($trap->getDevice())) {
            Log::warning('Could not find device for trap', ['trap_text' => $trap->raw]);

            return false;
        }

        if ($trap->findOid('iso.3.6.1.6.3.1.1.4.1.0')) {
            // Even the TrapOid is not properly converted to text, so snmptrapd is probably not
            // configured with any MIBs (-M and/or -m).
            // ObzoraNMS snmptraps code cannot process received data. Let's inform the user.
            Eventlog::log('Misconfigured MIBS or MIBDIRS for snmptrapd, check https://docs.obzora.meemtel.com/Extensions/SNMP-Trap-Handler/ : ' . $trap->raw, $trap->getDevice(), 'system');

            return false;
        }

        // note, this doesn't clear the resolved SnpmtrapHandler so only one per run
        /** @var \ObzoraNMS\Interfaces\SnmptrapHandler $handler */
        $handler = app(\ObzoraNMS\Interfaces\SnmptrapHandler::class, [$trap->getTrapOid()]);
        $handler->handle($trap->getDevice(), $trap);

        // log an event if appropriate
        $fallback = $handler instanceof Fallback;
        $logging = ObzoraConfig::get('snmptraps.eventlog', 'unhandled');
        $detailed = ObzoraConfig::get('snmptraps.eventlog_detailed', false);
        if ($logging == 'all' || ($fallback && $logging == 'unhandled')) {
            $trap->log($trap->toString($detailed));
        } else {
            $rules = new AlertRules;
            $rules->runRules($trap->getDevice()->device_id);
        }

        return ! $fallback;
    }
}
