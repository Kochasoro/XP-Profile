<?php
class User{
    private $conn;
    private $table_name = "users";

    public $id;
    public $firstname;
    public $middlename;
    public $lastname;
    public $email;
    public $mobile;
    public $password;
    public $role;
    public $status;

    public function __construct($db){
        $this->conn = $db;
    }

    public function create(){
        $query = "INSERT INTO " . $this->table_name . " (firstname, middlename, lastname, email, mobile, password, role, status) VALUES (:firstname, :middlename, :lastname, :email, :mobile, :password, :role, :status)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":firstname", $this->firstname);
        $stmt->bindParam(":middlename", $this->middlename);
        $stmt->bindParam(":lastname", $this->lastname);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":mobile", $this->mobile);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":status", $this->status);

        if($stmt->execute()){
            return ["success" => true, "message" => "User registered successfully. Please verify your account."];
        } else {
            return ["success" => false, "message" => "Unable to register user."];
        }
        
    }

    public function emailExists(){
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function login() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
    
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (password_verify($this->password, $row['password'])) {
                if ($row['status'] == 'Verified') {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['name'] = $row['firstname'];
                    $_SESSION['middlename'] = $row['middlename'];
                    $_SESSION['lastname'] = $row['lastname'];
                    $_SESSION['mobile'] = $row['mobile'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['role'] = $row['role'];
                    $_SESSION['login_time'] = time();
                    $_SESSION['expire_time'] = 300;
                    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    
                    return [
                        "success" => true,
                        "message" => "Login successful.",
                        "role" => $row['role']
                    ];
                } else {
                    http_response_code(403);
                    return [
                        "success" => false,
                        "message" => "Account not verified. Please verify your account."
                    ];
                }
            } else {
                http_response_code(401);
                return [
                    "success" => false,
                    "message" => "Invalid password."
                ];
            }
        } else {
            http_response_code(404);
            return [
                "success" => false,
                "message" => "User not found."
            ];
        }
    }
    public function updateVerificationStatus($id, $verified) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status 
                  WHERE id = :id";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $verified);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
        return $stmt->execute();
    }
    

    public function getAllUsers() {
        $query = "SELECT id, firstname, middlename, lastname, email, mobile, role, status FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $query = "SELECT id, firstname, middlename, lastname, email, mobile, role, status FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function updateUser($id, $firstname, $middlename, $lastname, $mobile) {
        $query = "UPDATE " . $this->table_name . " 
                  SET firstname = :firstname, middlename = :middlename, lastname = :lastname, mobile = :mobile 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':middlename', $middlename);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':mobile', $mobile);

        return $stmt->execute();
    }
    public function deleteUser($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
}

?>
