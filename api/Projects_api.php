<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include '../config/dbcon.php';
include '../config/Projects.php';

$database = new Database();
$db = $database->getConnection();
$project = new Projects($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        echo isset($_GET['id']) 
            ? json_encode(["project" => $project->getProjectById($_GET['id'])])
            : json_encode(["projects" => $project->getAllProjects()]);
        break;

    case 'POST':
        session_start();

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(["status" => "error", "message" => "User not logged in"]);
            exit;
        }

        if (
            isset($_POST['portfolio_id'], $_POST['project_name'], $_POST['project_url'], $_POST['description'])
        ) {
            $portfolio_id = htmlspecialchars($_POST['portfolio_id'], ENT_QUOTES, 'UTF-8');
            $project_name = htmlspecialchars($_POST['project_name'], ENT_QUOTES, 'UTF-8');
            $project_url = htmlspecialchars($_POST['project_url'], ENT_QUOTES, 'UTF-8');
            $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');

            $image_path = null;

            if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['project_image']['tmp_name'];
                $file_name = basename($_FILES['project_image']['name']);

                $target_dir = "resources/images/projects/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }

                if (!is_writable($target_dir)) {
                    echo json_encode(["status" => "error", "message" => "Uploads folder is not writable"]);
                    exit;
                }

                $target_file = $target_dir . uniqid() . '_' . $file_name;

                if (move_uploaded_file($file_tmp, $target_file)) {
                    $image_path = $target_file;
                } else {
                    echo json_encode(["status" => "error", "message" => "Failed to move uploaded file"]);
                    exit;
                }
            }

            if ($project->addProject($portfolio_id, $project_name, $project_url, $description, $image_path)) {
                echo json_encode(["status" => "success", "message" => "Project added successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to add project"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id)) {
            if (!empty($data->image_base64)) {
                $imgData = $data->image_base64;
        
                if (preg_match('/^data:image\/(\w+);base64,/', $imgData, $type)) {
                    $imgData = substr($imgData, strpos($imgData, ',') + 1);
                    $type = strtolower($type[1]);
        
                    $imgData = base64_decode($imgData);
                    if ($imgData === false) {
                        echo json_encode(["status" => "error", "message" => "Base64 decode failed"]);
                        exit;
                    }
        
                    $target_dir = "resources/images/projects/";
                    if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
                    $filename = uniqid() . '.' . $type;
                    $filePath = $target_dir . $filename;
        
                    if (file_put_contents($filePath, $imgData)) {
                        $image_url = $filePath;
                    } else {
                        echo json_encode(["status" => "error", "message" => "Failed to save image"]);
                        exit;
                    }
                }
            } else {
                $image_url = $data->image_url ?? null;
            }
        
            $project->updateProject(
                htmlspecialchars($data->id, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data->portfolio_id, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data->project_name, ENT_QUOTES, 'UTF-8'),
                $data->project_url,
                htmlspecialchars($data->description, ENT_QUOTES, 'UTF-8'),
                $image_url
            );
        
            echo json_encode(["status" => "success", "message" => "Project updated successfully."]);
        }
        
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        echo !empty($data->id)
            ? ( $project->deleteProject($data->id)
                ? json_encode(["status" => "success", "message" => "Project deleted successfully."])
                : json_encode(["status" => "error", "message" => "Delete failed."]))
            : json_encode(["status" => "error", "message" => "Missing 'id'."]);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
