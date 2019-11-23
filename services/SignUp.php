<?php

include_once '../config/Database.php';
include_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$user->email = $_POST['email'];
$user->password = $_POST['password'];
$user->created = date('Y-m-d H:i:s');

if($user->signup()){
    $user_arr=array(
        "status" => true,
        "message" => "Zarejestrowano poprawnie",
        "token" => $user->token
    );
}
else{
    $user_arr=array(
        "status" => false,
        "message" => "Podany email już istnieje. Skontaktuj się z nami"
    );
}
echo json_encode($user_arr);
