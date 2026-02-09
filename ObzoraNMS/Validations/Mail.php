<?php
namespace ObzoraNMS\Validations;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Validator;

class Mail extends BaseValidation
{
    protected static $RUN_BY_DEFAULT = false;

    /**
     * Validate this module.
     * To return ValidationResults, call ok, warn, fail, or result methods on the $validator
     *
     * @param  Validator  $validator
     */
    public function validate(Validator $validator): void
    {
        if (ObzoraConfig::get('alert.transports.mail') === true) {
            $run_test = 1;
            if (! ObzoraConfig::has('alert.default_mail')) {
                $validator->fail('default_mail config option needs to be specified to test email');
                $run_test = 0;
            } elseif (ObzoraConfig::get('email_backend') == 'sendmail') {
                if (! ObzoraConfig::has('email_sendmail_path')) {
                    $validator->fail('You have selected sendmail but not configured email_sendmail_path');
                    $run_test = 0;
                } elseif (! file_exists(ObzoraConfig::get('email_sendmail_path'))) {
                    $validator->fail('The configured email_sendmail_path is not valid');
                    $run_test = 0;
                }
            } elseif (ObzoraConfig::get('email_backend') == 'smtp') {
                if (! ObzoraConfig::has('email_smtp_host')) {
                    $validator->fail('You have selected SMTP but not configured an SMTP host');
                    $run_test = 0;
                }
                if (! ObzoraConfig::has('email_smtp_port')) {
                    $validator->fail('You have selected SMTP but not configured an SMTP port');
                    $run_test = 0;
                }
                if (ObzoraConfig::get('email_smtp_auth')
                    && (! ObzoraConfig::has('email_smtp_username') || ! ObzoraConfig::has('email_smtp_password'))
                ) {
                    $validator->fail('You have selected SMTP auth but have not configured both username and password');
                    $run_test = 0;
                }
            }//end if
            if ($run_test == 1) {
                $email = ObzoraConfig::get('alert.default_mail');
                try {
                    \ObzoraNMS\Util\Mail::send($email, 'Test email', 'Testing email from NMS');
                    $validator->ok('Email has been sent');
                } catch (\Exception $e) {
                    $validator->fail("Issue sending email to $email with error " . $e->getMessage());
                }
            }
        }//end if
    }
}
