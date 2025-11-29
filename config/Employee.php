<?php
class Employee {
    private $conn;
    private $table = "employees";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllEmployees() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmployeeById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addEmployee($first_name, $last_name, $email, $department, $salary) {
        $query = "INSERT INTO " . $this->table . " (first_name, last_name, email, department, salary) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$first_name, $last_name, $email, $department, $salary]);
    }

    public function updateEmployee($id, $first_name, $last_name, $email, $department, $salary) {
        $query = "UPDATE " . $this->table . " SET first_name = ?, last_name = ?, email = ?, department = ?, salary = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$first_name, $last_name, $email, $department, $salary, $id]);
    }

    public function deleteEmployee($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>
