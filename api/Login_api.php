<?php
require_once '../config/session_sec.php';
startSecSession();

require_once '../config/dbcon.php'; 
require_once '../config/user.php';    

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->email, $data->password)) {
    echo json_encode(["success" => false, "message" => "Email and password are required."]);
    exit();
}

$user->email = $data->email;
$user->password = $data->password;

$result = $user->login();

echo json_encode($result);

?>
