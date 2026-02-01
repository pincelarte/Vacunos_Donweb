<?php
require_once __DIR__ . '/../models/Vacuno.php';

/**
 * Función helper para sanitizar entradas y prevenir XSS
 */
function sanitizeInput($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Función helper para redirigir de forma segura
 * Mantiene compatibilidad con el diseño de Don Silicio
 */
function safeRedirectVerVacas($id_est, $tipo, $valor = null)
{
    $id = filter_var($id_est, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id) {
        if ($valor !== null) {
            header("Location: ../../front/ver_vacas.php?id=" . $id . "&" . $tipo . "=" . $valor);
        } else {
            header("Location: ../../front/ver_vacas.php?id=" . $id . "&" . $tipo . "=1");
        }
        exit();
    }
    header("Location: ../../front/gestion.php?mensaje=error");
    exit();
}

// 1. Lógica para ELIMINAR
if (isset($_GET['accion']) && $_GET['accion'] == 'eliminar') {
    if (!isset($_GET['caravana']) || !isset($_GET['id_est'])) {
        header("Location: ../../front/gestion.php?mensaje=error");
        exit();
    }

    $caravana = sanitizeInput($_GET['caravana']);
    $id_est = filter_var($_GET['id_est'], FILTER_VALIDATE_INT);

    if (!$id_est || empty($caravana)) {
        header("Location: ../../front/gestion.php?mensaje=error");
        exit();
    }

    if (Vacuno::eliminar($caravana)) {
        safeRedirectVerVacas($id_est, 'exito', 'eliminado');
    } else {
        echo "Error al intentar despachar al animal.";
        exit();
    }
}

// 2. Lógica para CREAR o EDITAR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id_establecimiento'])) {
        header("Location: ../../front/gestion.php?mensaje=error");
        exit();
    }

    $accion = isset($_POST['accion']) ? $_POST['accion'] : 'crear';
    $id_est = filter_var($_POST['id_establecimiento'], FILTER_VALIDATE_INT);

    if (!$id_est) {
        safeRedirectVerVacas(0, 'error', 'general');
    }

    if ($accion === 'editar') {
        // --- LÓGICA DE EDITAR ACTUALIZADA ---
        if (!isset($_POST['caravana_original']) || !isset($_POST['peso']) || !isset($_POST['cantidad_edad'])) {
            safeRedirectVerVacas($id_est, 'error', 'general');
        }

        $caravana_original = sanitizeInput($_POST['caravana_original']);
        $peso = (float)$_POST['peso'];

        // Recalculamos la fecha de nacimiento para la edición
        $cantidad = (int)$_POST['cantidad_edad'];
        $unidad = $_POST['unidad_edad'] ?? 'months';

        $fecha_ref = new DateTime();
        $fecha_ref->modify("-$cantidad $unidad");
        $nueva_fecha_inicio = $fecha_ref->format('Y-m-d');

        // Seguridad de peso
        if ($peso < 10) {
            safeRedirectVerVacas($id_est, 'error', 'peso_bajo');
        } elseif ($peso > 999) {
            safeRedirectVerVacas($id_est, 'error', 'peso_alto');
        }

        $historial = isset($_POST['historial']) ? sanitizeInput($_POST['historial']) : '';

        // Creamos el objeto con la nueva fecha calculada
        $vaca = new Vacuno("", $caravana_original, "", $nueva_fecha_inicio, $peso, $id_est);
        $vaca->actualizarHistorial($historial);

        if ($vaca->actualizar($caravana_original)) {
            safeRedirectVerVacas($id_est, 'exito', 'editado');
        }
    } else {
        // --- LÓGICA DE CREAR ---
        if (!isset($_POST['caravana']) || !isset($_POST['peso']) || !isset($_POST['tipo'])) {
            safeRedirectVerVacas($id_est, 'error', 'general');
        }

        $caravana = sanitizeInput($_POST['caravana']);
        $peso = (float)$_POST['peso'];
        $tipo = sanitizeInput($_POST['tipo']);
        $raza = isset($_POST['raza']) ? sanitizeInput($_POST['raza']) : '';

        if (empty($caravana)) {
            safeRedirectVerVacas($id_est, 'error', 'general');
        }

        if (Vacuno::existe($caravana)) {
            safeRedirectVerVacas($id_est, 'error', 'duplicado');
        } elseif ($peso < 10) {
            safeRedirectVerVacas($id_est, 'error', 'peso_bajo');
        } elseif ($peso > 999) {
            safeRedirectVerVacas($id_est, 'error', 'peso_alto');
        } else {
            // Cálculo de edad dinámica para el alta
            $cantidad = isset($_POST['cantidad_edad']) ? (int)$_POST['cantidad_edad'] : 0;
            $unidad = isset($_POST['unidad_edad']) ? $_POST['unidad_edad'] : 'months';

            $fecha_ref = new DateTime();
            $fecha_ref->modify("-$cantidad $unidad");
            $fecha_inicio = $fecha_ref->format('Y-m-d');

            $vaca = new Vacuno($tipo, $caravana, $raza, $fecha_inicio, $peso, $id_est);

            if ($vaca->insertar()) {
                safeRedirectVerVacas($id_est, 'exito', 'creado');
            }
        }
    }
}
