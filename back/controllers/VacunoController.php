<?php
require_once __DIR__ . '/../models/Vacuno.php';

// 1. Lógica para ELIMINAR (Llega por GET desde el enlace de la tabla) [cite: 2026-01-28]
if (isset($_GET['accion']) && $_GET['accion'] == 'eliminar') {
    $caravana = $_GET['caravana'];
    $id_est = $_GET['id_est'];

    // Llamamos al método estático del modelo [cite: 2026-01-28]
    if (Vacuno::eliminar($caravana)) {
        header("Location: ../../front/ver_vacas.php?id=" . $id_est);
        exit();
    } else {
        echo "Error al intentar despachar al animal.";
        exit();
    }
}

// 2. Lógica para CREAR o EDITAR (Llega por POST desde el formulario) [cite: 2026-01-28]
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $accion = $_POST['accion'] ?? 'crear';
    $id_est = (int)$_POST['id_establecimiento'];

    if ($accion === 'editar') {
        $caravana_original = $_POST['caravana_original'];
        $vaca = new Vacuno("", $caravana_original, "", (int)$_POST['edad'], (float)$_POST['peso'], $id_est);
        $vaca->actualizarHistorial($_POST['historial']);

        if ($vaca->actualizar($caravana_original)) {
            header("Location: ../../front/ver_vacas.php?id=" . $id_est);
            exit();
        }
    } else {
        $vaca = new Vacuno($_POST['tipo'], $_POST['caravana'], $_POST['raza'], 0, (float)$_POST['peso'], $id_est);

        if ($vaca->insertar()) {
            header("Location: ../../front/ver_vacas.php?id=" . $id_est);
            exit();
        }
    }
}
