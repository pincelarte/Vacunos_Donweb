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
        // Usar formato original: &exito=creado o &error=peso_bajo
        if ($valor !== null) {
            header("Location: ../../front/ver_vacas.php?id=" . $id . "&" . $tipo . "=" . $valor);
        } else {
            header("Location: ../../front/ver_vacas.php?id=" . $id . "&" . $tipo . "=1");
        }
        exit();
    }
    // Si el ID no es válido, redirigir a gestión
    header("Location: ../../front/gestion.php?mensaje=error");
    exit();
}

// 1. Lógica para ELIMINAR
if (isset($_GET['accion']) && $_GET['accion'] == 'eliminar') {
    // Validar que existan los parámetros necesarios
    if (!isset($_GET['caravana']) || !isset($_GET['id_est'])) {
        header("Location: ../../front/gestion.php?mensaje=error");
        exit();
    }

    // Sanitizar inputs
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
    // Validar que exista id_establecimiento
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
        // Validar campos requeridos para editar
        if (!isset($_POST['caravana_original']) || !isset($_POST['peso']) || !isset($_POST['edad'])) {
            safeRedirectVerVacas($id_est, 'error', 'general');
        }

        $caravana_original = sanitizeInput($_POST['caravana_original']);
        $peso = (float)$_POST['peso'];
        $edad = (int)$_POST['edad'];

        // Seguridad para EDITAR [cite: 2026-01-28]
        if ($peso < 10) {
            safeRedirectVerVacas($id_est, 'error', 'peso_bajo');
        } elseif ($peso > 999) {
            safeRedirectVerVacas($id_est, 'error', 'peso_alto');
        }

        // Validar edad razonable
        if ($edad < 0 || $edad > 30) {
            safeRedirectVerVacas($id_est, 'error', 'general');
        }

        // Sanitizar historial
        $historial = isset($_POST['historial']) ? sanitizeInput($_POST['historial']) : '';

        $vaca = new Vacuno("", $caravana_original, "", $edad, $peso, $id_est);
        $vaca->actualizarHistorial($historial);

        if ($vaca->actualizar($caravana_original)) {
            safeRedirectVerVacas($id_est, 'exito', 'editado');
        }
    } else {
        // --- LÓGICA DE CREAR CON QA --- [cite: 2026-01-28]
        // Validar campos requeridos
        if (!isset($_POST['caravana']) || !isset($_POST['peso']) || !isset($_POST['tipo'])) {
            safeRedirectVerVacas($id_est, 'error', 'general');
        }

        $caravana = sanitizeInput($_POST['caravana']);
        $peso = (float)$_POST['peso'];
        $tipo = sanitizeInput($_POST['tipo']);
        $raza = isset($_POST['raza']) ? sanitizeInput($_POST['raza']) : '';

        // Validar datos
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
            $vaca = new Vacuno($tipo, $caravana, $raza, 0, $peso, $id_est);
            if ($vaca->insertar()) {
                safeRedirectVerVacas($id_est, 'exito', 'creado');
            }
        }
    }
}
