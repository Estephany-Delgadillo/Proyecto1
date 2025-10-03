<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ProductoController.php';

// Obtener el recurso desde ?path=...
$path = $_GET['path'] ?? '';
$parts = explode('/', $path);
$resource = $parts[0] ?? '';
$id = $parts[1] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

// Instancias
$productoController = new ProductoController($pdo);

if ($resource === 'productos') {
    if ($method === 'GET') {
        if ($id === null) {
            $productoController->listar();
        } elseif (is_numeric($id)) {
            $productoController->obtener($id);
        } elseif ($id === 'buscar') {
            $q = $_GET['q'] ?? '';
            $productoController->buscar($q);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Ruta no válida']);
        }
    } elseif ($method === 'POST' && $id === null) {
        $productoController->crear();
    } elseif ($method === 'PUT' && is_numeric($id)) {
        $productoController->actualizar($id);
    } elseif ($method === 'DELETE' && is_numeric($id)) {
        $productoController->eliminar($id);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
    }

} elseif ($resource === 'usuarios') {
    require_once __DIR__ . '/../controllers/UsuarioController.php';
    $usuarioController = new UsuarioController($pdo);

    if ($method === 'GET') {
        if ($id === null) {
            $usuarioController->listar();
        } elseif (is_numeric($id)) {
            $usuarioController->obtener($id);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Ruta no válida']);
        }
    } elseif ($method === 'POST' && $id === null) {
        $usuarioController->crear();
    } elseif ($method === 'PUT' && is_numeric($id)) {
        $usuarioController->actualizar($id);
    } elseif ($method === 'DELETE' && is_numeric($id)) {
        $usuarioController->eliminar($id);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
    }

} else {
    // Depuración: ¿qué recurso recibió?
    error_log("Recurso recibido: '$resource'");
    http_response_code(404);
    echo json_encode(['error' => 'Recurso no encontrado', 'recurso_recibido' => $resource]);
}
?>