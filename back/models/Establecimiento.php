<?php
require_once __DIR__ . '/../config/Conexion.php';

class Establecimiento
{
    private $db;

    public function __construct()
    {
        $this->db = new Conexion();
    }

    // LISTAR: Usaba los nombres simples [cite: 2026-01-28]
    public function listarTodo()
    {
        $con = $this->db->conectar();
        $sql = "SELECT * FROM establecimientos";
        $stmt = $con->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // CREAR: Antes de cambiar los nombres de las columnas [cite: 2026-01-28]
    public function crear($nombre, $ubicacion)
    {
        $con = $this->db->conectar();
        // Cambiamos 'nombre_est' por 'nombre' y 'ubicacion_est' por 'ubicacion' [cite: 2026-01-28]
        $sql = "INSERT INTO establecimientos (nombre, ubicacion) VALUES (?, ?)";
        $stmt = $con->prepare($sql);
        return $stmt->execute([$nombre, $ubicacion]);
    }

    // ACTUALIZAR: Volviendo al ID simple [cite: 2026-01-28]
    public function actualizar($id, $nuevoNombre)
    {
        $con = $this->db->conectar();
        $sql = "UPDATE establecimientos SET nombre = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        return $stmt->execute([$nuevoNombre, $id]);
    }

    // ELIMINAR: Usando 'id' en lugar de 'id_est' [cite: 2026-01-28]
    public function eliminar($id)
    {
        $con = $this->db->conectar();
        $sql = "DELETE FROM establecimientos WHERE id = ?";
        $stmt = $con->prepare($sql);
        return $stmt->execute([$id]);
    }

    // OBTENER POR ID: Indispensable para que la edición no de Error 500 [cite: 2026-01-28]
    public function obtenerPorId($id)
    {
        $con = $this->db->conectar();
        $sql = "SELECT * FROM establecimientos WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
