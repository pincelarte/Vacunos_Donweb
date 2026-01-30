<?php
require_once __DIR__ . '/../back/models/Vacuno.php';

echo "<h2>Iniciando Pruebas de QA para Vacunos</h2>";

// 1. Prueba de existencia (Caravana Duplicada) [cite: 2026-01-28]
$caravana_prueba = "1"; // Usamos la que ya tenés en la foto [cite: 2026-01-28]
echo "Probando si la caravana $caravana_prueba existe... ";

// Aquí llamaremos a una función que crearemos en el modelo [cite: 2026-01-28]
if (Vacuno::existe($caravana_prueba)) {
    echo "<b style='color:red;'>DETECTADO:</b> La caravana ya existe. ¡Bien! El sistema la frenó.";
} else {
    echo "<b style='color:green;'>LIBRE:</b> La caravana no existe, se puede cargar.";
}

echo "<hr>";

// 2. Prueba de Limpieza (QA de eliminación) [cite: 2026-01-28]
echo "Probando eliminación de caravana de prueba... ";
if (Vacuno::eliminar($caravana_prueba)) {
    echo "<b style='color:blue;'>ÉXITO:</b> Registro eliminado correctamente.";
} else {
    echo "<b style='color:orange;'>FALLO:</b> No se pudo eliminar (quizás ya no estaba).";
}
