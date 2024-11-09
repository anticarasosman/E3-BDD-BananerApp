<?php
class Database {
    private $host = 'localhost';
    private $dbname = 'your_database';
    private $user = 'your_user';
    private $password = 'your_password';
    private $conn;

    public function connect() {
        try {
            $this->conn = new PDO("pgsql:host=$this->host;dbname=$this->dbname", $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();
            exit;
        }
    }
}
?>
