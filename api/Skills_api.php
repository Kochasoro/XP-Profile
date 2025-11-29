<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include '../config/dbcon.php';
include '../config/Skills.php';

$database = new Database();
$db = $database->getConnection();
$skill = new Skills($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        echo isset($_GET['id']) 
            ? json_encode(["skill" => $skill->getSkillById($_GET['id'])])
            : json_encode(["skills" => $skill->getAllSkills()]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['portfolio_id'], $data['skill_name'], $data['proficiency'])) {
            $skill->addSkill(
                htmlspecialchars($data['portfolio_id'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['skill_name'], ENT_QUOTES, 'UTF-8'),
                intval($data['proficiency'])
            );
            echo json_encode(["status" => "success", "message" => "Skill added successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Missing required fields."]);
        }

        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id)) {
            $skill->updateSkill(
                $data->id,
                htmlspecialchars($data->portfolio_id, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data->skill_name, ENT_QUOTES, 'UTF-8'),
                intval($data->proficiency)
            );
            echo json_encode(["status" => "success", "message" => "Skill updated successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Missing 'id'."]);
        }

        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        echo !empty($data->id)
            ? ( $skill->deleteSkill($data->id)
                ? json_encode(["status" => "success", "message" => "Skill deleted successfully."])
                : json_encode(["status" => "error", "message" => "Delete failed."]))
            : json_encode(["status" => "error", "message" => "Missing 'id'."]);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
