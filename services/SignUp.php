<?php

include_once '../config/Database.php';
include_once '../models/User.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$body = json_decode(file_get_contents('php://input'), true);

$user->email = $body['email'];
$user->password = $body['password'];
$user->created = date('Y-m-d H:i:s');


$validationError = $user->validate();

if ($validationError == null) {
    if($user->signup()){
        $user_arr=array(
            "status" => true,
            "message" => "Zarejestrowano poprawnie",
            "field" => "main",
            "token" => $user->token
        );
    }
    else{
        $user_arr=array(
            "status" => false,
            "field" => "main",
            "message" => "Podany email już istnieje. Skontaktuj się z nami!"
        );
    }
} else {
    $user_arr=array(
        "status" => false,
        "message" => $validationError[1],
        "field" => $validationError[0]
    );
}
echo json_encode($user_arr);