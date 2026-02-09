<?php
namespace ObzoraNMS\Alert\Transport;

use App\Facades\ObzoraConfig;
use Exception;
use Illuminate\Support\Str;
use ObzoraNMS\Alert\AlertUtil;
use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use Spatie\Permission\Models\Role;

class Mail extends Transport
{
    public function deliverAlert(array $alert_data): bool
    {
        $emails = match ($this->config['mail-contact'] ?? '') {
            'sysContact' => AlertUtil::findContactsSysContact($alert_data['faults']),
            'owners' => AlertUtil::findContactsOwners($alert_data['faults']),
            'role' => AlertUtil::findContactsRoles([$this->config['role']]),
            default => $this->config['email'] ?? $alert_data['contacts'] ?? [], // contacts is only used by legacy synthetic transport
        };

        $html = ObzoraConfig::get('email_html');

        if ($html && ! $this->isHtmlContent($alert_data['msg'])) {
            // if there are no html tags in the content, but we are sending an html email, use br for line returns instead
            $msg = preg_replace("/\r?\n/", "<br />\n", $alert_data['msg']);
        } else {
            // fix line returns for windows mail clients
            $msg = preg_replace("/(?<!\r)\n/", "\r\n", $alert_data['msg']);
        }

        try {
            return \ObzoraNMS\Util\Mail::send($emails, $alert_data['title'], $msg, $html, $this->config['bcc'] ?? false, $this->config['attach-graph'] ?? null);
        } catch (Exception $e) {
            throw new AlertTransportDeliveryException($alert_data, 0, $e->getMessage());
        }
    }

    public static function configTemplate(): array
    {
        $roles = ['None' => ''];
        foreach (Role::query()->pluck('name')->all() as $name) {
            $roles[$name] = Str::title(str_replace('-', ' ', $name));
        }

        return [
            'config' => [
                [
                    'title' => 'Contact Type',
                    'name' => 'mail-contact',
                    'descr' => 'Method for selecting contacts',
                    'type' => 'select',
                    'options' => [
                        'Specified Email' => 'email',
                        'Device sysContact' => 'sysContact',
                        'Owner(s)' => 'owners',
                        'Role' => 'role',
                    ],
                    'default' => 'email',
                ],
                [
                    'title' => 'Email',
                    'name' => 'email',
                    'descr' => 'Email address of contact',
                    'type' => 'text',
                ],
                [
                    'title' => 'Role',
                    'name' => 'role',
                    'descr' => 'Role of users to mail',
                    'type' => 'select',
                    'options' => $roles,
                ],
                [
                    'title' => 'BCC',
                    'name' => 'bcc',
                    'descr' => 'Use BCC instead of TO',
                    'type' => 'checkbox',
                    'default' => false,
                ],
                [
                    'title' => 'Include Graphs',
                    'name' => 'attach-graph',
                    'descr' => 'Include graph image data in the email.  Will be embedded if html5, otherwise attached. Template must use @signedGraphTag',
                    'type' => 'checkbox',
                    'default' => true,
                ],
            ],
            'validation' => [
                'mail-contact' => 'required|in:email,sysContact,owners,role',
                'email' => 'required_if:mail-contact,email|prohibited_unless:mail-contact,email|email',
                'role' => 'required_if:mail-contact,role|prohibited_unless:mail-contact,role|exists:roles,name',
            ],
        ];
    }
}
