<?php
class Database {
    // --- IMPORTANT ---
    // Please update these database credentials with your own details.
    private $host = "127.0.0.1"; // or "localhost"
    private $db_name = "dorm_management_system";
    private $username = "root"; // your db username
    private $password = ""; // your db password
    // --- --- --- ---

    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
