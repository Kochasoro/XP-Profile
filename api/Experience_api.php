<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include '../config/dbcon.php';
include '../config/Experience.php';

$database = new Database();
$db = $database->getConnection();
$exp = new Experience($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        echo isset($_GET['id']) 
            ? json_encode(["experience" => $exp->getExperienceById($_GET['id'])])
            : json_encode(["experiences" => $exp->getAllExperience()]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['portfolio_id'], $data['job_title'], $data['company_name'], $data['start_date'], $data['end_date'], $data['description'])) {
            $exp->addExperience(
                htmlspecialchars($data['portfolio_id'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['job_title'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['company_name'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['start_date']),
                htmlspecialchars($data['end_date']),
                htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8')
            );
            echo json_encode(["status" => "success", "message" => "Experience added successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Missing required fields."]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id)) {
            $exp->updateExperience(
                htmlspecialchars($data->id, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data->portfolio_id, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data->job_title, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data->company_name, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data->start_date),
                htmlspecialchars($data->end_date),
                htmlspecialchars($data->description, ENT_QUOTES, 'UTF-8')
            );
            echo json_encode(["status" => "success", "message" => "Experience updated successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Missing 'id'."]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        echo !empty($data->id)
            ? ( $exp->deleteExperience($data->id)
                ? json_encode(["status" => "success", "message" => "Experience deleted successfully."])
                : json_encode(["status" => "error", "message" => "Delete failed."]))
            : json_encode(["status" => "error", "message" => "Missing 'id'."]);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
