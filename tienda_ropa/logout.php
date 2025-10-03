<?php
/**
 * Archivo para cerrar la sesión del usuario.
 * Destruye la sesión y redirige al inicio.
 * 
 * Requisito: "Autenticación de usuarios".
 */
session_start();
session_destroy();
header('Location: index.html');
exit();
?>