<?php
class Experience {
    private $conn;
    private $table = "experience";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllExperience() {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table");
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }
        return [];
    }
    
    public function getExperienceById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE id = ?");
        if ($stmt->execute([$id])) {
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        }
        return [];
    }
    

    public function addExperience($portfolio_id, $job_title, $company_name, $start_date, $end_date, $description) {
        $stmt = $this->conn->prepare("INSERT INTO $this->table (portfolio_id, job_title, company_name, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$portfolio_id, $job_title, $company_name, $start_date, $end_date, $description]);
    }

    public function updateExperience($id, $portfolio_id, $job_title, $company_name, $start_date, $end_date, $description) {
        $stmt = $this->conn->prepare("UPDATE $this->table SET portfolio_id = ?, job_title = ?, company_name = ?, start_date = ?, end_date = ?, description = ? WHERE id = ?");
        return $stmt->execute([$portfolio_id, $job_title, $company_name, $start_date, $end_date, $description, $id]);
    }

    public function deleteExperience($id) {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
