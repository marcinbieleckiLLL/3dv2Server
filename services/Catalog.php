<?php

$token = isset($_GET['token']) ? $_GET['token'] : die();

if (JwtToken::isCorrect($token)) {
    header("Content-type:application/pdf");
    header("Content-Disposition:attachment;filename='downloaded.pdf'");
    readfile("../catalog.pdf");
} else {
    $arr = array(
        "status" => false,
        "message" => "Błędny login lub hasło",
    );
    echo json_encode($arr);
}