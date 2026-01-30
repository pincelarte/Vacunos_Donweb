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

    // Usamos 'static' para poder usarlo sin tener que crear una vaca primero [cite: 2026-01-28]
    public static function listarPorEstablecimiento($id_est)
    {
        $conexion = new Conexion();
        $con = $conexion->conectar();

        // Buscamos las vacas filtrando por el ID del campo [cite: 2026-01-28]
        $sql = "SELECT * FROM vacunos WHERE id_establecimiento = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$id_est]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca los datos de una sola vaca usando su caravana [cite: 2026-01-28]
    public static function obtenerPorCaravana($caravana)
    {
        $conexion = new Conexion();
        $con = $conexion->conectar();

        $sql = "SELECT * FROM vacunos WHERE caravana = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$caravana]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualiza los datos de una vaca existente [cite: 2026-01-28]
    public function actualizar($caravana_id)
    {
        $con = $this->db->conectar();

        // UPDATE (Actualizar): Cambiamos los valores donde la caravana coincida [cite: 2026-01-28]
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
        $db = (new Conexion())->conectar(); // Abrimos conexión [cite: 2026-01-28]

        // Comando: DELETE FROM (Borrar de) la tabla vacunos [cite: 2026-01-28]
        $stmt = $db->prepare("DELETE FROM vacunos WHERE caravana = :caravana");
        $stmt->bindParam(':caravana', $caravana);

        return $stmt->execute(); // Devuelve verdadero si lo borró con éxito [cite: 2026-01-28]
    }

    public static function existe($caravana)
    {
        // Abrimos la conexión con la base de datos [cite: 2026-01-28]
        $db = (new Conexion())->conectar();

        // Preparamos la consulta para contar si esa caravana ya figura [cite: 2026-01-28]
        $stmt = $db->prepare("SELECT COUNT(*) FROM vacunos WHERE caravana = :caravana");
        $stmt->bindParam(':caravana', $caravana);
        $stmt->execute();

        // fetchColumn() nos devuelve el número directamente [cite: 2026-01-28]
        $cantidad = $stmt->fetchColumn();

        // Si es mayor a 0, significa que ya existe [cite: 2026-01-28]
        return $cantidad > 0;
    }
}
