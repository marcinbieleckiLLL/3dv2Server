<?php

include_once '../config/Database.php';
include_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$user->email = $_POST['email'];
$user->password = $_POST['password'];

$token = $user->login();
if ($token != null) {
    $user_arr = array(
        "status" => true,
        "message" => "Zalogowano poprawnie",
        "token" => $token
    );
} else {
    $user_arr = array(
        "status" => false,
        "message" => "Błędny login lub hasło",
    );
}

echo json_encode($user_arr);