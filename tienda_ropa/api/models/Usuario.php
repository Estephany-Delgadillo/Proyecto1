<?php
/**
 * Modelo Usuario
 * Representa la entidad 'usuarios' en la base de datos.
 * Proporciona métodos para realizar operaciones CRUD (Crear, Leer, Actualizar, Eliminar) y verificar la existencia de un email.
 * Utiliza PDO para interactuar de forma segura con la base de datos.
 */

class Usuario {
    // Propiedad para almacenar la conexión a la base de datos (PDO).
    private $pdo;

    // Constructor: inicializa el modelo con una conexión PDO.
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Método para crear un nuevo usuario
    // Inserta un nuevo registro en la tabla 'usuarios' con los datos proporcionados.
    public function crear($nombre_completo, $email, $password) {
        // Encripta la contraseña usando el algoritmo predeterminado de PHP (bcrypt por defecto).
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        // Consulta SQL para insertar un nuevo usuario.
        $sql = "INSERT INTO usuarios (nombre_completo, email, password) VALUES (?, ?, ?)";
        // Prepara la consulta para evitar inyecciones SQL.
        $stmt = $this->pdo->prepare($sql);
        // Ejecuta la consulta con los valores proporcionados y devuelve true si tiene éxito.
        return $stmt->execute([$nombre_completo, $email, $password_hash]);
    }

    // Método para obtener todos los usuarios
    // Recupera todos los registros de la tabla 'usuarios', ordenados por fecha de registro descendente.
    public function obtenerTodos() {
        // Consulta SQL para seleccionar todos los usuarios, excluyendo la contraseña por seguridad.
        $sql = "SELECT id, nombre_completo, email, fecha_registro FROM usuarios ORDER BY fecha_registro DESC";
        // Ejecuta la consulta directamente (sin parámetros, no requiere preparación).
        $stmt = $this->pdo->query($sql);
        // Devuelve todos los resultados como un array asociativo.
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener un usuario por su ID
    // Recupera un único registro de la tabla 'usuarios' según el ID proporcionado.
    public function obtenerPorId($id) {
        // Consulta SQL para seleccionar un usuario por su ID, excluyendo la contraseña.
        $sql = "SELECT id, nombre_completo, email, fecha_registro FROM usuarios WHERE id = ?";
        // Prepara la consulta para evitar inyecciones SQL.
        $stmt = $this->pdo->prepare($sql);
        // Ejecuta la consulta con el ID proporcionado.
        $stmt->execute([$id]);
        // Devuelve el resultado como un array asociativo o false si no se encuentra.
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método para actualizar un usuario
    // Modifica un registro existente en la tabla 'usuarios' según el ID y los datos proporcionados.
    public function actualizar($id, $nombre_completo, $email) {
        // Consulta SQL para actualizar los campos nombre_completo y email de un usuario.
        $sql = "UPDATE usuarios SET nombre_completo = ?, email = ? WHERE id = ?";
        // Prepara la consulta para evitar inyecciones SQL.
        $stmt = $this->pdo->prepare($sql);
        // Ejecuta la consulta con los valores proporcionados y devuelve true si tiene éxito.
        return $stmt->execute([$nombre_completo, $email, $id]);
    }

    // Método para eliminar un usuario
    // Elimina un registro de la tabla 'usuarios' según el ID proporcionado.
    public function eliminar($id) {
        // Consulta SQL para eliminar un usuario por su ID.
        $sql = "DELETE FROM usuarios WHERE id = ?";
        // Prepara la consulta para evitar inyecciones SQL.
        $stmt = $this->pdo->prepare($sql);
        // Ejecuta la consulta con el ID proporcionado y devuelve true si tiene éxito.
        return $stmt->execute([$id]);
    }

    // Método para verificar si un email ya está registrado
    // Comprueba si existe un usuario con el email proporcionado.
    public function existeEmail($email) {
        // Consulta SQL para buscar un usuario por su email.
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        // Prepara la consulta para evitar inyecciones SQL.
        $stmt = $this->pdo->prepare($sql);
        // Ejecuta la consulta con el email proporcionado.
        $stmt->execute([$email]);
        // Devuelve true si se encuentra un registro, false si no.
        return $stmt->fetch() !== false;
    }
}
?>