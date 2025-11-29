<?php
class Education {
    private $conn;
    private $table = "education";

    public function __construct($db){
        $this->conn = $db;
    }

    public function getAllEducation() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }
        return [];
    }
    
    public function getEducationById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute([$id])) {
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        }
        return [];
    }
    

    public function addEducation($portfolio_id, $school_name, $degree, $start_year, $end_year, $description) {
        $query = "INSERT INTO " . $this->table . " (portfolio_id, school_name, degree, start_year, end_year, description) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$portfolio_id, $school_name, $degree, $start_year, $end_year, $description]);
    }

    public function updateEducation($id, $portfolio_id, $school_name, $degree, $start_year, $end_year, $description) {
        $query = "UPDATE " . $this->table . " 
                  SET portfolio_id = ?, school_name = ?, degree = ?, start_year = ?, end_year = ?, description = ? 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$portfolio_id, $school_name, $degree, $start_year, $end_year, $description, $id]);
    }

    public function deleteEducation($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>
