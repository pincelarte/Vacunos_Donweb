<?php
require_once __DIR__ . '/../config/Conexion.php';

class Usuario
{
    private $db;

    public function __construct()
    {
        // Guardamos la instancia de conexión una sola vez al iniciar la clase
        $this->db = new Conexion();
    }

    // 1. MÉTODO PARA LOGIN: Busca si el nombre existe
    public function buscarPorNombre($username)
    {
        $con = $this->db->conectar();
        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = :nombre LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':nombre', $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. MÉTODO PARA REGISTRO: Inserta el nuevo usuario con su hash
    public function registrar($nombre, $passwordHash)
    {
        $con = $this->db->conectar();

        // Usamos los nombres de columnas que me pasaste: nombre_usuario y password
        $sql = "INSERT INTO usuarios (nombre_usuario, password) VALUES (?, ?)";
        $stmt = $con->prepare($sql);

        return $stmt->execute([$nombre, $passwordHash]);
    }
}
