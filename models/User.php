<?php

include_once '../models/JwtToken.php';
include_once '../models/EmailSender.php';

class User
{
    private $conn;
    private $emailSender;
    private $table_name = "users";

    public $id;
    public $company_name;
    public $nip;
    public $email;
    public $password;
    public $created;
    public $token;

    public function __construct($db){
        $this->conn = $db;
        $this->emailSender = new EmailSender();
    }

    function signup(){
        if($this->isAlreadyExist()){
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " SET email=:email, password=:password, nip=:nip, company_name=:company_name, created=:created";

        $stmt = $this->conn->prepare($query);

        $this->company_name=htmlspecialchars(strip_tags($this->company_name));
        $this->nip=htmlspecialchars(strip_tags($this->nip));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->password=htmlspecialchars(strip_tags($this->password));
        $this->created=htmlspecialchars(strip_tags($this->created));

        $stmt->bindParam(":company_name", $this->company_name);
        $stmt->bindParam(":nip", $this->nip);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", password_hash($this->password, PASSWORD_DEFAULT));
        $stmt->bindParam(":created", $this->created);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            $this->saveNewToken($this->id, $this->email);
            $this->emailSender->send(EmailComposer::createForRegistration($this));
            $this->emailSender->send(EmailComposer::createForRegistrationInnerMessage($this));
            return true;
        } else {
            print_r($stmt->errorInfo());
        }
        return false;
    }


    function validate() {
        if (empty($this->company_name)) return array("company_name", "Nazwa frimy nie może być pusta!");
        if (strlen($this->company_name) < 5) return array("company_name", "Nazwa firmy nie może być krótsza niż 5 znaków!");
        if (strlen($this->company_name) > 255) return array("company_name", "Nazwa frimy nie może być dłuższa niż 255 znaków!");

        if (empty($this->nip)) return array("nip", "Numer Nip nie może być pusty!");
        if (strlen($this->nip) < 5) return array("nip", "Numer Nip nie może być krótszy niż 5 znaków!");
        if (strlen($this->nip) > 255) return array("nip", "Numer Nip nie może być dłuższy niż 255 znaków!");

        if (empty($this->email)) return array("email", "Mail nie może być pusty!");
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) return array("email", "Niepoprawny adres mailowy!");

        if (empty($this->password)) return array("password", "Hasło nie może być puste!");
        if (strlen($this->password) < 5) return array("password", "Hasło nie może być krótsze niż 5 znaków!");
        if (strlen($this->password) > 8190) return array("password", "Hasło nie może być dłuższe niż 255 znaków!");

        return null;
    }

    function saveNewToken($id, $email) {
        $query = "UPDATE " . $this->table_name . " SET token=:token where id=:id";
        $stmt = $this->conn->prepare($query);
        $this->token = JwtToken::create($id, $email);
        $stmt->bindParam(":token", $this->token);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
    }

    function login() {
        $query = "SELECT `id`, `email`, `password`, `created` FROM " . $this->table_name . " WHERE is_enabled=1 AND email='".$this->email."'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($this->password, $row['password'])){
                $this->saveNewToken($row['id'], $row['email']);
                return array(true, $this->token);
            }
        } else {
            $query = "SELECT `id`, `email`, `password`, `created` FROM " . $this->table_name . " WHERE email='".$this->email."'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            if ($stmt->rowCount() > 0) return array(false, "Użytkownik nie został jeszcze zweryfikowany. <br> Weryfikacja odbędzie się w ciągu 24 godzin!");
        }
        return array(false, "Błędny login lub hasło!");
    }

    function isAlreadyExist() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email='".$this->email."'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        if($stmt->rowCount() != 0) return true;

        $query = "SELECT * FROM " . $this->table_name . " WHERE nip='".$this->nip."'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        if($stmt->rowCount() != 0) return true;

        return false;
    }
}