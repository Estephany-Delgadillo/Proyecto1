<?php
/**
 * Archivo de conexión a la base de datos MySQL
 * Proyecto: Tienda de Ropa - Desarrollo de Aplicaciones Web
 */

$host = 'localhost';      // Servidor de la base de datos
$dbname = 'tienda_ropa';  // Nombre de la base de datos
$username = 'root';       // Usuario por defecto en XAMPP
$password = '';           // Contraseña vacía por defecto en XAMPP

try {
    // Crear una instancia de PDO (PHP Data Objects)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Configurar el modo de error de PDO a excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En caso de error, mostrar un mensaje en formato JSON (útil para APIs)
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a la base de datos: ' . $e->getMessage()]);
    exit();
}
?>