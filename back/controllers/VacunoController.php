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

if (isset($_GET['accion']) && $_GET['accion'] == 'eliminar') {
    $caravana = sanitizeInput($_GET['caravana']);
    $id_est = filter_var($_GET['id_est'], FILTER_VALIDATE_INT);
    if (Vacuno::eliminar($caravana)) {
        safeRedirectVerVacas($id_est, 'exito', 'eliminado');
    }
    exit();
}

if (isset($_GET['accion']) && $_GET['accion'] == 'eliminar_pesaje') {
    $id_pesaje = filter_var($_GET['id_pesaje'], FILTER_VALIDATE_INT);
    $caravana = sanitizeInput($_GET['caravana']);
    if ($id_pesaje && Pesaje::eliminar($id_pesaje)) {
        header("Location: ../../front/historial_vaca.php?caravana=" . $caravana);
        exit();
    }
    header("Location: ../../front/historial_vaca.php?caravana=" . $caravana . "&error=1");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? 'crear';
    $id_est = filter_var($_POST['id_establecimiento'], FILTER_VALIDATE_INT);

    if ($accion === 'editar') {
        $caravana_original = sanitizeInput($_POST['caravana_original']);
        $peso = (float)$_POST['peso'];
        $cantidad = (int)$_POST['cantidad_edad'];
        $unidad = $_POST['unidad_edad'] ?? 'months';

        $fecha_ref = new DateTime();
        $fecha_ref->modify("-$cantidad $unidad");
        $nueva_fecha_inicio = $fecha_ref->format('Y-m-d');

        $vaca = new Vacuno("", $caravana_original, "", $nueva_fecha_inicio, $peso, $id_est);
        if ($vaca->actualizar($caravana_original)) {
            safeRedirectVerVacas($id_est, 'exito', 'editado');
        }
    } elseif ($accion === 'registrar_pesaje_produccion') {
        // --- NUEVA ACCIÓN INDEPENDIENTE ---
        $caravana = sanitizeInput($_POST['caravana']);
        $nuevo_peso = (float)$_POST['nuevo_peso'];

        $p = new Pesaje();
        if ($p->registrar($caravana, $nuevo_peso)) {
            $vaca = new Vacuno("", $caravana, "", "", $nuevo_peso, $id_est);
            $vaca->actualizarPeso($caravana);
            header("Location: ../../front/historial_vaca.php?caravana=" . $caravana);
            exit();
        }
    } else {
        // --- SECCIÓN CREAR ---
        $caravana = sanitizeInput($_POST['caravana']);
        $peso = (float)$_POST['peso'];
        $tipo = sanitizeInput($_POST['tipo']);
        $raza = sanitizeInput($_POST['raza'] ?? '');

        // Validar longitud máxima de caravan (8 caracteres)
        if (strlen($caravana) > 8) {
            safeRedirectVerVacas($id_est, 'error', 'caravana_larga');
        }

        if (Vacuno::existe($caravana)) {
            safeRedirectVerVacas($id_est, 'error', 'duplicado');
        } else {
            $cantidad = (int)($_POST['cantidad_edad'] ?? 0);
            $unidad = $_POST['unidad_edad'] ?? 'months';
            $fecha_ref = new DateTime();
            $fecha_ref->modify("-$cantidad $unidad");
            $fecha_inicio = $fecha_ref->format('Y-m-d');

            $vaca = new Vacuno($tipo, $caravana, $raza, $fecha_inicio, $peso, $id_est);
            if ($vaca->insertar()) {
                $p = new Pesaje();
                $p->registrar($caravana, $peso);
                safeRedirectVerVacas($id_est, 'exito', 'creado');
            }
        }
    }
}
