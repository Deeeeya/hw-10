<?php
    class Product {
        private $conn;
        private $table_name = "products";
        
        public $id;
        public $name;
        public $description;
        public $price;
        public $created;
        
        public function __construct($db) {
            $this->conn = $db;
        }
        
        public function read($search = '') {
            $query = "SELECT * FROM " . $this->table_name;
            
            if(!empty($search)) {
                $query .= " WHERE name LIKE :search OR description LIKE :search";
            }
            
            $stmt = $this->conn->prepare($query);
            
            if(!empty($search)) {
                $searchValue = "%{$search}%";
                $stmt->bindParam(':search', $searchValue);
            }
            
            $stmt->execute();
            return $stmt;
        }
        
        public function create() {
            $query = "INSERT INTO " . $this->table_name . " 
                    SET name=:name, description=:description, price=:price, created=:created";
            
            $stmt = $this->conn->prepare($query);
            
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->description = htmlspecialchars(strip_tags($this->description));
            $this->price = htmlspecialchars(strip_tags($this->price));
            $this->created = date('Y-m-d H:i:s');
            
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":description", $this->description);
            $stmt->bindParam(":price", $this->price);
            $stmt->bindParam(":created", $this->created);
            
            if($stmt->execute()) {
                return true;
            }
            return false;
        }
        
        public function update() {
            $query = "UPDATE " . $this->table_name . " 
                    SET name=:name, description=:description, price=:price 
                    WHERE id=:id";
            
            $stmt = $this->conn->prepare($query);
            
            $this->id = htmlspecialchars(strip_tags($this->id));
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->description = htmlspecialchars(strip_tags($this->description));
            $this->price = htmlspecialchars(strip_tags($this->price));
            
            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":description", $this->description);
            $stmt->bindParam(":price", $this->price);
            
            if($stmt->execute()) {
                return true;
            }
            return false;
        }
        
        public function delete() {
            $query = "DELETE FROM " . $this->table_name . " WHERE id=:id";
            
            $stmt = $this->conn->prepare($query);
            
            $this->id = htmlspecialchars(strip_tags($this->id));
            
            $stmt->bindParam(":id", $this->id);
            
            if($stmt->execute()) {
                return true;
            }
            return false;
        }
        
        public function readOne() {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id=:id LIMIT 0,1";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":id", $this->id);
            
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row) {
                $this->name = $row['name'];
                $this->description = $row['description'];
                $this->price = $row['price'];
                $this->created = $row['created'];
                return true;
            }
            return false;
        }
    }
?>