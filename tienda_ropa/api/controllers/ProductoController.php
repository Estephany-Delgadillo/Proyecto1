<?php
/**
 * Controlador de Productos
 * Maneja las solicitudes HTTP relacionadas con la entidad 'productos'.
 * Devuelve respuestas en formato JSON.
 * Este controlador implementa un CRUD (Crear, Leer, Actualizar, Eliminar) y una funcionalidad de búsqueda.
 */

// Incluye el modelo Producto, que contiene la lógica para interactuar con la base de datos.
require_once __DIR__ . '/../models/Producto.php';

class ProductoController {
    // Propiedad para almacenar una instancia del modelo Producto.
    private $productoModel;

    // Constructor: inicializa el controlador con una conexión a la base de datos (PDO).
    public function __construct($pdo) {
        $this->productoModel = new Producto($pdo);
    }

    // Método GET /productos → Listar todos los productos
    // Devuelve un listado de todos los productos en formato JSON.
    public function listar() {
        try {
            // Obtiene todos los productos desde el modelo.
            $productos = $this->productoModel->obtenerTodos();
            // Responde con código HTTP 200 (OK) y los productos en JSON.
            http_response_code(200);
            echo json_encode($productos);
        } catch (Exception $e) {
            // En caso de error, responde con código HTTP 500 (Error interno del servidor) y un mensaje de error.
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener productos: ' . $e->getMessage()]);
        }
    }

    // Método GET /productos/{id} → Obtener un producto por su ID
    // Devuelve los detalles de un producto específico.
    public function obtener($id) {
        // Valida que el ID sea numérico.
        if (!is_numeric($id)) {
            http_response_code(400); // Código HTTP 400 (Solicitud incorrecta).
            echo json_encode(['error' => 'ID inválido']);
            return;
        }

        // Consulta el producto por su ID.
        $producto = $this->productoModel->obtenerPorId($id);
        if ($producto) {
            // Si el producto existe, responde con código 200 y los datos del producto.
            http_response_code(200);
            echo json_encode($producto);
        } else {
            // Si no se encuentra, responde con código 404 (No encontrado).
            http_response_code(404);
            echo json_encode(['error' => 'Producto no encontrado']);
        }
    }

    // Método POST /productos → Crear un nuevo producto
    // Permite añadir un nuevo producto a la base de datos.
    public function crear() {
        // Obtiene los datos enviados en el cuerpo de la solicitud (formato JSON).
        $datos = json_decode(file_get_contents("php://input"), true);

        // Valida que los datos existan y contengan los campos requeridos (nombre y precio).
        if (!$datos || !isset($datos['nombre']) || !isset($datos['precio'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos. Se requieren nombre y precio.']);
            return;
        }

        // Limpia y asigna los valores recibidos, usando valores predeterminados si no se proporcionan.
        $nombre = trim($datos['nombre']);
        $descripcion = trim($datos['descripcion'] ?? '');
        $precio = floatval($datos['precio']);
        $talla = trim($datos['talla'] ?? '');
        $color = trim($datos['color'] ?? '');
        $categoria = trim($datos['categoria'] ?? '');
        $imagen = trim($datos['imagen'] ?? null);

        // Valida que el precio sea mayor a 0.
        if ($precio <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'El precio debe ser mayor a 0.']);
            return;
        }

        try {
            // Intenta crear el producto usando el modelo.
            if ($this->productoModel->crear($nombre, $descripcion, $precio, $talla, $color, $categoria, $imagen)) {
                // Responde con código 201 (Creado) si se creó correctamente.
                http_response_code(201);
                echo json_encode(['mensaje' => 'Producto creado exitosamente']);
            } else {
                // Responde con código 500 si no se pudo crear.
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo crear el producto']);
            }
        } catch (Exception $e) {
            // Maneja cualquier excepción y responde con un mensaje de error.
            http_response_code(500);
            echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    // Método PUT /productos/{id} → Actualizar un producto existente
    // Permite modificar los datos de un producto según su ID.
    public function actualizar($id) {
        // Valida que el ID sea numérico.
        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido']);
            return;
        }

        // Obtiene los datos enviados en el cuerpo de la solicitud (formato JSON).
        $datos = json_decode(file_get_contents("php://input"), true);

        // Valida que los datos existan y contengan los campos requeridos (nombre y precio).
        if (!$datos || !isset($datos['nombre']) || !isset($datos['precio'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos. Se requieren nombre y precio.']);
            return;
        }

        // Limpia y asigna los valores recibidos, usando valores predeterminados si no se proporcionan.
        $nombre = trim($datos['nombre']);
        $descripcion = trim($datos['descripcion'] ?? '');
        $precio = floatval($datos['precio']);
        $talla = trim($datos['talla'] ?? '');
        $color = trim($datos['color'] ?? '');
        $categoria = trim($datos['categoria'] ?? '');
        $imagen = trim($datos['imagen'] ?? null);

        // Valida que el precio sea mayor a 0.
        if ($precio <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'El precio debe ser mayor a 0.']);
            return;
        }

        // Verifica si el producto existe antes de intentar actualizarlo.
        $productoExistente = $this->productoModel->obtenerPorId($id);
        if (!$productoExistente) {
            http_response_code(404);
            echo json_encode(['error' => 'Producto no encontrado']);
            return;
        }

        try {
            // Intenta actualizar el producto usando el modelo.
            if ($this->productoModel->actualizar($id, $nombre, $descripcion, $precio, $talla, $color, $categoria, $imagen)) {
                // Responde con código 200 si se actualizó correctamente.
                http_response_code(200);
                echo json_encode(['mensaje' => 'Producto actualizado exitosamente']);
            } else {
                // Responde con código 500 si no se pudo actualizar.
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo actualizar el producto']);
            }
        } catch (Exception $e) {
            // Maneja cualquier excepción y responde con un mensaje de error.
            http_response_code(500);
            echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    // Método DELETE /productos/{id} → Eliminar un producto
    // Elimina un producto de la base de datos según su ID.
    public function eliminar($id) {
        // Valida que el ID sea numérico.
        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido']);
            return;
        }

        // Verifica si el producto existe antes de intentar eliminarlo.
        $productoExistente = $this->productoModel->obtenerPorId($id);
        if (!$productoExistente) {
            http_response_code(404);
            echo json_encode(['error' => 'Producto no encontrado']);
            return;
        }

        try {
            // Intenta eliminar el producto usando el modelo.
            if ($this->productoModel->eliminar($id)) {
                // Responde con código 200 si se eliminó correctamente.
                http_response_code(200);
                echo json_encode(['mensaje' => 'Producto eliminado exitosamente']);
            } else {
                // Responde con código 500 si no se pudo eliminar.
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo eliminar el producto']);
            }
        } catch (Exception $e) {
            // Maneja cualquier excepción y responde con un mensaje de error.
            http_response_code(500);
            echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    // Método GET /productos/buscar?q=... → Búsqueda por término
    // Busca productos que coincidan con un término de búsqueda.
    public function buscar($termino) {
        // Valida que el término de búsqueda no esté vacío.
        if (empty($termino)) {
            http_response_code(400);
            echo json_encode(['error' => 'Parámetro de búsqueda vacío']);
            return;
        }

        try {
            // Realiza la búsqueda usando el modelo y devuelve los resultados.
            $resultados = $this->productoModel->buscar($termino);
            http_response_code(200);
            echo json_encode($resultados);
        } catch (Exception $e) {
            // Maneja cualquier excepción y responde con un mensaje de error.
            http_response_code(500);
            echo json_encode(['error' => 'Error en la búsqueda: ' . $e->getMessage()]);
        }
    }
}
?>