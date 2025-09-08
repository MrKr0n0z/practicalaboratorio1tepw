<?php
// Headers CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Cargar autoloader
require_once 'autoload/autoload.php';

try {
    // Obtener parámetros de la URL
    $request = $_SERVER['REQUEST_URI'];
    $path = parse_url($request, PHP_URL_PATH);
    $pathParts = explode('/', trim($path, '/'));
    
    // Obtener tabla y acción de la URL
    $apiIndex = array_search('api.php', $pathParts);
    $table = isset($pathParts[$apiIndex + 1]) ? $pathParts[$apiIndex + 1] : '';
    $id = isset($pathParts[$apiIndex + 2]) ? $pathParts[$apiIndex + 2] : '';
    $action = isset($pathParts[$apiIndex + 3]) ? $pathParts[$apiIndex + 3] : '';
    
    if (empty($table)) {
        http_response_code(400);
        echo json_encode(['error' => 'Tabla no especificada']);
        exit();
    }
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Crear controlador y manejar la petición
    $controller = new ApiController($table);
    $result = $controller->handleRequest($method, $id, $action);
    
    echo json_encode($result);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>