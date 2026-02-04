<?php
session_start();

/**
 * Configuración de seguridad de sesión
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Activar en producción si usa HTTPS

// Regenerar ID de sesión cada 30 minutos para prevenir hijacking
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

/**
 * Función helper para escapar HTML y prevenir XSS
 */
function escapeHtml($data)
{
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// 1. Llamamos al modelo para traer los datos [cite: 2026-01-28]
require_once __DIR__ . '/../back/models/Establecimiento.php';
$modelo = new Establecimiento();
$listaEstablecimientos = $modelo->listarTodo();

// Manejo seguro de mensajes con lista blanca
$mensajesValidos = ['ok', 'error', 'eliminado', 'actualizado'];
$mensajeMostrar = '';
if (isset($_GET['mensaje']) && in_array($_GET['mensaje'], $mensajesValidos)) {
    switch ($_GET['mensaje']) {
        case 'ok':
            $mensajeMostrar = '💾 Guardado con éxito.';
            break;
        case 'eliminado':
            $mensajeMostrar = '✅ El establecimiento fue borrado.';
            break;
        case 'actualizado':
            $mensajeMostrar = '✏️ Actualizado con éxito.';
            break;
        case 'error':
            $mensajeMostrar = '❌ Hubo un error.';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión - Don Silicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/estilos.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between">
            <h1>Bienvenido, <?php echo escapeHtml($_SESSION['usuario']); ?></h1>
            <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
        <hr>

        <div class="card my-4">
            <div class="card-body">
                <form action="../back/controllers/EstablecimientoController.php" method="POST" class="row g-3">
                    <input type="hidden" name="accion" value="crear">
                    <div class="col-md-5">
                        <input type="text" name="nombre_est" class="form-control" placeholder="Establecimiento (máx. 20 letras)" maxlength="20" required pattern="[a-zA-Z0-9\s]{1,20}" title="Solo letras, números y espacios (máx 20 caracteres)">
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="ubicacion_est" class="form-control" placeholder="Ubicación (máx. 20 letras)" maxlength="20" required pattern="[a-zA-Z0-9\s]{1,20}" title="Solo letras, números y espacios (máx 20 caracteres)">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
        <?php if (!empty($mensajeMostrar)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $mensajeMostrar; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped bg-white shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Nombre</th>
                        <th class="text-center">Ubicación</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($listaEstablecimientos)): ?>
                        <?php foreach ($listaEstablecimientos as $est): ?>
                            <tr>
                                <td class="text-center"><?php echo escapeHtml($est['id']); ?></td>
                                <td class="text-center"><?php echo escapeHtml($est['nombre']); ?></td>
                                <td class="text-center"><?php echo escapeHtml($est['ubicacion']); ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="ver_vacas.php?id=<?php echo urlencode($est['id']); ?>" class="btn btn-sm btn-success">Seleccionar</a>

                                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                            ⚙️
                                        </button>

                                        <ul class="dropdown-menu shadow">
                                            <li>
                                                <a class="dropdown-item" href="editar_establecimiento.php?id=<?php echo urlencode($est['id']); ?>">✏️ Editar</a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <button class="dropdown-item text-danger" onclick="confirmarBorrado(<?php echo (int)$est['id']; ?>, '<?php echo addslashes(escapeHtml($est['nombre'])); ?>')">
                                                    🗑️ Borrar
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No hay establecimientos cargados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <script>
            function confirmarBorrado(id, nombre) {
                // Pedimos al usuario que escriba 'borrar' para confirmar [cite: 2026-01-24]
                let confirmacion = prompt("Para eliminar el establecimiento '" + nombre + "', escriba 'borrar':");

                if (confirmacion === 'borrar') {
                    // Si escribió bien, lo mandamos al controlador [cite: 2026-01-28]
                    window.location.href = "../back/controllers/EstablecimientoController.php?accion=eliminar&id=" + encodeURIComponent(id);
                } else if (confirmacion !== null) {
                    alert("Palabra incorrecta. No se eliminó el establecimiento.");
                }
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>