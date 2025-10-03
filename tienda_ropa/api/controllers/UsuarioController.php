<?php
/**
 * Controlador de Usuarios
 * Maneja las solicitudes HTTP relacionadas con la entidad 'usuarios'.
 * Implementa un CRUD (Crear, Leer, Actualizar, Eliminar) y devuelve respuestas en formato JSON.
 */

// Incluye el modelo Usuario, que contiene la lógica para interactuar con la base de datos.
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {
    // Propiedad para almacenar una instancia del modelo Usuario.
    private $usuarioModel;

    // Constructor: inicializa el controlador con una conexión a la base de datos (PDO).
    public function __construct($pdo) {
        $this->usuarioModel = new Usuario($pdo);
    }

    // Método GET /usuarios → Listar todos los usuarios
    // Devuelve un listado de todos los usuarios en formato JSON.
    public function listar() {
        try {
            // Obtiene todos los usuarios desde el modelo.
            $usuarios = $this->usuarioModel->obtenerTodos();
            // Responde con código HTTP 200 (OK) y los usuarios en JSON.
            http_response_code(200);
            echo json_encode($usuarios);
        } catch (Exception $e) {
            // En caso de error, responde con código HTTP 500 (Error interno del servidor) y un mensaje genérico.
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener usuarios']);
        }
    }

    // Método GET /usuarios/{id} → Obtener un usuario por su ID
    // Devuelve los detalles de un usuario específico.
    public function obtener($id) {
        // Valida que el ID sea numérico.
        if (!is_numeric($id)) {
            http_response_code(400); // Código HTTP 400 (Solicitud incorrecta).
            echo json_encode(['error' => 'ID inválido']);
            return;
        }

        // Consulta el usuario por su ID.
        $usuario = $this->usuarioModel->obtenerPorId($id);
        if ($usuario) {
            // Si el usuario existe, responde con código 200 y los datos del usuario.
            http_response_code(200);
            echo json_encode($usuario);
        } else {
            // Si no se encuentra, responde con código 404 (No encontrado).
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
        }
    }

    // Método POST /usuarios → Crear un nuevo usuario
    // Permite registrar un nuevo usuario en la base de datos.
    public function crear() {
        // Obtiene los datos enviados en el cuerpo de la solicitud (formato JSON).
        $datos = json_decode(file_get_contents("php://input"), true);

        // Valida que los datos existan y contengan los campos requeridos (nombre_completo, email, password).
        if (!$datos || !isset($datos['nombre_completo']) || !isset($datos['email']) || !isset($datos['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Faltan datos: nombre_completo, email y password son requeridos']);
            return;
        }

        // Limpia y asigna los valores recibidos.
        $nombre = trim($datos['nombre_completo']);
        $email = trim($datos['email']);
        $password = $datos['password'];

        // Valida que la contraseña tenga al menos 6 caracteres.
        if (strlen($password) < 6) {
            http_response_code(400);
            echo json_encode(['error' => 'La contraseña debe tener al menos 6 caracteres']);
            return;
        }

        // Verifica si el correo ya está registrado.
        if ($this->usuarioModel->existeEmail($email)) {
            http_response_code(409); // Código HTTP 409 (Conflicto).
            echo json_encode(['error' => 'El correo ya está registrado']);
            return;
        }

        try {
            // Intenta crear el usuario usando el modelo.
            if ($this->usuarioModel->crear($nombre, $email, $password)) {
                // Responde con código 201 (Creado) si se creó correctamente.
                http_response_code(201);
                echo json_encode(['mensaje' => 'Usuario registrado exitosamente']);
            } else {
                // Responde con código 500 si no se pudo crear.
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo registrar el usuario']);
            }
        } catch (Exception $e) {
            // Maneja cualquier excepción y responde con un mensaje genérico.
            http_response_code(500);
            echo json_encode(['error' => 'Error interno']);
        }
    }

    // Método PUT /usuarios/{id} → Actualizar un usuario existente
    // Permite modificar los datos de un usuario según su ID.
    public function actualizar($id) {
        // Valida que el ID sea numérico.
        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido']);
            return;
        }

        // Obtiene los datos enviados en el cuerpo de la solicitud (formato JSON).
        $datos = json_decode(file_get_contents("php://input"), true);

        // Valida que los datos existan y contengan los campos requeridos (nombre_completo y email).
        if (!$datos || !isset($datos['nombre_completo']) || !isset($datos['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Faltan nombre_completo o email']);
            return;
        }

        // Limpia y asigna los valores recibidos.
        $nombre = trim($datos['nombre_completo']);
        $email = trim($datos['email']);

        // Verifica si el usuario existe antes de intentar actualizarlo.
        $usuarioActual = $this->usuarioModel->obtenerPorId($id);
        if (!$usuarioActual) {
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
            return;
        }

        // Verifica si el nuevo email ya está en uso por otro usuario.
        if ($usuarioActual['email'] !== $email && $this->usuarioModel->existeEmail($email)) {
            http_response_code(409);
            echo json_encode(['error' => 'El correo ya está en uso']);
            return;
        }

        try {
            // Intenta actualizar el usuario usando el modelo.
            if ($this->usuarioModel->actualizar($id, $nombre, $email)) {
                // Responde con código 200 si se actualizó correctamente.
                http_response_code(200);
                echo json_encode(['mensaje' => 'Usuario actualizado']);
            } else {
                // Responde con código 500 si no se pudo actualizar.
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo actualizar']);
            }
        } catch (Exception $e) {
            // Maneja cualquier excepción y responde con un mensaje genérico.
            http_response_code(500);
            echo json_encode(['error' => 'Error interno']);
        }
    }

    // Método DELETE /usuarios/{id} → Eliminar un usuario
    // Elimina un usuario de la base de datos según su ID.
    public function eliminar($id) {
        // Valida que el ID sea numérico.
        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido']);
            return;
        }

        // Verifica si el usuario existe antes de intentar eliminarlo.
        if (!$this->usuarioModel->obtenerPorId($id)) {
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
            return;
        }

        try {
            // Intenta eliminar el usuario usando el modelo.
            if ($this->usuarioModel->eliminar($id)) {
                // Responde con código 200 si se eliminó correctamente.
                http_response_code(200);
                echo json_encode(['mensaje' => 'Usuario eliminado']);
            } else {
                // Responde con código 500 si no se pudo eliminar.
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo eliminar']);
            }
        } catch (Exception $e) {
            // Maneja cualquier excepción y responde con un mensaje genérico.
            http_response_code(500);
            echo json_encode(['error' => 'Error interno']);
        }
    }
}
?>