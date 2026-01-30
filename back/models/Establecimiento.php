<?php
require_once __DIR__ . '/../config/Conexion.php';

class Establecimiento
{
    private $db;

    public function __construct()
    {
        $this->db = new Conexion();
    }

    public function crear($nombre, $ubicacion)
    {
        $con = $this->db->conectar();
        $sql = "INSERT INTO establecimientos (nombre, ubicacion) VALUES (?, ?)";
        $stmt = $con->prepare($sql);
        return $stmt->execute([$nombre, $ubicacion]);
    }

    public function listarTodo()
    {
        $con = $this->db->conectar();
        $sql = "SELECT * FROM establecimientos";
        $stmt = $con->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id)
    {
        $con = $this->db->conectar();
        $sql = "SELECT nombre FROM establecimientos WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function eliminar($id)
    {
        $con = $this->db->conectar();
        // Usamos el marcador '?' para que el ID se envíe de forma segura [cite: 2026-01-28]
        $sql = "DELETE FROM establecimientos WHERE id = ?";
        $stmt = $con->prepare($sql);
        // execute (Ejecutar): despacha la orden a la base de datos [cite: 2026-01-28]
        return $stmt->execute([$id]);
    }
}
