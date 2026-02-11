<?php
require_once __DIR__ . '/../models/Vacuno.php';
require_once __DIR__ . '/../models/Pesaje.php';

function sanitizeInput($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function safeRedirectVerVacas($id_est, $tipo, $valor = null)
{
    $id = filter_var($id_est, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id) {
        $query = ($valor !== null) ? "$tipo=$valor" : "$tipo=1";
        header("Location: ../../front/ver_vacas.php?id=$id&$query");
        exit();
    }
    header("Location: ../../front/gestion.php?mensaje=error");
    exit();
}

// --- CAMBIO: ELIMINAR VACA POR ID ---
if (isset($_GET['accion']) && $_GET['accion'] == 'eliminar') {
    $id_vaca = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    $id_est = filter_var($_GET['id_est'], FILTER_VALIDATE_INT);
    if (Vacuno::eliminar($id_vaca)) {
        safeRedirectVerVacas($id_est, 'exito', 'eliminado');
    }
    exit();
}

// --- SE MANTIENE: ELIMINAR PESAJE ---
if (isset($_GET['accion']) && $_GET['accion'] == 'eliminar_pesaje') {
    $id_pesaje = filter_var($_GET['id_pesaje'], FILTER_VALIDATE_INT);
    $id_vaca = filter_var($_GET['id_vaca'], FILTER_VALIDATE_INT);
    if ($id_pesaje && Pesaje::eliminar($id_pesaje)) {
        header("Location: ../../front/historial_vaca.php?id=" . $id_vaca);
        exit();
    }
    header("Location: ../../front/historial_vaca.php?id=" . $id_vaca . "&error=1");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? 'crear';
    $id_est = filter_var($_POST['id_establecimiento'], FILTER_VALIDATE_INT);

    if ($accion === 'editar') {
        $id_vaca = filter_var($_POST['id_vaca'], FILTER_VALIDATE_INT);
        $peso = (float)$_POST['peso'];
        $historial = sanitizeInput($_POST['historial'] ?? '');
        $cantidad = (int)$_POST['cantidad_edad'];
        $unidad = $_POST['unidad_edad'] ?? 'months';

        $fecha_ref = new DateTime();
        $fecha_ref->modify("-$cantidad $unidad");
        $nueva_fecha_inicio = $fecha_ref->format('Y-m-d');

        $vaca = new Vacuno("", "", "", $nueva_fecha_inicio, $peso, $id_est);
        $vaca->setHistorial($historial);  // Agregar el historial
        if ($vaca->actualizar($id_vaca)) {
            safeRedirectVerVacas($id_est, 'exito', 'editado');
        }
    } elseif ($accion === 'registrar_pesaje_produccion') {
        // --- REGISTRAR PESAJE: Ahora enviamos el id_vaca ---
        $caravana = sanitizeInput($_POST['caravana']);
        $id_vaca = filter_var($_POST['id_vaca'], FILTER_VALIDATE_INT);
        $nuevo_peso = (float)$_POST['nuevo_peso'];

        $p = new Pesaje();
        // Traduciendo: Pasamos el ID único para que no se mezclen los datos [cite: 2026-01-24]
        if ($p->registrar($id_vaca, $caravana, $nuevo_peso)) {
            $vaca = new Vacuno("", $caravana, "", "", $nuevo_peso, $id_est);
            $vaca->actualizarPeso($id_vaca);
            header("Location: ../../front/historial_vaca.php?id=" . $id_vaca);
            exit();
        }
    } else {
        // --- SECCIÓN CREAR ---
        $caravana = sanitizeInput($_POST['caravana']);
        $peso = (float)$_POST['peso'];
        $tipo = sanitizeInput($_POST['tipo']);
        $raza = sanitizeInput($_POST['raza'] ?? '');

        if (strlen($caravana) > 8) {
            safeRedirectVerVacas($id_est, 'error', 'caravana_larga');
        }

        $cantidad = (int)($_POST['cantidad_edad'] ?? 0);
        $unidad = $_POST['unidad_edad'] ?? 'months';
        $fecha_ref = new DateTime();
        $fecha_ref->modify("-$cantidad $unidad");
        $fecha_inicio = $fecha_ref->format('Y-m-d');

        $vaca = new Vacuno($tipo, $caravana, $raza, $fecha_inicio, $peso, $id_est);

        // Al insertar una vaca nueva, también vinculamos su primer pesaje por ID
        if ($vaca->insertar()) {
            $id_vaca_nueva = $vaca->getUltimoId(); // Necesitás esta función en tu modelo Vacuno
            $p = new Pesaje();
            $p->registrar($id_vaca_nueva, $caravana, $peso);
            safeRedirectVerVacas($id_est, 'exito', 'creado');
        }
    }
}
