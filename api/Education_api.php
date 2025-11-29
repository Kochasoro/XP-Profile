<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include '../config/dbcon.php';
include '../config/Education.php';

$database = new Database();
$db = $database->getConnection();
$education = new Education($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $education->getEducationById($id);
            echo json_encode(["education" => $result]);
        } else {
            $result = $education->getAllEducation();
            echo json_encode(["education" => $result]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (
            isset($data['portfolio_id'], $data['school_name'], $data['degree'], 
                  $data['start_year'], $data['end_year'], $data['description'])
        ) {
            $result = $education->addEducation(
                htmlspecialchars($data['portfolio_id'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['school_name'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['degree'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['start_year']),
                htmlspecialchars($data['end_year']),
                htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8')
            );
            echo json_encode(["status" => "success", "message" => "Education record added successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Missing required fields."]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (
            !empty($data->id) && !empty($data->portfolio_id) && !empty($data->school_name) &&
            !empty($data->degree) && !empty($data->start_year) && !empty($data->end_year) &&
            !empty($data->description)
        ) {
            $result = $education->updateEducation(
                htmlspecialchars($data->id, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data->portfolio_id, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data->school_name, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data->degree, ENT_QUOTES, 'UTF-8'),
                $data->start_year,
                $data->end_year,
                htmlspecialchars($data->description, ENT_QUOTES, 'UTF-8')
            );
            echo json_encode(["status" => "success", "message" => "Education updated successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Missing data."]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id)) {
            $result = $education->deleteEducation($data->id);
            echo json_encode(["status" => "success", "message" => "Education deleted successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Missing 'id'."]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
        break;
}
?>
