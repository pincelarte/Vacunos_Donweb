<?php
require_once __DIR__ . '/../config/Conexion.php';

class Pesaje
{
    private $db;

    public function __construct()
    {
        $this->db = new Conexion();
    }

    // Traduciendo: Registrar ahora usa el ID numérico de la vaca [cite: 2026-01-24]
    public function registrar($id_vaca, $caravana, $peso)
    {
        $con = $this->db->conectar();
        // Guardamos el ID de la vaca (para que no se mezcle) y la caravana (por referencia)
        $sql = "INSERT INTO pesajes (id_vaca, caravana_vacuno, peso) VALUES (:id_vaca, :caravana, :peso)";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id_vaca', $id_vaca);
        $stmt->bindParam(':caravana', $caravana);
        $stmt->bindParam(':peso', $peso);
        return $stmt->execute();
    }

    // Traduciendo: Traer historial buscando por el ID único del animal
    public static function obtenerHistorial($id_vaca)
    {
        $conexion = new Conexion();
        $con = $conexion->conectar();
        // Cambiamos el WHERE para que use id_vaca en lugar del texto de caravana
        $sql = "SELECT id, peso, fecha_pesaje FROM pesajes WHERE id_vaca = ? ORDER BY fecha_pesaje DESC";
        $stmt = $con->prepare($sql);
        $stmt->execute([$id_vaca]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // El comando DELETE se mantiene igual porque el ID de pesaje ya es único
    public static function eliminar($id_pesaje)
    {
        $db = (new Conexion())->conectar();
        $sql = "DELETE FROM pesajes WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$id_pesaje]);
    }

    // El comando UPDATE para corregir pesajes
    public static function corregir($id_pesaje, $nuevo_valor)
    {
        $db = (new Conexion())->conectar();
        $sql = "UPDATE pesajes SET peso = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$nuevo_valor, $id_pesaje]);
    }

    // Obtener un pesaje por su ID
    public static function obtenerPorId($id_pesaje)
    {
        $db = (new Conexion())->conectar();
        $sql = "SELECT * FROM pesajes WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_pesaje]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
