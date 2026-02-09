<?php
namespace ObzoraNMS\Validations;

use App\Facades\ObzoraConfig;
use Illuminate\Contracts\Encryption\DecryptException;
use ObzoraNMS\DB\Eloquent;
use ObzoraNMS\Validator;

class Configuration extends BaseValidation
{
    /**
     * Validate this module.
     * To return ValidationResults, call ok, warn, fail, or result methods on the $validator
     *
     * @param  Validator  $validator
     */
    public function validate(Validator $validator): void
    {
        // Test transports
        if (ObzoraConfig::get('alerts.email.enable') == true) {
            $validator->warn('You have the old alerting system enabled - this is to be deprecated on the 1st of June 2015: https://groups.google.com/forum/#!topic/obzora-project/1llxos4m0p4');
        }

        if (config('app.debug')) {
            $validator->warn('Debug enabled.  This is a security risk.');
        }

        if (Eloquent::isConnected() && ! \DB::table('devices')->exists()) {
            $validator->warn('You have no devices.', 'Consider adding a device such as localhost: ' . $validator->getBaseURL() . '/addhost');
        }

        if (ObzoraConfig::has('validation.encryption.test')) {
            try {
                if (\Crypt::decryptString(ObzoraConfig::get('validation.encryption.test')) !== 'obzora') {
                    $this->failKeyChanged($validator);
                }
            } catch (DecryptException $e) {
                $this->failKeyChanged($validator);
            }
        } else {
            ObzoraConfig::persist('validation.encryption.test', \Crypt::encryptString('obzora'));
        }
    }

    /**
     * @param  Validator  $validator
     */
    private function failKeyChanged(Validator $validator): void
    {
        $validator->fail(
            'APP_KEY does not match key used to encrypt data. APP_KEY must be the same on all nodes.',
            'If you rotated APP_KEY, run lnms key:rotate to resolve.'
        );
    }
}
