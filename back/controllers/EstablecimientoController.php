<?php
require_once __DIR__ . '/../models/Establecimiento.php';
$modelo = new Establecimiento();

// BLOQUE 1: Para Guardar (Método POST) [cite: 2026-01-28]
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_est'];
    $ubicacion = $_POST['ubicacion_est'];

    if ($modelo->crear($nombre, $ubicacion)) {
        header("Location: ../../front/gestion.php?mensaje=ok");
        exit();
    } else {
        echo "Hubo un error al guardar.";
    }
}

// BLOQUE 2: Para Eliminar (Método GET) [cite: 2026-01-28]
// Fíjate que este bloque está AFUERA de las llaves del POST [cite: 2026-01-24]
if (isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
    $id = $_GET['id'];

    if ($modelo->eliminar($id)) {
        header("Location: ../../front/gestion.php?mensaje=eliminado");
    } else {
        header("Location: ../../front/gestion.php?mensaje=error");
    }
    exit();
}
