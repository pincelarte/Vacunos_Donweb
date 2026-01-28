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

        // INSERT INTO (Insertar en): Ahora usamos los nombres que VOS tenés en la DB [cite: 2026-01-28]
        $sql = "INSERT INTO establecimientos (nombre, ubicacion) VALUES (?, ?)";
        $stmt = $con->prepare($sql);

        return $stmt->execute([$nombre, $ubicacion]);
    }

    public function listarTodo()
    {
        $con = $this->db->conectar();

        // SELECT * (Seleccionar todo): trae id, nombre, ubicacion e id_usuario tal cual están en tu tabla [cite: 2026-01-28]
        $sql = "SELECT * FROM establecimientos";
        $stmt = $con->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca un solo establecimiento por su ID para obtener su nombre [cite: 2026-01-28]
    public function obtenerPorId($id)
    {
        $con = $this->db->conectar();
        $sql = "SELECT nombre FROM establecimientos WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
