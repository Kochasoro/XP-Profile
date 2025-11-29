<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include '../config/dbcon.php';
include '../config/Subject.php';

$database = new Database();
$db = $database->getConnection();
$subject = new Subject($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method){
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $subject->getSubjectById($id);
            echo json_encode(["subjects" => $result]);
        } else {
            $result = $subject->getAllSubjects();
            echo json_encode(["subjects" => $result]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data['code']) && isset($data['description'])) {
            $code = htmlspecialchars($data['code'], ENT_QUOTES, 'UTF-8');
            $description = htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8');

            $result = $subject->addSubject($code, $description);
            echo json_encode(["status" => "success", "message" => "Subject added successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid input"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!$data || empty($data->id) || empty($data->code) || empty($data->description)) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing 'id', 'code', or 'description'."
            ]);
            exit;
        }
        $id = $data->id;
        $code = $data->code;
        $description = $data->description;

        if ($subject->updateSubject($id, $code, $description)) {
            echo json_encode([
                "status" => "success",
                "message" => "Subject updated successfully!"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to update subject."
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

        if ($subject->deleteSubject($id)) {
            echo json_encode([
                "status" => "success",
                "message" => "Subject deleted successfully!"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to delete subject."
            ]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
