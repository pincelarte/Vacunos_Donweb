<?php
require_once __DIR__ . '/../models/Establecimiento.php';

// Verificamos si los datos vienen por el método POST (envío de formulario) [cite: 2026-01-28]
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Guardamos lo que escribió el usuario en variables [cite: 2026-01-28]
    $nombre = $_POST['nombre_est'];
    $ubicacion = $_POST['ubicacion_est'];

    // Creamos el objeto del modelo que ya tenés listo [cite: 2026-01-28]
    $modelo = new Establecimiento();

    // Intentamos crear el registro [cite: 2026-01-28]
    if ($modelo->crear($nombre, $ubicacion)) {
        // header (encabezado) / Location (ubicación): lo devuelve a la gestión [cite: 2026-01-28]
        header("Location: ../../front/gestion.php?mensaje=ok");
        exit();
    } else {
        echo "Hubo un error al guardar.";
    }
}
