<?php
require_once __DIR__ . '/../config/Conexion.php';

class Vacuno
{
    protected string $tipo;
    protected string $caravana;
    protected string $raza;
    protected string $edad;
    protected float $peso;
    protected int $id_establecimiento;
    protected string $historial;
    private $db;
    // Agregamos esta propiedad para guardar la conexión activa
    private $con_activa;

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

    // Traduciendo: Obtener el último ID generado por la base de datos
    public function getUltimoId()
    {
        // El comando lastInsertId() devuelve el ID de la última fila insertada
        return $this->con_activa->lastInsertId();
    }

    public function insertar()
    {
        // Guardamos la conexión en la propiedad para poder pedirle el ID después
        $this->con_activa = $this->db->conectar();

        $sql = "INSERT INTO vacunos (caravana, tipo, raza, edad, id_establecimiento, peso_actual, historial) 
                VALUES (:caravana, :tipo, :raza, :edad, :id_est, :peso, :historial)";

        $stmt = $this->con_activa->prepare($sql);
        $stmt->bindParam(':caravana', $this->caravana);
        $stmt->bindParam(':tipo', $this->tipo);
        $stmt->bindParam(':raza', $this->raza);
        $stmt->bindParam(':edad', $this->edad);
        $stmt->bindParam(':id_est', $this->id_establecimiento);
        $stmt->bindParam(':peso', $this->peso);
        $stmt->bindParam(':historial', $this->historial);

        return $stmt->execute();
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

    public static function obtenerPorId($id)
    {
        $conexion = new Conexion();
        $con = $conexion->conectar();
        $sql = "SELECT * FROM vacunos WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id)
    {
        $con = $this->db->conectar();
        $sql = "UPDATE vacunos SET edad = ?, peso_actual = ?, historial = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        return $stmt->execute([$this->edad, $this->peso, $this->historial, $id]);
    }

    public function actualizarPeso($id)
    {
        $con = $this->db->conectar();
        $sql = "UPDATE vacunos SET peso_actual = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        return $stmt->execute([$this->peso, $id]);
    }

    public static function eliminar($id)
    {
        $db = (new Conexion())->conectar();
        $stmt = $db->prepare("DELETE FROM vacunos WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public static function getTipos()
    {
        return ['Vaca', 'Toro', 'Ternero', 'Ternera', 'Vaquillona', 'Novillo'];
    }

    public static function getRazas()
    {
        return ['Holando Argentino', 'Aberdeen Angus', 'Hereford', 'Jersey'];
    }

    // Método para actualizar solo el peso actual (usado desde editar_pesaje.php)
    public static function actualizarPesoSimple($id, $nuevo_peso)
    {
        $con = (new Conexion())->conectar();
        $sql = "UPDATE vacunos SET peso_actual = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        return $stmt->execute([$nuevo_peso, $id]);
    }

    // Método para obtener ID por caravana (usado desde editar_pesaje.php)
    public static function obtenerIdPorCaravana($caravana)
    {
        $con = (new Conexion())->conectar();
        $sql = "SELECT id FROM vacunos WHERE caravana = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$caravana]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['id'] : null;
    }
}
