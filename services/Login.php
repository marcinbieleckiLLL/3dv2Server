<?php

include_once '../config/Database.php';
include_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$user->email = isset($_GET['username']) ? $_GET['username'] : die();
$user->password = isset($_GET['password']) ? $_GET['password'] : die();

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