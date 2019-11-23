<?php


class User
{
    private $conn;
    private $table_name = "users";

    public $id;
    public $email;
    public $password;
    public $created;
    public $token;

    public function __construct($db){
        $this->conn = $db;
    }

    function signup(){
        if($this->isAlreadyExist()){
            return false;
        }

        $query = "INSERT INTO" . $this->table_name . " SET email=:email, password=:password, created=:created";

        $stmt = $this->conn->prepare($query);

        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->password=htmlspecialchars(strip_tags($this->password));
        $this->created=htmlspecialchars(strip_tags($this->created));

        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":created", $this->created);

        if($stmt->execute()){
            $this->id = $this->conn->lastInsertId();
            $this->saveNewToken($this->id, $this->email);
            return true;
        }
        return false;
    }

    function saveNewToken($id, $email) {
        $query = "UPDATE " . $this->table_name . " SET token=:token";
        $stmt = $this->conn->prepare($query);
        $this->token = JwtToken::create($id, $email);
        $stmt->bindParam(":token", $this->token);
        $stmt->execute();
    }

    function login() {
        $query = "SELECT`id`, `username`, `password`, `created` FROM " . $this->table_name . " WHERE email='".$this->email."' AND password='".$this->password."'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->saveNewToken($row['id'], $row['email']);
            return $this->token;
        }
        return null;
    }

    function isAlreadyExist() {
        $query = "SELECT * FROM" . $this->table_name . " WHERE username='".$this->email."'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->rowCount() == 0;
    }
}