<?php
/**
 * Database Configuration & Connection Handler
 * Manages all database connections for the Food Hub application
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'jsc_food_hub';
    private $user = 'root';
    private $password = '';
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name,
                $this->user,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Connection Error: ' . $e->getMessage()]);
            die();
        }

        return $this->conn;
    }
}
?>