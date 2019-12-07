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

$response = $user->login();
if ($response[0]) {
    $user_arr = array(
        "status" => true,
        "message" => "Zalogowano poprawnie",
        "field" => "main",
        "token" => $response[1]
    );
} else {
    $user_arr = array(
        "status" => false,
        "field" => "main",
        "message" => $response[1],
    );
}

echo json_encode($user_arr);