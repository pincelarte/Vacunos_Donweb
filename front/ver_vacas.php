<?php
session_start();

/**
 * Configuración de seguridad de sesión
 */
ini_set('session.cookie_httponly', 1);

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

// Validar y sanitizar el ID del establecimiento
$id_establecimiento = null;
if (isset($_GET['id'])) {
    $id_establecimiento = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if (!$id_establecimiento || $id_establecimiento < 1) {
        header("Location: gestion.php?mensaje=error");
        exit();
    }
}

if (!$id_establecimiento) {
    header("Location: gestion.php?mensaje=error");
    exit();
}

require_once __DIR__ . '/../back/models/Vacuno.php';
require_once __DIR__ . '/../back/models/Establecimiento.php';

$modeloEst = new Establecimiento();
$datosEst = $modeloEst->obtenerPorId($id_establecimiento);
$nombreEst = $datosEst['nombre'] ?? 'Desconocido';

$listaVacas = Vacuno::listarPorEstablecimiento($id_establecimiento);

// Validar mensajes
$erroresValidos = ['duplicado', 'peso_bajo', 'peso_alto', 'general', 'caravana_larga'];
$exitosValidos = ['creado', 'editado', 'eliminado'];

$error = null;
$exito = null;

if (isset($_GET['error']) && in_array($_GET['error'], $erroresValidos)) {
    $error = $_GET['error'];
}

if (isset($_GET['exito']) && in_array($_GET['exito'], $exitosValidos)) {
    $exito = $_GET['exito'];
}

$nombreEstSafe = escapeHtml($nombreEst);

$mensajeSilicio = '';
if ($error === 'duplicado') {
    $mensajeSilicio = '<span style="color: red;"><b>¡Epa, amigo!</b></span> Ese número de caravana ya lo tenemos anotado.';
} elseif ($error === 'peso_bajo') {
    $mensajeSilicio = '<span style="color: red;"><b>¡Atención!</b></span> ¿Seguro que pesa menos de 10 kg? Revise la balanza.';
} elseif ($error === 'peso_alto') {
    $mensajeSilicio = '<span style="color: red;"><b>¡Epa, amigo!</b></span><br>¡Ese animal es un gigante! ¡Saque el pie de la balanza y vuelva a pesar!!!';
} elseif ($error === 'caravana_larga') {
    $mensajeSilicio = '<span style="color: red;"><b>¡Atención!</b></span> La caravana no puede tener más de 8 caracteres.';
} elseif ($exito === 'creado') {
    $mensajeSilicio = '<span style="color: green;"><b>¡Lindo ejemplar!</b></span> Ya anoté al nuevo vacuno en el cuaderno.';
} elseif ($exito === 'editado') {
    $mensajeSilicio = '<span style="color: blue;"><b>¡Listo!</b></span> Ya actualicé los datos del animal como pidió.';
} elseif ($exito === 'eliminado') {
    $mensajeSilicio = '<span style="color: orange;"><b>¡Despachado!</b></span> El animal ya no figura más en nuestro cuaderno.';
} elseif (empty($listaVacas)) {
    $mensajeSilicio = '¡Buenas ' . ucfirst(escapeHtml($_SESSION['usuario'])) . '! Aún no tengo nada de ganado anotado en mi cuaderno.';
} else {
    $mensajeSilicio = '¡Vea, amigo ' . ucfirst(escapeHtml($_SESSION['usuario'])) . '! Aquí tenemos el detalle de nuestra hacienda.';
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Vacunos - <?php echo $nombreEstSafe; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .contenedor-asistente {
            position: absolute;
            top: 10px;
            right: 0;
            z-index: 1000;
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        .img-silicio {
            width: 300px;
            height: auto;
            transform: scaleX(-1);
            filter: drop-shadow(3px 3px 5px rgba(0, 0, 0, 0.3));
        }

        .burbuja-silicio {
            background: #ffffff;
            border: 2px solid #2c3e50;
            border-radius: 15px;
            padding: 8px 12px;
            margin-right: -55px;
            max-width: 280px;
            box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);
            font-size: 0.85rem;
            position: relative;
            z-index: 1001;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-4">
        <h1><?php echo $nombreEstSafe; ?></h1>
        <a href="gestion.php" class="btn btn-secondary mb-3">Volver a Gestión</a>
        <hr>

        <div class="card my-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Registrar Nuevo Vacuno</h5>
                <form action="../back/controllers/VacunoController.php" method="POST" class="row g-3">
                    <input type="hidden" name="id_establecimiento" value="<?php echo $id_establecimiento; ?>">

                    <div class="col-md-3">
                        <label class="form-label text-muted small">Número de Caravana</label>
                        <input type="text" name="caravana" class="form-control" placeholder="Ej: A102" required pattern="[A-Za-z0-9]{1,8}" maxlength="8">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted small">Tipo de Animal</label>
                        <select name="tipo" class="form-select">
                            <?php foreach (Vacuno::getTipos() as $t): ?>
                                <option value="<?php echo escapeHtml($t); ?>"><?php echo escapeHtml($t); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted small">Raza</label>
                        <select name="raza" class="form-select">
                            <?php foreach (Vacuno::getRazas() as $r): ?>
                                <option value="<?php echo escapeHtml($r); ?>"><?php echo escapeHtml($r); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted small">Edad Actual</label>
                        <div class="input-group">
                            <input type="number" name="cantidad_edad" class="form-control" placeholder="0" min="0" required>
                            <select name="unidad_edad" class="form-select">
                                <option value="days">Días</option>
                                <option value="months" selected>Meses</option>
                                <option value="years">Años</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-muted small">Peso Inicial (kg)</label>
                        <input type="number" name="peso" class="form-control" placeholder="10 - 999 kg" required>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 py-2">💾 Anotar en Cuaderno</button>
                    </div>
                </form>
            </div>
        </div>

        <table class="table table-hover bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>Num</th>
                    <th>Tipo</th>
                    <th>Raza</th>
                    <th>Edad</th>
                    <th>Peso</th>
                    <th>Histo</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaVacas)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No hay vacunos registrados en este establecimiento.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($listaVacas as $vaca): ?>
                        <?php
                        // LÓGICA DE CÁLCULO DINÁMICO [cite: 2026-01-31]
                        $fecha_nac = new DateTime($vaca['edad']);
                        $hoy = new DateTime();
                        $dif = $hoy->diff($fecha_nac);
                        $texto_edad = $dif->format('%y años, %m meses');
                        ?>
                        <tr>
                            <td><?php echo escapeHtml($vaca['caravana']); ?></td>
                            <td><?php echo escapeHtml($vaca['tipo']); ?></td>
                            <td><?php echo escapeHtml($vaca['raza']); ?></td>
                            <td><strong><?php echo $texto_edad; ?></strong></td>
                            <td><?php echo escapeHtml($vaca['peso_actual']); ?> kg</td>
                            <td class="text-center">
                                <a href="historial_vaca.php?caravana=<?php echo urlencode($vaca['caravana']); ?>"
                                    title="Ver historial de pesajes"
                                    style="text-decoration: none; font-size: 1.2rem;">
                                    🔍
                                </a>
                            </td>
                            <td>
                                <a href="editar_vaca.php?caravana=<?php echo urlencode($vaca['caravana']); ?>" class="btn btn-sm btn-warning">
                                    Editar
                                </a>

                                <a href="../back/controllers/VacunoController.php?accion=eliminar&caravana=<?php echo urlencode($vaca['caravana']); ?>&id_est=<?php echo $id_establecimiento; ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('¿Seguro que quiere sacar este animal del sistema?')">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="contenedor-asistente">
        <div class="burbuja-silicio">
            <b>Don Silicio dice:</b><br>
            <?php echo $mensajeSilicio; ?>
        </div>
        <img src="assets/img/DonSilicio-indice.png" class="img-silicio" alt="Don Silicio">
    </div>
</body>

</html>