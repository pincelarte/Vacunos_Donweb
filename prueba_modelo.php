<?php
// 1. Importamos el modelo (Asegúrate de que la ruta sea correcta)
require_once __DIR__ . '/back/models/Vacuno.php';

echo "--- Probando Inserción de Vacuno ---\n";

try {
    // 2. Creamos un objeto con datos de prueba
    // Estos datos simularían lo que viene de tus <select> en el futuro
    $tipo = "Vaca";
    $caravana = "TR-001"; // Si ya existe en tu BD, cámbiala por otra (ej: TR-002)
    $raza = "Holando Argentino";
    $edad = 3;
    $peso = 420.50;

    $animal = new Vacuno($tipo, $caravana, $raza, $edad, $peso);

    // 3. Intentamos guardar
    echo "Guardando animal...\n";
    $resultado = $animal->insertar();

    echo "Resultado: " . $resultado . "\n";
} catch (Exception $e) {
    echo "ERROR CRÍTICO: " . $e->getMessage() . "\n";
}

echo "-----------------------------------\n";
