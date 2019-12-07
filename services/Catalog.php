<?php

include_once '../models/JwtToken.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");

$body = json_decode(file_get_contents('php://input'), true);
$token = $body['token'];

header("Content-type:application/pdf");
header("Content-Disposition:attachment;filename='downloaded.pdf'");

if (JwtToken::isCorrect($token)) {
    readfile("../catalog.pdf");
}