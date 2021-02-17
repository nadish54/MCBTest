<?php
    class Database{
        private string $server = "mysql:host=localhost;dbname=mcb";
        private string $username = "root";
        private string $password = '';
        protected $conn;

        public function open(): PDO{
            try {
                $this->conn = new PDO($this->server, $this->username, $this->password);
                return $this->conn;
            } catch (PDOException $e) {
                echo "There is some problem in connection: " . $e->getMessage();
            }
        }

        public function close()
        {
            $this->conn = null;
        }
    }

    $pdo = new Database();
?>
