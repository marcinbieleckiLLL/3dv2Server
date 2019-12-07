<?php

class EmailSender
{
    public function __construct(){}

    function send(){
        $to      = 'marcinbielecki95@gmail.com';
	$subject = 'the subject';
	$message = 'hello';
	$headers = 'From: marcinbielecki95@gmail.com' . "\r\n" .
	    'Reply-To: marcinbielecki95@gmail.com' . "\r\n" .
	    'X-Mailer: PHP/' . phpversion();

	mail($to, $subject, $message, $headers);
    }
}
