<?php

class Conexion
{
    private $conexion;
    
    public function __construct()
    {
        $rutaEnv = __DIR__ . '/../../.env';
        $this->cargarEnv($rutaEnv);
    }

    private function cargarEnv($ruta)
    {
        if (!file_exists($ruta)) {
            return false;
        }

        $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lineas as $linea) {
            if (strpos(trim($linea), '#') === 0) {
                continue;
            }

            if (strpos($linea, '=') !== false) {
                list($nombre, $valor) = explode('=', $linea, 2);
                $nombre = trim($nombre);
                $valor = trim($valor);

                putenv("$nombre=$valor");
            }
        }
        return true;
    }
    public function conectar()
    {
        try {
            // 1. Armamos el DSN (la ruta mágica)
            // mysql:host=localhost;dbname=nombre_bd;charset=utf8
            $dsn = "mysql:host=" . getenv('DB_HOST') .
                ";dbname=" . getenv('DB_NAME') .
                ";charset=utf8";

            // 2. Creamos la instancia de PDO
            $this->conexion = new PDO(
                $dsn,
                getenv('DB_USER'),
                getenv('DB_PASS')
            );

            // 3. Configuramos PDO para que sea estricto con los errores
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->conexion;
        } catch (PDOException $e) {
            // Si algo falla, lo atrapamos aquí
            echo "Error de conexión: " . $e->getMessage();
            return null;
        }
    }
} 