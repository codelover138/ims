<?php defined('BASEPATH') OR exit('No direct script access allowed');


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Tec_mail
{
    protected $gamma_mail_from_email;
    protected $gamma_mail_from_name;
    protected $gamma_mail_reply_to_email;
    protected $gamma_mail_reply_to_name;

    public function __construct() {
        $this->load->config('gamma', true);
        $this->gamma_mail_from_email = trim((string) $this->config->item('gamma_mail_from_email', 'gamma'));
        $this->gamma_mail_from_name = trim((string) $this->config->item('gamma_mail_from_name', 'gamma'));
        $this->gamma_mail_reply_to_email = trim((string) $this->config->item('gamma_mail_reply_to_email', 'gamma'));
        $this->gamma_mail_reply_to_name = trim((string) $this->config->item('gamma_mail_reply_to_name', 'gamma'));
    }

    public function __get($var) {
        return get_instance()->$var;
    }

    protected function getConfiguredFromEmail($fallback = null)
    {
        if ($this->gamma_mail_from_email && filter_var($this->gamma_mail_from_email, FILTER_VALIDATE_EMAIL)) {
            return $this->gamma_mail_from_email;
        }

        if ($fallback && filter_var($fallback, FILTER_VALIDATE_EMAIL)) {
            return $fallback;
        }

        if (!empty($this->Settings->default_email) && filter_var($this->Settings->default_email, FILTER_VALIDATE_EMAIL)) {
            return $this->Settings->default_email;
        }

        if (!empty($this->Settings->smtp_user) && filter_var($this->Settings->smtp_user, FILTER_VALIDATE_EMAIL)) {
            return $this->Settings->smtp_user;
        }

        return null;
    }

    protected function getConfiguredFromName($fallback = null)
    {
        if ($this->gamma_mail_from_name !== '') {
            return $this->gamma_mail_from_name;
        }

        if ($fallback !== null && $fallback !== '') {
            return $fallback;
        }

        return $this->Settings->site_name;
    }

    protected function getConfiguredReplyToEmail($fromEmail)
    {
        if ($this->gamma_mail_reply_to_email && filter_var($this->gamma_mail_reply_to_email, FILTER_VALIDATE_EMAIL)) {
            return $this->gamma_mail_reply_to_email;
        }

        return $fromEmail;
    }

    protected function getConfiguredReplyToName($fromName)
    {
        if ($this->gamma_mail_reply_to_name !== '') {
            return $this->gamma_mail_reply_to_name;
        }

        return $fromName;
    }

    public function send_mail($to, $subject, $body, $from = NULL, $from_name = NULL, $attachment = NULL, $cc = NULL, $bcc = NULL) {

        $mail = new PHPMailer;
        // $mail->SMTPDebug = 4;
        $mail->CharSet = 'UTF-8';
        try {
            if ($this->Settings->protocol == 'mail') {
                $mail->isMail();
            } elseif ($this->Settings->protocol == 'sendmail') {
                $mail->isSendmail();
            } elseif ($this->Settings->protocol == 'smtp') {
                $mail->isSMTP();
                $mail->Host = $this->Settings->smtp_host;
                $mail->SMTPAuth = true;
                $mail->Username = $this->Settings->smtp_user;
                $mail->Password = $this->Settings->smtp_pass;
                $mail->SMTPSecure = !empty($this->Settings->smtp_crypto) ? $this->Settings->smtp_crypto : false;
                $mail->Port = $this->Settings->smtp_port;
                // $mail->SMTPDebug = 2;
            } else {
                $mail->isMail();
            }

            $resolvedFromEmail = $this->getConfiguredFromEmail($from);
            $resolvedFromName = $this->getConfiguredFromName($from_name);
            $resolvedReplyToEmail = $this->getConfiguredReplyToEmail($resolvedFromEmail);
            $resolvedReplyToName = $this->getConfiguredReplyToName($resolvedFromName);

            if (!$resolvedFromEmail) {
                throw new \Exception('No valid outgoing sender email is configured.');
            }

            $mail->setFrom($resolvedFromEmail, $resolvedFromName);
            $mail->Sender = $resolvedFromEmail;
            $mail->addReplyTo($resolvedReplyToEmail, $resolvedReplyToName);

            $mail->addAddress($to);
            if ($cc) { $mail->addCC($cc); }
            if ($bcc) { $mail->addBCC($bcc); }
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $body;
            $mail->AltBody = trim(html_entity_decode(strip_tags(str_replace(array('<br>', '<br/>', '<br />', '</p>', '</div>'), PHP_EOL, $body)), ENT_QUOTES, 'UTF-8'));
            if ($attachment) {
                if (is_array($attachment)) {
                    foreach ($attachment as $attach) {
                        $mail->addAttachment($attach);
                    }
                } else {
                    $mail->addAttachment($attachment);
                }
            }

            if (!$mail->send()) {
                throw new Exception($mail->ErrorInfo);
                return FALSE;
            }
            return TRUE;
        } catch (Exception $e) {
            throw new \Exception($e->errorMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
