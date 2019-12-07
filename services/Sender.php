<?php

include_once '../models/EmailSender.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");

$emailSender = new EmailSender();
$emailSender->send();
echo(1);
