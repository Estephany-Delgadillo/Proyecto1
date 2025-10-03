<?php
/**
 * Menú de usuario protegido por sesión.
 * Solo accesible si el usuario ha iniciado sesión.
 * 
 * Requisito: "Autenticación de usuarios".
 */
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Menú de Usuario</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f9f9f9; }
    .container { max-width: 500px; margin: 50px auto; padding: 20px; background: white; border: 1px solid #ddd; }
    .btn { display: block; width: 100%; padding: 12px; margin: 10px 0; background: #0056b3; color: white; text-decoration: none; text-align: center; }
  </style>
</head>
<body>
  <!--
    Menú principal para usuarios autenticados.
    Permite acceder a todas las funcionalidades CRUD.
  -->
  <div class="container">
    <h2>Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?></h2>
    <a href="crear_producto.html" class="btn">Registrar Producto</a>
    <a href="listar_productos.html" class="btn">Ver Productos</a>
    <a href="listar_usuarios.html" class="btn">Ver Usuarios</a>
    <a href="logout.php" class="btn" style="background:#6c757d;">Cerrar Sesión</a>
  </div>
</body>
</html>