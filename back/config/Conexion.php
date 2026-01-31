<?php

/**
 * Configuración de Conexión a Base de Datos
 * Versión robusta con manejo seguro de errores
 */

class Conexion
{
    private $conexion;
    private $logFile;

    public function __construct()
    {
        $rutaEnv = __DIR__ . '/../../.env';
        $this->cargarEnv($rutaEnv);
        $this->logFile = __DIR__ . '/../../logs/db_errors.log';

        // Crear directorio de logs si no existe
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
    }

    /**
     * Carga variables de entorno desde archivo .env
     */
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

    /**
     * Registra errores en archivo de log (no muestra al usuario)
     */
    private function logError($mensaje)
    {
        $fecha = date('Y-m-d H:i:s');
        $logEntry = "[$fecha] $mensaje" . PHP_EOL;
        @file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Valida que las variables de entorno estén configuradas
     */
    private function validarConfiguracion()
    {
        $required = ['DB_HOST', 'DB_NAME', 'DB_USER'];
        foreach ($required as $var) {
            $valor = getenv($var);
            if (empty($valor)) {
                $this->logError("Variable de entorno $var no configurada");
                return false;
            }
        }
        return true;
    }

    /**
     * Establece conexión a la base de datos
     * Retorna null en caso de error (sin mostrar información sensible)
     */
    public function conectar()
    {
        // Validar configuración antes de intentar conectar
        if (!$this->validarConfiguracion()) {
            $this->logError("Error de configuración de base de datos");
            return null;
        }

        try {
            // 1. Armamos el DSN (la ruta mágica)
            $dsn = "mysql:host=" . getenv('DB_HOST') .
                ";dbname=" . getenv('DB_NAME') .
                ";charset=utf8";

            // 2. Creamos la instancia de PDO
            $this->conexion = new PDO(
                $dsn,
                getenv('DB_USER'),
                getenv('DB_PASS'),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );

            return $this->conexion;
        } catch (PDOException $e) {
            // No mostrar el error al usuario - solo loguear
            $this->logError("PDOException: " . $e->getMessage());
            return null;
        } catch (Exception $e) {
            $this->logError("Exception: " . $e->getMessage());
            return null;
        }
    }
}
