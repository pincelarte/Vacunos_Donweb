<?php
require_once __DIR__ . '/../models/Establecimiento.php';
$modelo = new Establecimiento();

/**
 * Función helper para sanitizar entradas y prevenir XSS
 */
function sanitizeInput($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Función helper para validar que un ID sea numérico y positivo
 */
function validateId($id)
{
    return filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
}

/**
 * Función helper para redirigir de forma segura
 */
function safeRedirect($mensaje)
{
    // Solo permitimos mensajes seguros conhecidos
    $mensajesPermitidos = ['ok', 'error', 'eliminado', 'actualizado'];
    $msg = in_array($mensaje, $mensajesPermitidos) ? $mensaje : 'error';
    header("Location: ../../front/gestion.php?mensaje=" . $msg);
    exit();
}

// BLOQUE 1: Para Guardar (Método POST) [cite: 2026-01-28]
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Solo si vienen los datos de creación, ejecutamos esto [cite: 2026-01-28]
    if (
        isset($_POST['nombre_est']) && isset($_POST['ubicacion_est']) &&
        isset($_POST['accion']) && $_POST['accion'] === 'crear'
    ) {

        // Validar que no estén vacíos
        $nombre = sanitizeInput($_POST['nombre_est']);
        $ubicacion = sanitizeInput($_POST['ubicacion_est']);

        // Validar longitud máxima
        if (strlen($nombre) > 20 || strlen($ubicacion) > 20) {
            safeRedirect('error');
        }

        // Validar que no estén vacíos después de sanitizar
        if (empty($nombre) || empty($ubicacion)) {
            safeRedirect('error');
        }

        if ($modelo->crear($nombre, $ubicacion)) {
            safeRedirect('ok');
        } else {
            safeRedirect('error');
        }
    }
}

// BLOQUE 2: Para Eliminar (Método GET) [cite: 2026-01-28]
// Fíjate que este bloque está AFUERA de las llaves del POST [cite: 2026-01-24]
if (isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
    // Validar que exista y sea válido el ID
    if (!isset($_GET['id']) || !validateId($_GET['id'])) {
        safeRedirect('error');
    }

    $id = (int)$_GET['id'];

    if ($modelo->eliminar($id)) {
        safeRedirect('eliminado');
    } else {
        safeRedirect('error');
    }
}

// 1. Detectamos si la acción es 'actualizar' [cite: 2026-01-28]
if (isset($_POST['accion']) && $_POST['accion'] == 'actualizar') {
    // Validar que existan todos los campos necesarios
    if (!isset($_POST['id_est']) || !isset($_POST['nuevo_nombre'])) {
        safeRedirect('error');
    }

    // Validar ID
    $id = validateId($_POST['id_est']);
    if (!$id) {
        safeRedirect('error');
    }

    $nuevoNombre = sanitizeInput($_POST['nuevo_nombre']);

    // Validar longitud
    if (empty($nuevoNombre) || strlen($nuevoNombre) > 20) {
        safeRedirect('error');
    }

    // 2. Le pedimos al modelo (el cocinero) que haga el trabajo [cite: 2026-01-28]
    if ($modelo->actualizar($id, $nuevoNombre)) {
        // 3. Si salió bien, volvemos a la gestión con un mensaje de éxito [cite: 2026-01-28]
        safeRedirect('ok');
    } else {
        safeRedirect('error');
    }
}
