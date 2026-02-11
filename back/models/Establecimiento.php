<?php
require_once __DIR__ . '/../config/Conexion.php';

class Establecimiento
{
    private $db;

    public function __construct()
    {
        $this->db = new Conexion();
    }

    // LISTAR: Solo los establecimientos del usuario logueado
    public function listarPorUsuario($id_usuario)
    {
        $con = $this->db->conectar();
        $sql = "SELECT * FROM establecimientos WHERE id_usuario = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // CREAR: Agregar el id_usuario del usuario logueado
    public function crear($nombre, $ubicacion, $id_usuario)
    {
        $con = $this->db->conectar();
        // Cambiamos 'nombre_est' por 'nombre' y 'ubicacion_est' por 'ubicacion' [cite: 2026-01-28]
        $sql = "INSERT INTO establecimientos (nombre, ubicacion, id_usuario) VALUES (?, ?, ?)";
        $stmt = $con->prepare($sql);
        return $stmt->execute([$nombre, $ubicacion, $id_usuario]);
    }

    // ACTUALIZAR: Verificar que pertenezca al usuario
    public function actualizarPorUsuario($id, $nuevoNombre, $id_usuario)
    {
        $con = $this->db->conectar();
        $sql = "UPDATE establecimientos SET nombre = ? WHERE id = ? AND id_usuario = ?";
        $stmt = $con->prepare($sql);
        return $stmt->execute([$nuevoNombre, $id, $id_usuario]);
    }

    // ELIMINAR: Verificar que pertenezca al usuario
    public function eliminarPorUsuario($id, $id_usuario)
    {
        $con = $this->db->conectar();
        $sql = "DELETE FROM establecimientos WHERE id = ? AND id_usuario = ?";
        $stmt = $con->prepare($sql);
        return $stmt->execute([$id, $id_usuario]);
    }

    // OBTENER POR ID: Verificar que pertenezca al usuario
    public function obtenerPorIdYUsuario($id, $id_usuario)
    {
        $con = $this->db->conectar();
        $sql = "SELECT * FROM establecimientos WHERE id = ? AND id_usuario = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$id, $id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
