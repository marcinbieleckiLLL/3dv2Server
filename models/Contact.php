<?php

include_once '../models/EmailSender.php';

class Contact
{

    private $conn;
    private $table_name = "contact";
    private $emailSender;

    public $id;
    public $person_name;
    public $company_name;
    public $nip;
    public $email;
    public $topic;
    public $message;
    public $created;

    public function __construct($db){
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " SET person_name=:person_name, company_name=:company_name, nip=:nip, email=:email, topic=:topic, message=:message, created=:created";
        $stmt = $this->conn->prepare($query);

        $this->person_name=htmlspecialchars(strip_tags($this->person_name));
        $this->company_name=htmlspecialchars(strip_tags($this->company_name));
        $this->nip=htmlspecialchars(strip_tags($this->nip));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->topic=htmlspecialchars(strip_tags($this->topic));
        $this->message=htmlspecialchars(strip_tags($this->message));
        $this->created=htmlspecialchars(strip_tags($this->created));

        $stmt->bindParam(":person_name", $this->person_name);
        $stmt->bindParam(":company_name", $this->company_name);
        $stmt->bindParam(":nip", $this->nip);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":topic", $this->topic);
        $stmt->bindParam(":message", $this->message);
        $stmt->bindParam(":created", $this->created);

        if($stmt->execute()) {
            $this->emailSender->send(EmailComposer::createForContact($this));
        } else {
            print_r($stmt->errorInfo());
        }
    }

    function validate() {
        if (empty($this->email)) return array("email", "Mail nie może być pusty!");
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) return array("email", "Niepoprawny adres mailowy!");

        if (!empty($this->person_name) && strlen($this->person_name) > 255) return array("person_name", "Imię i nazwisko nie może być dłuższe niż 255 znaków!");
        if (!empty($this->company_name) && strlen($this->company_name) > 255) return array("company_name", "Nazwa frimy nie może być dłuższa niż 255 znaków!");
        if (!empty($this->nip) && strlen($this->nip) > 255) return array("nip", "Numer Nip nie może być dłuższy niż 255 znaków!");
        if (!empty($this->topic) && strlen($this->topic) > 255) return array("topic", "Temat nie może być dłuższy niż 255 znaków!");

        if (empty($this->message)) return array("message", "Wiadomość nie może być pusta!");
        if (strlen($this->message) < 5) return array("message", "Wiadomość nie może być krótsza niż 5 znaków!");
        if (strlen($this->message) > 8190) return array("message", "Wiadomość nie może być dłuższa niż 8190 znaków!");

        return null;
    }
}