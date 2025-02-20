<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'care_compass_hospitals';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

class DatabaseConnection
{
    private $host = 'localhost';
    private $username = 'root';  // Change if different
    private $password = '';      // Change if different
    private $database = 'care_compass_hospitals';
    public $conn;

    public function __construct()
    {
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);

            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function closeConnection()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
