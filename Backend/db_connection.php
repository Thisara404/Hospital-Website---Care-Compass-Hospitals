<?php
class DatabaseConnection {
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'care_compass_hospitals';
    public $conn;

    public function __construct() {
        // Check if MySQLi extension is loaded
        if (!extension_loaded('mysqli')) {
            die("MySQLi extension is not loaded.");
        }

        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function closeConnection() {
        $this->conn->close();
    }
}
?>