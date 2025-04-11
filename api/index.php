<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require_once '../config/database.php';
    require_once '../controllers/ProductController.php';

    $database = new Database();
    $db = $database->getConnection();

    $productController = new ProductController($db);

    $request_method = $_SERVER["REQUEST_METHOD"];
    $endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';

    switch($request_method) {
        case 'GET':
            if($endpoint === 'product' && isset($_GET['id'])) {
                $_POST = json_decode(json_encode(array('id' => $_GET['id'])), FALSE);
                $productController->getOne();
            } else {
                $productController->getAll();
            }
            break;
            
        case 'POST':
            $productController->create();
            break;
            
        case 'PUT':
            $productController->update();
            break;
            
        case 'DELETE':
            $productController->delete();
            break;
            
        default:
            header("HTTP/1.0 405 Method Not Allowed");
            echo json_encode(array("message" => "Method not allowed."));
            break;
    }
?>