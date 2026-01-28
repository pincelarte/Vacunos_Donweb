<?php
require_once __DIR__ . '/../models/Vacuno.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Detectamos qué acción queremos realizar [cite: 2026-01-28]
    $accion = $_POST['accion'] ?? 'crear';
    $id_est = (int)$_POST['id_establecimiento'];

    if ($accion === 'editar') {
        // Lógica para EDITAR [cite: 2026-01-28]
        $caravana_original = $_POST['caravana_original'];

        // Creamos el objeto con los nuevos datos [cite: 2026-01-24]
        $vaca = new Vacuno("", $caravana_original, "", (int)$_POST['edad'], (float)$_POST['peso'], $id_est);
        // Le asignamos el historial manualmente ya que el constructor no lo recibe [cite: 2026-01-24]
        $vaca->actualizarHistorial($_POST['historial']);

        if ($vaca->actualizar($caravana_original)) {
            header("Location: ../../front/ver_vacas.php?id=" . $id_est);
            exit();
        }
    } else {
        // Lógica para CREAR (la que ya tenías) [cite: 2026-01-28]
        $vaca = new Vacuno($_POST['tipo'], $_POST['caravana'], $_POST['raza'], 0, (float)$_POST['peso'], $id_est);

        if ($vaca->insertar()) {
            header("Location: ../../front/ver_vacas.php?id=" . $id_est);
            exit();
        }
    }
}
