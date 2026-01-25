<?php
// 1. Incluimos la clase
require_once 'back/config/Conexion.php';

// 2. Instanciamos el objeto (esto dispara el constructor y lee el .env)
$db = new Conexion();

// 3. Intentamos conectar
$con = $db->conectar();

// 4. Verificamos el resultado
if ($con) {
    echo "¡Conexión exitosa! El túnel con MariaDB está abierto.";
} else {
    echo "La conexión falló. Revisá los datos del .env";
}