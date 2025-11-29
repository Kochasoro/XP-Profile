<?php
class Portfolio {
    private $conn;
    private $table = "portfolio";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllPortfolios() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $portfolios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $portfolios;
    }
    public function getPortfolioById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function addPortfolio($title, $summary, $user_id, $profile_image = null) {
        $query = "INSERT INTO portfolio (user_id, title, summary, profile_image) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$user_id, $title, $summary, $profile_image]);
    }
    
    
    


    public function updatePortfolio($id, $title, $summary, $newImagePath = null) {
        $finalImagePath = $newImagePath ?: null;
    
        $query = "UPDATE " . $this->table . " SET title = ?, summary = ?, profile_image = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([$title, $summary, $finalImagePath, $id]);
    }
    


    public function deletePortfolio($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>
