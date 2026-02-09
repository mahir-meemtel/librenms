<?php
namespace ObzoraNMS\Validations;

use App\Facades\ObzoraConfig;
use Exception;
use Illuminate\Support\Facades\Cache;
use ObzoraNMS\ValidationResult;
use ObzoraNMS\Validator;

class Scheduler extends BaseValidation
{
    /**
     * Validate this module.
     * To return ValidationResults, call ok, warn, fail, or result methods on the $validator
     *
     * @param  Validator  $validator
     */
    public function validate(Validator $validator): void
    {
        try {
            $scheduler_working = Cache::has('scheduler_working');
        } catch (Exception $e) {
            $validator->fail(trans('validation.validations.poller.CheckLocking.fail', ['message' => $e->getMessage()]));

            return;
        }

        if (! $scheduler_working) {
            $commands = $this->generateCommands($validator);
            $validator->result(ValidationResult::fail('Scheduler is not running')->setFix($commands));
        }
    }

    /**
     * @param  Validator  $validator
     * @return array
     */
    private function generateCommands(Validator $validator): array
    {
        $commands = [];
        $systemctl_bin = ObzoraConfig::locateBinary('systemctl');
        $base_dir = rtrim($validator->getBaseDir(), '/');

        if (is_executable($systemctl_bin)) {
            // systemd exists
            if ($base_dir === '/opt/obzora') {
                // standard install dir
                $commands[] = 'sudo cp /opt/obzora/dist/obzora-scheduler.service /opt/obzora/dist/obzora-scheduler.timer /etc/systemd/system/';
            } else {
                // non-standard install dir
                $commands[] = "sudo sh -c 'sed \"s#/opt/obzora#$base_dir#\" $base_dir/dist/obzora-scheduler.service > /etc/systemd/system/obzora-scheduler.service'";
                $commands[] = "sudo sh -c 'sed \"s#/opt/obzora#$base_dir#\" $base_dir/dist/obzora-scheduler.timer > /etc/systemd/system/obzora-scheduler.timer'";
            }
            $commands[] = 'sudo systemctl enable obzora-scheduler.timer';
            $commands[] = 'sudo systemctl start obzora-scheduler.timer';

            return $commands;
        }

        // non-systemd use cron
        if ($base_dir === '/opt/obzora') {
            $commands[] = 'sudo cp /opt/obzora/dist/obzora-scheduler.cron /etc/cron.d/';

            return $commands;
        }

        // non-standard install dir
        $commands[] = "sudo sh -c 'sed \"s#/opt/obzora#$base_dir#\" $base_dir/dist/obzora-scheduler.cron > /etc/cron.d/obzora-scheduler.cron'";

        return $commands;
    }
}
