<?php
// Importamos la clase que queremos testear
require_once __DIR__ . '/../back/config/Conexion.php';

class ConexionTest
{
    /**
     * Orquestador de la auditoría.
     */
    public function correr()
    {
        echo "Iniciando auditoría de calidad...\n";
        echo "---------------------------------\n";

        // 1. Verificación física
        $this->testArchivoEnvExiste();

        // 2. Carga de infraestructura
        $db = new Conexion();

        // 3. Auditoría de datos
        $this->testVariablesEntornoCargadas();

        // 4. Prueba funcional
        $this->testConexionPdo($db);
    }

    private function testArchivoEnvExiste()
    {
        $ruta = __DIR__ . '/../.env';
        if (file_exists($ruta)) {
            echo "[PASS] Archivo .env encontrado.\n";
        } else {
            echo "[FAIL] El archivo .env no existe en la raíz.\n";
        }
    }

    private function testVariablesEntornoCargadas()
    {
        $obligatorias = ['DB_HOST', 'DB_NAME', 'DB_USER'];
        $faltantes = [];

        foreach ($obligatorias as $v) {
            $valor = getenv($v);
            if ($valor === false || trim($valor) === "") {
                $faltantes[] = $v;
            }
        }

        if (empty($faltantes)) {
            echo "[PASS] Variables críticas (Host, DB, User) cargadas.\n";
            $pass = getenv('DB_PASS');
            if ($pass === false || trim($pass) === "") {
                echo "[AVISO] La base de datos no tiene contraseña (Aceptable en local/XAMPP).\n";
            }
        } else {
            echo "[FAIL] Faltan variables obligatorias: " . implode(", ", $faltantes) . "\n";
        }
    }

    private function testConexionPdo($db)
    {
        $con = $db->conectar();
        if ($con instanceof PDO) {
            echo "[PASS] La conexión devuelve un objeto PDO válido.\n";
        } else {
            echo "[FAIL] La conexión no pudo establecerse.\n";
        }
    }
} // <--- Aquí faltaba cerrar la clase

// --- Punto de ejecución (Fuera de la clase) ---
$auditoria = new ConexionTest();
$auditoria->correr();
