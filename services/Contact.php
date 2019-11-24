<?php

include_once '../config/Database.php';
include_once '../models/Contact.php';

$database = new Database();
$db = $database->getConnection();

$contact = new Contact($db);

$contact->person_name = $_POST['person_name'];
$contact->company_name = $_POST['company_name'];
$contact->nip = $_POST['nip'];
$contact->email = $_POST['email'];
$contact->topic = $_POST['topic'];
$contact->message = $_POST['message'];
$contact->created = date('Y-m-d H:i:s');

$validateError = $contact->validate();

if($validateError == null){
    $contact->create();
    $contact_arr=array(
        "status" => true,
        "message" => "Poprawnie wysłano wiadomość"
    );
}
else{
    $contact_arr=array(
        "status" => false,
        "message" => $validateError
    );
}
echo json_encode($contact_arr);