<?php
    $env = parse_ini_file('../.env');

    define('DBNAME', $env['DBNAME']);
    define('DBHOST', $env['DBHOST']);
    define('DBUSER', $env['DBUSER']);
    define('DBPASS', $env['DBPASS']);

    class Database {
        private $host;
        private $db_name;
        private $username;
        private $password;
        public $conn;
        
        public function __construct() {
            $this->host = DBHOST;
            $this->db_name = DBNAME;
            $this->username = DBUSER;
            $this->password = DBPASS;
        }
        
        public function getConnection() {
            $this->conn = null;
            try {
                $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                echo "Connection error: " . $e->getMessage();
            }
            return $this->conn;
        }
    }
?>