<?php
require_once __DIR__ . '/../config/Conexion.php';

class Pesaje
{
    private $db;

    public function __construct()
    {
        $this->db = new Conexion();
    }

    /**
     * Comando: INSERT (Insertar).
     * Registra un nuevo peso asociado a una caravana.
     */
    public function registrar($caravana, $peso)
    {
        $con = $this->db->conectar();

        $sql = "INSERT INTO pesajes (caravana_vacuno, peso) VALUES (:caravana, :peso)";

        $stmt = $con->prepare($sql);

        // Vinculamos los datos para que sean seguros
        $stmt->bindParam(':caravana', $caravana);
        $stmt->bindParam(':peso', $peso);

        return $stmt->execute();
    }

    /**
     * Comando: SELECT (Seleccionar). 
     * Trae el ID, el peso y la fecha. El ID es vital para poder borrar o editar después.
     */
    public static function obtenerHistorial($caravana)
    {
        $conexion = new Conexion();
        $con = $conexion->conectar();

        // IMPORTANTE: Agregamos 'id' a la consulta. Sin esto no podemos identificar qué pesaje borrar.
        $sql = "SELECT id, peso, fecha_pesaje FROM pesajes WHERE caravana_vacuno = ? ORDER BY fecha_pesaje DESC";

        $stmt = $con->prepare($sql);
        $stmt->execute([$caravana]);

        // Retornamos todos los registros como un array asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Comando: DELETE (Borrar).
     * Borra un registro de pesaje específico usando su ID único.
     */
    public static function eliminar($id_pesaje)
    {
        $db = (new Conexion())->conectar();
        $sql = "DELETE FROM pesajes WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$id_pesaje]);
    }

    /**
     * Comando: UPDATE (Actualizar).
     * Cambia el valor del peso de un registro ya existente.
     */
    public static function corregir($id_pesaje, $nuevo_valor)
    {
        $db = (new Conexion())->conectar();
        $sql = "UPDATE pesajes SET peso = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$nuevo_valor, $id_pesaje]);
    }
}
