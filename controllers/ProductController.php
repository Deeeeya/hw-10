<?php
    class ProductController {
        private $product;
        
        public function __construct($db) {
            require_once '../models/Product.php';
            $this->product = new Product($db);
        }
        
        public function getAll() {
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            $stmt = $this->product->read($search);
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $products_arr = array();
                $products_arr["records"] = array();
                
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    
                    $product_item = array(
                        "id" => $id,
                        "name" => $name,
                        "description" => $description,
                        "price" => $price,
                        "created" => $created
                    );
                    
                    array_push($products_arr["records"], $product_item);
                }
                
                http_response_code(200);
                echo json_encode($products_arr);
            } else {
                http_response_code(200);
                echo json_encode(array("records" => array(), "message" => "No products found."));
            }
        }
        
        public function getOne() {
            $data = json_decode(file_get_contents("php://input"));
            
            if(!empty($data->id)) {
                $this->product->id = $data->id;
                
                if($this->product->readOne()) {
                    $product_arr = array(
                        "id" => $this->product->id,
                        "name" => $this->product->name,
                        "description" => $this->product->description,
                        "price" => $this->product->price,
                        "created" => $this->product->created
                    );
                    
                    http_response_code(200);
                    echo json_encode($product_arr);
                } else {
                    http_response_code(404);
                    echo json_encode(array("message" => "Product not found."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Missing required parameter 'id'."));
            }
        }
        
        public function create() {
            $data = json_decode(file_get_contents("php://input"));
            
            if(!empty($data->name) && !empty($data->description) && !empty($data->price)) {
                $this->product->name = $data->name;
                $this->product->description = $data->description;
                $this->product->price = $data->price;
                
                if($this->product->create()) {
                    http_response_code(201);
                    echo json_encode(array("message" => "Product was created."));
                } else {
                    http_response_code(503);
                    echo json_encode(array("message" => "Unable to create product."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Unable to create product. Data is incomplete."));
            }
        }
        
        public function update() {
            $data = json_decode(file_get_contents("php://input"));
            
            if(!empty($data->id) && !empty($data->name) && !empty($data->description) && !empty($data->price)) {
                $this->product->id = $data->id;
                $this->product->name = $data->name;
                $this->product->description = $data->description;
                $this->product->price = $data->price;
                
                if($this->product->update()) {
                    http_response_code(200);
                    echo json_encode(array("message" => "Product was updated."));
                } else {
                    http_response_code(503);
                    echo json_encode(array("message" => "Unable to update product."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Unable to update product. Data is incomplete."));
            }
        }
        
        public function delete() {
            $data = json_decode(file_get_contents("php://input"));
            
            if(!empty($data->id)) {
                $this->product->id = $data->id;
                
                if($this->product->delete()) {
                    http_response_code(200);
                    echo json_encode(array("message" => "Product was deleted."));
                } else {
                    http_response_code(503);
                    echo json_encode(array("message" => "Unable to delete product."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Unable to delete product. Please provide an ID."));
            }
        }
    }
?>