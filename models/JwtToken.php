<?php

require "vendor/autoload.php";
use \Firebase\JWT\JWT;

class JwtToken {

    const issuer_claim = "MARCIN";
    const audience_claim = "MACIEK";
    const secret_key = "5f2b5cdbe5194f10b3241568fe4e2b24";

    static function create($id, $email) {
        $issuedat_claim = time();
        $notbefore_claim = $issuedat_claim - 10;
        $expire_claim = $issuedat_claim + 24 * 3600;

        $token = array(
            "iss" => JwtToken::issuer_claim,
            "aud" => JwtToken::audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "data" => array(
                "id" => $id,
                "email" => $email
        ));
        return JWT::encode($token, JwtToken::secret_key);
    }

    static function isCorrect($jwt) {
        try {
            $decoded = JWT::decode($jwt, JwtToken::$secret_key, array('HS256'));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
