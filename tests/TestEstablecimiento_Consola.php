<?php
// Usamos este archivo solo para verificar el motor desde la consola [cite: 2026-01-28]
require_once __DIR__ . '/../back/models/Establecimiento.php';

$modelo = new Establecimiento();

echo "\n--- CHEQUEO DE MOTOR (ARCHIVO NUEVO) ---\n";

$lista = $modelo->listarTodo();

if (is_array($lista)) {
    echo "✅ MOTOR OK: Se encontraron " . count($lista) . " establecimientos.\n";
} else {
    echo "❌ ERROR: El motor no responde.\n";
}




// Prueba de edición
if ($modelo->actualizar(1, "Campo Editado QA")) {
    echo "✅ EDICIÓN OK\n";
} else {
    echo "❌ EDICIÓN FALLIDA\n";
}

// Prueba de eliminación (borramos el que creamos para testear)
if ($modelo->eliminar(1)) {
    echo "✅ ELIMINACIÓN OK\n";
} else {
    echo "❌ ELIMINACIÓN FALLIDA\n";
}