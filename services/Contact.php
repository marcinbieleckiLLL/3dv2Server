<?php

include_once '../config/Database.php';
include_once '../models/Contact.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");

$database = new Database();
$db = $database->getConnection();

$contact = new Contact($db);
$body = json_decode(file_get_contents('php://input'), true);

$contact->person_name = $body['person_name'];
$contact->company_name = $body['company_name'];
$contact->nip = $body['nip'];
$contact->email = $body['email'];
$contact->topic = $body['topic'];
$contact->message = $body['message'];
$contact->created = date('Y-m-d H:i:s');

$validateError = $contact->validate();

if($validateError == null){
    $contact->create();
    $contact_arr=array(
        "status" => true,
        "message" => "Poprawnie wysłano wiadomość",
        "field" => "main"
    );
}
else{
    $contact_arr=array(
        "status" => false,
        "message" => $validateError[1],
        "field" => $validateError[0]
    );
}
echo json_encode($contact_arr);