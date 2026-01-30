<?php
require_once __DIR__ . '/../models/Vacuno.php';

// 1. Lógica para ELIMINAR
if (isset($_GET['accion']) && $_GET['accion'] == 'eliminar') {
    $caravana = $_GET['caravana'];
    $id_est = $_GET['id_est'];

    if (Vacuno::eliminar($caravana)) {
        header("Location: ../../front/ver_vacas.php?id=" . $id_est . "&exito=eliminado");
        exit();
    } else {
        echo "Error al intentar despachar al animal.";
        exit();
    }
}

// 2. Lógica para CREAR o EDITAR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? 'crear';
    $id_est = (int)$_POST['id_establecimiento'];

    if ($accion === 'editar') {
        $caravana_original = $_POST['caravana_original'];
        $peso = (float)$_POST['peso'];

        // Seguridad para EDITAR [cite: 2026-01-28]
        if ($peso < 10) {
            header("Location: ../../front/ver_vacas.php?id=" . $id_est . "&error=peso_bajo");
            exit();
        } elseif ($peso > 999) {
            header("Location: ../../front/ver_vacas.php?id=" . $id_est . "&error=peso_alto");
            exit();
        }

        $vaca = new Vacuno("", $caravana_original, "", (int)$_POST['edad'], $peso, $id_est);
        $vaca->actualizarHistorial($_POST['historial']);

        if ($vaca->actualizar($caravana_original)) {
            header("Location: ../../front/ver_vacas.php?id=" . $id_est . "&exito=editado");
            exit();
        }
    } else {
        // --- LÓGICA DE CREAR CON QA --- [cite: 2026-01-28]
        $caravana = $_POST['caravana'];
        $peso = (float)$_POST['peso'];

        if (Vacuno::existe($caravana)) {
            header("Location: ../../front/ver_vacas.php?id=" . $id_est . "&error=duplicado");
            exit();
        } elseif ($peso < 10) {
            header("Location: ../../front/ver_vacas.php?id=" . $id_est . "&error=peso_bajo");
            exit();
        } elseif ($peso > 999) {
            header("Location: ../../front/ver_vacas.php?id=" . $id_est . "&error=peso_alto");
            exit();
        } else {
            $vaca = new Vacuno($_POST['tipo'], $caravana, $_POST['raza'], 0, $peso, $id_est);
            if ($vaca->insertar()) {
                header("Location: ../../front/ver_vacas.php?id=" . $id_est . "&exito=creado");
                exit();
            }
        }
    }
}
