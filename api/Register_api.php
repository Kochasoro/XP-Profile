<?php
    session_start();
    
    header("Content-Type: application/json");
    include_once '../config/dbcon.php';
    include_once '../config/User.php';

    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);

    $data = json_decode(file_get_contents("php://input"), true);

    $user->firstname = htmlspecialchars(strip_tags(trim($data['firstname'])), ENT_QUOTES, 'UTF-8');
    $user->middlename = htmlspecialchars(strip_tags(trim($data['middlename'])), ENT_QUOTES, 'UTF-8');
    $user->lastname = htmlspecialchars(strip_tags(trim($data['lastname'])), ENT_QUOTES, 'UTF-8');
    $user->email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
    $user->mobile = preg_replace('/\D/', '', trim($data['mobile']));
    $user->password = trim($data['password']);
    if ($user->password !== trim($data['confirm_password'])) {
        http_response_code(400); 
        echo json_encode(["message" => "Passwords do not match.", "success" => false]);
        exit();
    }
    
    $user->role = "user";
    $user->status = "unverified";

    $user->password = password_hash($user->password, PASSWORD_BCRYPT);
    
    if ($user->emailExists()){
        http_response_code(409); 
        echo json_encode(["message" => "Email already exists.", "success" => false]);
        exit();
    }

    if($user->create()){
        http_response_code(201); 
        echo json_encode(["message" => "User registered successfully. Please verify your account.", "success" => true]);
    } else {
        http_response_code(500); 
        echo json_encode(["message" => "Unable to register user.", "success" => false]);
    }
    
?>