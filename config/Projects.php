<?php
class Projects {
    private $conn;
    private $table = "projects";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllProjects() {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table");
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }
        return [];
    }
    
    public function getProjectById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE id = ?");
        if ($stmt->execute([$id])) {
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        }
        return [];
    }
    
    public function addProject($portfolio_id, $project_name, $project_url, $description, $image_path = null) {
        $stmt = $this->conn->prepare("INSERT INTO $this->table (portfolio_id, project_name, project_url, description, image_url) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$portfolio_id, $project_name, $project_url, $description, $image_path]);
    }
    
    
    public function updateProject($id, $portfolio_id, $project_name, $project_url, $description, $image_url = null) {
        $oldImage = null;
        if ($image_url) {
            $stmt = $this->conn->prepare("SELECT image_url FROM $this->table WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && !empty($result['image_url'])) {
                $oldImage = $result['image_url'];
            }
        }
    
        $query = "UPDATE $this->table SET portfolio_id = ?, project_name = ?, project_url = ?, description = ?";
        if ($image_url) {
            $query .= ", image_url = ?";
        }
        $query .= " WHERE id = ?";
    
        $stmt = $this->conn->prepare($query);
    
        $success = $image_url
            ? $stmt->execute([$portfolio_id, $project_name, $project_url, $description, $image_url, $id])
            : $stmt->execute([$portfolio_id, $project_name, $project_url, $description, $id]);
    
            $fullImagePath = __DIR__ . "/../api/resources/images/projects/$oldImage";
            if ($success && $image_url && $oldImage && file_exists($fullImagePath)) {
                unlink($fullImagePath);
            }
            
    
        return $success;
    }
    
    

    public function deleteProject($id) {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
