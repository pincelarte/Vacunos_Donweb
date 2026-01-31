<?php
require_once __DIR__ . '/../models/Establecimiento.php';
$modelo = new Establecimiento();

// BLOQUE 1: Para Guardar (Método POST) [cite: 2026-01-28]
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Solo si vienen los datos de creación, ejecutamos esto [cite: 2026-01-28]
    if (isset($_POST['nombre_est']) && isset($_POST['ubicacion_est'])) {
        $nombre = $_POST['nombre_est'];
        $ubicacion = $_POST['ubicacion_est'];

        if ($modelo->crear($nombre, $ubicacion)) {
            header("Location: ../../front/gestion.php?mensaje=ok");
            exit();
        } else {
            header("Location: ../../front/gestion.php?mensaje=error");
            exit();
        }
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

// 1. Detectamos si la acción es 'actualizar' [cite: 2026-01-28]
if (isset($_POST['accion']) && $_POST['accion'] == 'actualizar') {
    $id = $_POST['id_est'];
    $nuevoNombre = $_POST['nuevo_nombre'];

    // 2. Le pedimos al modelo (el cocinero) que haga el trabajo [cite: 2026-01-28]
    if ($modelo->actualizar($id, $nuevoNombre)) {
        // 3. Si salió bien, volvemos a la gestión con un mensaje de éxito [cite: 2026-01-28]
        header("Location: ../../front/gestion.php?mensaje=ok");
    } else {
        header("Location: ../../front/gestion.php?mensaje=error");
    }
    exit();
}