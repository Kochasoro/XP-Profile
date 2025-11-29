<?php
    class Subject{
        private $conn;
        private $table = "subjects";

        public function __construct($db){
            $this->conn = $db;
        }
        public function getAllSubjects(){
            $query = "SELECT * FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        public function getSubjectById($id){
            $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        public function addSubject($code, $description){
            $query = "INSERT INTO " . $this->table . " (code, description) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$code, $description]);
        }

        public function updateSubject($id, $code, $description){
            $query = "UPDATE " . $this->table . " SET code = ?, description = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$code, $description, $id]);
        }

        public function deleteSubject($id){
            $query = "DELETE FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$id]);
        }
    }

?>