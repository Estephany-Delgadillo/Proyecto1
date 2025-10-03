<?php
/**
 * Endpoint de autenticación.
 * Valida credenciales y crea una sesión PHP.
 * 
 * Requisito: "Registro y autenticación de usuarios".
 */
session_start();
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Correo y contraseña son requeridos']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, nombre_completo, password FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre_completo'];
        echo json_encode(['mensaje' => 'Autenticación exitosa']);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Credenciales incorrectas']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno']);
}
?>