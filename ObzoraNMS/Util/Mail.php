<?php
namespace ObzoraNMS\Util;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Exceptions\RrdGraphException;
use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    /**
     * Parse string with emails. Return array with email (as key) and name (as value)
     *
     * @param  string  $emails
     * @return array|false
     */
    public static function parseEmails($emails)
    {
        $result = [];
        $regex = '/^[\"\']?([^\"\']+)[\"\']?\s{0,}<([^@]+@[^>]+)>$/';
        if (is_string($emails)) {
            $emails = preg_split('/[,;]\s{0,}/', $emails);
            foreach ($emails as $email) {
                if (preg_match($regex, $email, $out, PREG_OFFSET_CAPTURE)) {
                    $result[$out[2][0]] = $out[1][0];
                } else {
                    if (strpos($email, '@')) {
                        $from_name = ObzoraConfig::get('email_user');
                        $result[$email] = $from_name;
                    }
                }
            }

            return $result;
        }

        // Return FALSE if input not string
        return false;
    }

    /**
     * Send email with PHPMailer
     *
     * @param  array|string  $emails
     * @param  string  $subject
     * @param  string  $message
     * @param  bool  $html
     * @param  bool  $bcc
     * @param  bool|null  $embedGraphs
     * @return bool
     *
     * @throws \PHPMailer\PHPMailer\Exception if delivery fails
     */
    public static function send($emails, $subject, $message, bool $html = false, bool $bcc = false, ?bool $embedGraphs = null): bool
    {
        if (is_array($emails) || ($emails = self::parseEmails($emails))) {
            d_echo("Attempting to email $subject to: " . implode('; ', array_keys($emails)) . PHP_EOL);
            $mail = new PHPMailer(true);
            $mail->Hostname = php_uname('n');

            foreach (self::parseEmails(ObzoraConfig::get('email_from')) as $from => $from_name) {
                $mail->setFrom($from, $from_name);
            }

            // add addresses
            $addMethod = $bcc ? 'addBCC' : 'addAddress';
            foreach ($emails as $email => $email_name) {
                $mail->$addMethod($email, $email_name);
            }

            $mail->Subject = $subject;
            $mail->XMailer = ObzoraConfig::get('project_name');
            $mail->CharSet = 'utf-8';
            $mail->WordWrap = 76;
            $mail->Body = $message;
            if ($embedGraphs ?? ObzoraConfig::get('email_attach_graphs')) {
                self::embedGraphs($mail, $html);
            }
            if ($html) {
                $mail->isHTML();
            }
            switch (strtolower(trim(ObzoraConfig::get('email_backend')))) {
                case 'sendmail':
                    $mail->Mailer = 'sendmail';
                    $mail->Sendmail = ObzoraConfig::get('email_sendmail_path');
                    break;
                case 'smtp':
                    $mail->isSMTP();
                    $mail->Host = ObzoraConfig::get('email_smtp_host');
                    $mail->Timeout = ObzoraConfig::get('email_smtp_timeout');
                    $mail->SMTPAuth = ObzoraConfig::get('email_smtp_auth');
                    $mail->SMTPSecure = ObzoraConfig::get('email_smtp_secure');
                    $mail->Port = ObzoraConfig::get('email_smtp_port');
                    $mail->Username = ObzoraConfig::get('email_smtp_username');
                    $mail->Password = ObzoraConfig::get('email_smtp_password');
                    $mail->SMTPAutoTLS = ObzoraConfig::get('email_auto_tls');
                    $mail->SMTPDebug = 0;
                    $mail->SMTPOptions = [
                        'ssl' => [
                            'verify_peer' => ObzoraConfig::get('email_smtp_verifypeer', true),
                            'allow_self_signed' => ObzoraConfig::get('email_smtp_allowselfsigned', false),
                        ],
                    ];
                    break;
                default:
                    $mail->Mailer = 'mail';
                    break;
            }

            return $mail->send();
        }

        throw new \PHPMailer\PHPMailer\Exception('No contacts found');
    }

    /**
     * Search for generated graph links, generate them, attach them to the email and update the url to a cid link
     */
    private static function embedGraphs(PHPMailer $mail, bool $html = false): void
    {
        $body = $mail->Body;

        // search for generated graphs
        preg_match_all('#<img class=\"obzora-graph\" src=\"(.*?)\" ?/?>#', $body, $matches);

        $count = 0;
        foreach (array_combine($matches[1], $matches[0]) as $url => $tag) {
            try {
                $cid = 'graph' . ++$count;

                // fetch image data
                $image = Graph::getImage($url);

                // attach image
                $fileName = substr(Clean::fileName($image->title ?: $cid), 0, 250);
                $mail->addStringEmbeddedImage(
                    $image->data,
                    $cid,
                    $fileName . '.' . $image->fileExtension(),
                    PHPMailer::ENCODING_BASE64,
                    $image->format->contentType()
                );

                // update image tag to link to attached image, or just the image name
                if ($html) {
                    $body = str_replace($url, "cid:$cid", $body);
                } else {
                    $body = str_replace($tag, "[$fileName]", $body);
                }
            } catch (RrdGraphException|\PHPMailer\PHPMailer\Exception $e) {
                report($e);
            }
        }

        $mail->Body = $body;
    }
}
