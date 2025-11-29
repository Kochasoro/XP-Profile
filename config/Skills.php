<?php
class Skills {
    private $conn;
    private $table = "skills";

    public function __construct($db) {
        $this->conn = $db;
    }
    public function getAllSkills() {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table");
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }
        return [];
    }
    
    public function getSkillById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE id = ?");
        if ($stmt->execute([$id])) {
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        }
        return [];
    }
    
    public function addSkill($portfolio_id, $skill_name, $proficiency) {
        $stmt = $this->conn->prepare("INSERT INTO $this->table (portfolio_id, skill_name, proficiency) VALUES (?, ?, ?)");
        return $stmt->execute([$portfolio_id, $skill_name, $proficiency]);
    }
    
    public function updateSkill($id, $portfolio_id, $skill_name, $proficiency) {
        $stmt = $this->conn->prepare("UPDATE $this->table SET portfolio_id = ?, skill_name = ?, proficiency = ? WHERE id = ?");
        return $stmt->execute([$portfolio_id, $skill_name, $proficiency, $id]);
    }
    
    public function deleteSkill($id) {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
