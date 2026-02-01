<?php
require_once __DIR__ . '/../config/Conexion.php';

class Vacuno
{
    protected string $tipo;
    protected string $caravana;
    protected string $raza;
    // CAMBIO: Ahora es 'string' porque recibe una fecha (AAAA-MM-DD) [cite: 2026-01-28]
    protected string $edad;
    protected float $peso;
    protected int $id_establecimiento;
    protected string $historial;
    private $db;

    public function __construct($tipo, $caravana, $raza, $edad, $peso, $id_establecimiento)
    {
        $this->tipo = $tipo;
        $this->caravana = $caravana;
        $this->raza = $raza;
        $this->edad = $edad; // Aquí llega la fecha calculada del controlador
        $this->peso = $peso;
        $this->id_establecimiento = $id_establecimiento;
        $this->historial = "";
        $this->db = new Conexion();
    }

    public function insertar()
    {
        $con = $this->db->conectar();

        // El comando INSERT se mantiene igual, pero el valor de :edad será la fecha [cite: 2026-01-28]
        $sql = "INSERT INTO vacunos (caravana, tipo, raza, edad, id_establecimiento, peso_actual, historial) 
                VALUES (:caravana, :tipo, :raza, :edad, :id_est, :peso, :historial)";

        $stmt = $con->prepare($sql);

        $stmt->bindParam(':caravana', $this->caravana);
        $stmt->bindParam(':tipo', $this->tipo);
        $stmt->bindParam(':raza', $this->raza);
        $stmt->bindParam(':edad', $this->edad);
        $stmt->bindParam(':id_est', $this->id_establecimiento);
        $stmt->bindParam(':peso', $this->peso);
        $stmt->bindParam(':historial', $this->historial);

        if ($stmt->execute()) {
            return true; // Simplificado para que el controlador lo entienda mejor
        } else {
            return false;
        }
    }

    public static function getTipos()
    {
        return ['Vaca', 'Toro', 'Ternero', 'Ternera', 'Vaquillona', 'Novillo'];
    }

    public static function getRazas()
    {
        return ['Holando Argentino', 'Aberdeen Angus', 'Hereford', 'Jersey'];
    }

    public static function listarPorEstablecimiento($id_est)
    {
        $conexion = new Conexion();
        $con = $conexion->conectar();
        $sql = "SELECT * FROM vacunos WHERE id_establecimiento = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$id_est]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorCaravana($caravana)
    {
        $conexion = new Conexion();
        $con = $conexion->conectar();
        $sql = "SELECT * FROM vacunos WHERE caravana = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$caravana]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($caravana_id)
    {
        $con = $this->db->conectar();
        // Mantenemos tu lógica de UPDATE [cite: 2026-01-28]
        $sql = "UPDATE vacunos SET edad = ?, peso_actual = ?, historial = ? WHERE caravana = ?";
        $stmt = $con->prepare($sql);
        return $stmt->execute([$this->edad, $this->peso, $this->historial, $caravana_id]);
    }

    public function actualizarHistorial($texto)
    {
        $this->historial = $texto;
    }

    public static function eliminar($caravana)
    {
        $db = (new Conexion())->conectar();
        $stmt = $db->prepare("DELETE FROM vacunos WHERE caravana = :caravana");
        $stmt->bindParam(':caravana', $caravana);
        return $stmt->execute();
    }

    public static function existe($caravana)
    {
        $db = (new Conexion())->conectar();
        $stmt = $db->prepare("SELECT COUNT(*) FROM vacunos WHERE caravana = :caravana");
        $stmt->bindParam(':caravana', $caravana);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
