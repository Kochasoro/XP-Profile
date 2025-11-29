<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include '../config/dbcon.php';
include '../config/Employee.php';

$database = new Database();
$db = $database->getConnection();
$employee = new Employee($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $employee->getEmployeeById($id);
            echo json_encode(["employee" => $result ?: null]);
        } else {
            $result = $employee->getAllEmployees();
            echo json_encode(["employees" => $result]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['first_name'], $data['last_name'], $data['email'], $data['department'], $data['salary'])) {
            $success = $employee->addEmployee(
                htmlspecialchars($data['first_name'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['last_name'], ENT_QUOTES, 'UTF-8'),
                filter_var($data['email'], FILTER_SANITIZE_EMAIL),
                htmlspecialchars($data['department'], ENT_QUOTES, 'UTF-8'),
                $data['salary']
            );

            echo json_encode([
                "status" => $success ? "success" : "error",
                "message" => $success ? "Employee added successfully" : "Failed to add employee"
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid input"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id, $data->first_name, $data->last_name, $data->email, $data->department, $data->salary)) {
            echo json_encode(["status" => "error", "message" => "Missing fields"]);
            exit;
        }

        $success = $employee->updateEmployee(
            $data->id,
            htmlspecialchars($data->first_name, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($data->last_name, ENT_QUOTES, 'UTF-8'),
            filter_var($data->email, FILTER_SANITIZE_EMAIL),
            htmlspecialchars($data->department, ENT_QUOTES, 'UTF-8'),
            $data->salary
        );
        echo json_encode([
            "status" => $success ? "success" : "error",
            "message" => $success ? "Employee updated successfully" : "Failed to update employee"
        ]);
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id)) {
            echo json_encode(["status" => "error", "message" => "Missing 'id'"]);
            exit;
        }

        $success = $employee->deleteEmployee($data->id);
        echo json_encode([
            "status" => $success ? "success" : "error",
            "message" => $success ? "Employee deleted successfully" : "Failed to delete employee"
        ]);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
