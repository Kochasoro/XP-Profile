<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include '../config/dbcon.php';
include '../config/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
session_start();
$loggedInUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$method = $_SERVER['REQUEST_METHOD'];

switch ($method){
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $user->getUserById($id);
            echo json_encode(["users" => $result]);
        } else {
            $result = $user->getAllUsers();
            echo json_encode(["users" => $result]);
        }
        break;

case 'PUT':
    $data = json_decode(file_get_contents("php://input"));

    if (
        !$data || empty($data->id) ||
        empty($data->firstname) ||
        empty($data->middlename) ||
        empty($data->lastname) ||
        empty($data->mobile)
    ) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing 'id', 'firstname', 'middlename', 'lastname', or 'mobile'."
        ]);
        exit;
    }

    $id = filter_var($data->id, FILTER_VALIDATE_INT);
    $firstname = htmlspecialchars(strip_tags(trim($data->firstname)), ENT_QUOTES, 'UTF-8');
    $middlename = htmlspecialchars(strip_tags(trim($data->middlename)), ENT_QUOTES, 'UTF-8');
    $lastname = htmlspecialchars(strip_tags(trim($data->lastname)), ENT_QUOTES, 'UTF-8');
    $mobile = htmlspecialchars(strip_tags(trim($data->mobile)), ENT_QUOTES, 'UTF-8');
    

    if ($id !== $loggedInUserId) {
        echo json_encode([
            "status" => "error",
            "message" => "Unauthorized access: you can only update your own details."
        ]);
        exit;
    }

    if ($user->updateUser($id, $firstname, $middlename, $lastname, $mobile)) {
        echo json_encode([
            "status" => "success",
            "message" => "User updated successfully!"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to update user."
        ]);
    }
    break;


    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if (!$data || empty($data->id)) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing 'id'."
            ]);
            exit;
        }
        $id = $data->id;

        if ($user->deleteUser($id)) {
            echo json_encode([
                "status" => "success",
                "message" => "User deleted successfully!"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to delete user."
            ]);
        }
        break;
    case 'PATCH':
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id) && isset($data->verified)) {
            $id = $data->id;
            $verified = $data->verified;

            if (!in_array($verified, ['Verified', 'Unverified'])) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Invalid status value. Must be 'Verified' or 'Unverified'."
                ]);
                exit;
            }

            if ($user->updateVerificationStatus($id, $verified)) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Verification status updated."
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Failed to update status."
                ]);
            }
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Missing 'id' or 'verified'."
            ]);
        }
        break;


    default:
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
