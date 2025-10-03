<?php
/**
 * Modelo Producto
 * Representa la entidad 'productos' en la base de datos.
 * Proporciona métodos para realizar operaciones CRUD (Crear, Leer, Actualizar, Eliminar) y búsqueda.
 * Utiliza PDO para interactuar de forma segura con la base de datos.
 */

class Producto {
    // Propiedad para almacenar la conexión a la base de datos (PDO).
    private $pdo;

    // Constructor: inicializa el modelo con una conexión PDO.
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Método para crear un nuevo producto
    // Inserta un nuevo registro en la tabla 'productos' con los datos proporcionados.
    public function crear($nombre, $descripcion, $precio, $talla, $color, $categoria, $imagen = null) {
        // Consulta SQL para insertar un nuevo producto.
        $sql = "INSERT INTO productos (nombre, descripcion, precio, talla, color, categoria, imagen) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        // Prepara la consulta para evitar inyecciones SQL.
        $stmt = $this->pdo->prepare($sql);
        // Ejecuta la consulta con los valores proporcionados y devuelve true si tiene éxito.
        return $stmt->execute([$nombre, $descripcion, $precio, $talla, $color, $categoria, $imagen]);
    }

    // Método para obtener todos los productos
    // Recupera todos los registros de la tabla 'productos', ordenados por fecha de creación descendente.
    public function obtenerTodos() {
        // Consulta SQL para seleccionar todos los productos.
        $sql = "SELECT * FROM productos ORDER BY fecha_creacion DESC";
        // Ejecuta la consulta directamente (sin parámetros, no requiere preparación).
        $stmt = $this->pdo->query($sql);
        // Devuelve todos los resultados como un array asociativo.
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener un producto por su ID
    // Recupera un único registro de la tabla 'productos' según el ID proporcionado.
    public function obtenerPorId($id) {
        // Consulta SQL para seleccionar un producto por su ID.
        $sql = "SELECT * FROM productos WHERE id = ?";
        // Prepara la consulta para evitar inyecciones SQL.
        $stmt = $this->pdo->prepare($sql);
        // Ejecuta la consulta con el ID proporcionado.
        $stmt->execute([$id]);
        // Devuelve el resultado como un array asociativo o false si no se encuentra.
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método para actualizar un producto
    // Modifica un registro existente en la tabla 'productos' según el ID y los datos proporcionados.
    public function actualizar($id, $nombre, $descripcion, $precio, $talla, $color, $categoria, $imagen = null) {
        // Consulta SQL para actualizar los campos de un producto.
        $sql = "UPDATE productos 
                SET nombre = ?, descripcion = ?, precio = ?, talla = ?, color = ?, categoria = ?, imagen = ?
                WHERE id = ?";
        // Prepara la consulta para evitar inyecciones SQL.
        $stmt = $this->pdo->prepare($sql);
        // Ejecuta la consulta con los valores proporcionados y devuelve true si tiene éxito.
        return $stmt->execute([$nombre, $descripcion, $precio, $talla, $color, $categoria, $imagen, $id]);
    }

    // Método para eliminar un producto
    // Elimina un registro de la tabla 'productos' según el ID proporcionado.
    public function eliminar($id) {
        // Consulta SQL para eliminar un producto por su ID.
        $sql = "DELETE FROM productos WHERE id = ?";
        // Prepara la consulta para evitar inyecciones SQL.
        $stmt = $this->pdo->prepare($sql);
        // Ejecuta la consulta con el ID proporcionado y devuelve true si tiene éxito.
        return $stmt->execute([$id]);
    }

    // Método para buscar productos
    // Busca productos cuyo nombre o categoría coincidan parcialmente con el término proporcionado.
    public function buscar($termino) {
        // Consulta SQL para buscar productos por nombre o categoría (usando LIKE).
        $sql = "SELECT * FROM productos 
                WHERE nombre LIKE ? OR categoria LIKE ?";
        // Prepara la consulta para evitar inyecciones SQL.
        $stmt = $this->pdo->prepare($sql);
        // Agrega comodines (%) al término de búsqueda para coincidencias parciales.
        $termino = "%$termino%";
        // Ejecuta la consulta con el término para nombre y categoría.
        $stmt->execute([$termino, $termino]);
        // Devuelve todos los resultados como un array asociativo.
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>