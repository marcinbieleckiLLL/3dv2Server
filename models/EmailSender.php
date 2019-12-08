<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

class EmailSender
{
    public $mail;

    public function __construct(){
        $this->initSender();
    }

    function initSender() {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host       = 'smtp.gmail.com';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = 'marcinbielecki95@gmail.com';
        $this->mail->Password   = 'xxx';
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = 587;
        $this->mail->isHTML(true);
    }

    function send($emailContentWrapper) {
        try {
            $this->mail->setFrom('marcinbielecki95@gmail.com', 'BIG S.C.');
            $this->mail->addAddress($emailContentWrapper->recipient);

            $this->mail->Subject = $emailContentWrapper->subject;
            $this->mail->Body    = $emailContentWrapper->body;
            $this->mail->AltBody = $emailContentWrapper->altBody;

            $this->mail->send();
        } catch (Exception $e) {
            print_r($this->mail->ErrorInfo);
        }
    }
}

class EmailContentWrapper {

    public $recipient;
    public $subject;
    public $body;
    public $altBody;

    /**
     * EmailContentWrapper constructor.
     * @param $recipient
     * @param $subject
     * @param $body
     * @param $altBody
     */
    public function __construct($recipient, $subject, $body, $altBody = null)
    {
        $this->recipient = $recipient;
        $this->subject = $subject;
        $this->body = $body;
        $this->altBody = ($altBody != null) ? $altBody : $body;
    }
}

class EmailComposer {

    public static $innerEmail = 'marcinbielecki95@gmail.com';

    static function createForRegistration($user) {
        $subject = 'Witaj '.$user->company_name;
        return new EmailContentWrapper($user->email, $subject,
            'Dzi&#281;kujemy za rejestracje w naszym serwisie. <br> Twoje konto b&#281;dzie aktywne po przeprowadzeniu weryfikacji przez naszego pracownika (max 24h).',
            'Dzi&#281;kujemy za rejestracje w naszym serwisie. Twoje konto b&#281;dzie aktywne po przeprowadzeniu weryfikacji przez naszego pracownika (max 24h).');
    }

    static function createForRegistrationInnerMessage($user) {
        $subject = 'Prosba o weryfikacje od '.$user->company_name;
        $body = 'Nazwa firmy: '.$user->company_name.'<br> Email: '.$user->email.'<br> Nip: '.$user->nip;
        $altBody = 'Nazwa firmy: '.$user->company_name.', Email: '.$user->email.', Nip: '.$user->nip;
        return new EmailContentWrapper(EmailComposer::$innerEmail, $subject, $body, $altBody);
    }

    static function createForContact($contact) {
        $subject = 'Prosba o kontakt od '.$contact->person_name;
        $body = 'Imie i nazwisko: '.$contact->person_name.'<br> Nazwa firmy: '.$contact->company_name.'<br> Email: '.$contact->email.'<br> Nip: '.$contact->nip.'<br> Tytul: '.$contact->topic.'<br> Wiadomosc: '.$contact->message;
        $altBody = 'Imie i nazwisko: '.$contact->person_name.', Nazwa firmy: '.$contact->company_name.', Email: '.$contact->email.', Nip: '.$contact->nip.', Tytul: '.$contact->topic.', Wiadomosc: '.$contact->message;

        return new EmailContentWrapper(EmailComposer::$innerEmail, $subject, $body, $altBody);
    }
}