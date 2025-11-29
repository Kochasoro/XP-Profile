<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include '../config/dbcon.php';
include '../config/Portfolio.php';
$method = $_SERVER['REQUEST_METHOD'];

$database = new Database();
$db = $database->getConnection();
$portfolio = new Portfolio($db);

switch ($method) {
    case 'GET':
        session_start();
        $loggedInUserId = $_SESSION['user_id'] ?? null;
    
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $portfolioData = $portfolio->getPortfolioById($id);
    
            if ($portfolioData) {
                $portfolioData['title'] = html_entity_decode($portfolioData['title'], ENT_QUOTES, 'UTF-8');
                $portfolioData['summary'] = html_entity_decode($portfolioData['summary'], ENT_QUOTES, 'UTF-8');
    
                echo json_encode([
                    'user_id' => $loggedInUserId,
                    'portfolio' => $portfolioData
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Portfolio not found'
                ]);
            }
        } else {
            $portfolios = $portfolio->getAllPortfolios();
    
            foreach ($portfolios as &$item) {
                $item['title'] = html_entity_decode($item['title'], ENT_QUOTES, 'UTF-8');
                $item['summary'] = html_entity_decode($item['summary'], ENT_QUOTES, 'UTF-8');
            }
    
            echo json_encode([
                'user_id' => $loggedInUserId,
                'portfolios' => $portfolios
            ]);
        }
        break;
    
    
    case 'POST':
        session_start();

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(["status" => "error", "message" => "User not logged in"]);
            exit;
        }
        $data = $_POST;
        if (isset($data['title']) && isset($data['summary'])) {
            $title = htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8');
            $summary = htmlspecialchars($data['summary'], ENT_QUOTES, 'UTF-8');
            $user_id = $_SESSION['user_id'];

            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['profile_image']['tmp_name'];
                $file_name = basename($_FILES['profile_image']['name']);

                $target_dir = "resources/images/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                
                if (!is_writable($target_dir)) {
                    echo json_encode(["status" => "error", "message" => "Uploads folder is not writable"]);
                    exit;
                }
                $target_file = $target_dir . $file_name;

                if (move_uploaded_file($file_tmp, $target_file)) {
                    $profile_image = $target_file;
                } else {
                    echo json_encode(["status" => "error", "message" => "Failed to move uploaded file"]);
                    exit;
                }
            } else {
                $profile_image = null;
            }

            if ($portfolio->addPortfolio($title, $summary, $user_id, $profile_image)) {
                echo json_encode(["status" => "success", "message" => "Portfolio added successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to add portfolio"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid input"]);
        }
        break;

    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['id'], $input['title'], $input['summary'])) {
            echo json_encode(["status" => "error", "message" => "Missing required fields."]);
            exit;
        }

        $id = htmlspecialchars($input['id'], ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars($input['title'], ENT_QUOTES, 'UTF-8');
        $summary = htmlspecialchars($input['summary'], ENT_QUOTES, 'UTF-8');
        $profile_image_base64 = $input['profile_image'] ?? null;

        $existing = $portfolio->getPortfolioById($id);
        $old_image_path = $existing ? $existing['profile_image'] : null;

        if ($profile_image_base64) {
            if (preg_match('/^data:image\/(\w+);base64,/', $profile_image_base64, $type)) {
                $image_ext = strtolower($type[1]); 
                $base64_str = substr($profile_image_base64, strpos($profile_image_base64, ',') + 1);
                $base64_str = base64_decode($base64_str);

                $file_name = uniqid("profile_") . "." . $image_ext;
                $profile_image_path = "resources/images/" . $file_name;

                if (file_put_contents($profile_image_path, $base64_str)) {
                    if ($old_image_path && file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                } else {
                    echo json_encode(["status" => "error", "message" => "Failed to save the image."]);
                    exit;
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid image format"]);
                exit;
            }
        } else {
            if ($old_image_path && file_exists($old_image_path)) {
                unlink($old_image_path); 
            }
            $profile_image_path = null; 
        }

        if ($portfolio->updatePortfolio($id, $title, $summary, $profile_image_path)) {
            echo json_encode(["status" => "success", "message" => "Portfolio updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update portfolio"]);
        }
        break;





    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id)) {
            $portfolio = new Portfolio($db);
            if ($portfolio->deletePortfolio($data->id)) {
                echo json_encode(["status" => "success", "message" => "Portfolio deleted successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to delete portfolio"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid input"]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
        break;
}
?>