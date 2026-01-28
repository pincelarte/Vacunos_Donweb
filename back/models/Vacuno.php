<?php
require_once __DIR__ . '/../config/Conexion.php';

class Vacuno
{
    protected string $tipo;
    protected string $caravana;
    protected string $raza;
    protected int $edad;
    protected float $peso;
    protected int $id_establecimiento;
    protected string $historial;
    private $db;

    public function __construct($tipo, $caravana, $raza, $edad, $peso, $id_establecimiento)
    {
        $this->tipo = $tipo;
        $this->caravana = $caravana;
        $this->raza = $raza;
        $this->edad = $edad;
        $this->peso = $peso;
        $this->id_establecimiento = $id_establecimiento;
        $this->historial = "";
        $this->db = new Conexion();
    }

    public function insertar()
    {
        $con = $this->db->conectar();

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
            return "Vacuno guardado con éxito.";
        } else {
            return "Error al guardar.";
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
}
