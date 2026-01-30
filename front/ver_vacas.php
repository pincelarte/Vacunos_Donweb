<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$id_establecimiento = $_GET['id'] ?? null;

if (!$id_establecimiento) {
    echo "No se seleccionó ningún establecimiento.";
    exit();
}

require_once __DIR__ . '/../back/models/Vacuno.php';
// Requerimos el modelo de Establecimiento para buscar el nombre [cite: 2026-01-28]
require_once __DIR__ . '/../back/models/Establecimiento.php';

$modeloEst = new Establecimiento();
// Usamos la función obtenerPorId (asegurate de haberla agregado al modelo como vimos antes) [cite: 2026-01-28]
$datosEst = $modeloEst->obtenerPorId($id_establecimiento);
$nombreEst = $datosEst['nombre'] ?? 'Desconocido';

$listaVacas = Vacuno::listarPorEstablecimiento($id_establecimiento);

$error = $_GET['error'] ?? null;
$exito = $_GET['exito'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Vacunos - <?php echo $nombreEst; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .contenedor-asistente {
            position: absolute;
            /* Cambiamos fixed por absolute para que NO baje con el scroll [cite: 2026-01-28] */
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
            /* Esto lo acerca al paisano como lo tenías antes [cite: 2026-01-28] */
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
        <h1><?php echo $nombreEst; ?></h1>
        <a href="gestion.php" class="btn btn-secondary mb-3">Volver a Gestión</a>
        <hr>

        <div class="card my-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Registrar Nuevo Vacuno</h5>
                <form action="../back/controllers/VacunoController.php" method="POST" class="row g-2">
                    <input type="hidden" name="id_establecimiento" value="<?php echo $id_establecimiento; ?>">
                    <div class="col-md-2">
                        <input type="text" name="caravana" class="form-control" placeholder="Caravana" required>
                    </div>
                    <div class="col-md-2">
                        <select name="tipo" class="form-select">
                            <?php foreach (Vacuno::getTipos() as $t): ?>
                                <option value="<?php echo $t; ?>"><?php echo $t; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="raza" class="form-select">
                            <?php foreach (Vacuno::getRazas() as $r): ?>
                                <option value="<?php echo $r; ?>"><?php echo $r; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="peso" class="form-control" placeholder="Peso (kg)">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <table class="table table-hover bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>Caravana</th>
                    <th>Tipo</th>
                    <th>Raza</th>
                    <th>Edad</th>
                    <th>Peso</th>
                    <th>Historial</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaVacas)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No hay vacunos registrados en este establecimiento.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($listaVacas as $vaca): ?>
                        <tr>
                            <td><?php echo $vaca['caravana']; ?></td>
                            <td><?php echo $vaca['tipo']; ?></td>
                            <td><?php echo $vaca['raza']; ?></td>
                            <td><?php echo $vaca['edad']; ?> m</td>
                            <td><?php echo $vaca['peso_actual']; ?> kg</td>
                            <td class="text-center">
                                <?php if (!empty($vaca['historial'])): ?>
                                    <a href="editar_vaca.php?caravana=<?php echo $vaca['caravana']; ?>" style="text-decoration: none;">🔍</a>
                                <?php else: ?>
                                    <small class="text-muted">Sin datos</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="editar_vaca.php?caravana=<?php echo $vaca['caravana']; ?>" class="btn btn-sm btn-warning">
                                    Editar
                                </a>

                                <a href="../back/controllers/VacunoController.php?accion=eliminar&caravana=<?php echo $vaca['caravana']; ?>&id_est=<?php echo $id_establecimiento; ?>"
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

            <?php if ($error === 'duplicado'): ?>
                <span style="color: red;"><b>¡Epa, amigo!</b></span> Ese número de caravana ya lo tenemos anotado.
            <?php elseif ($error === 'peso_bajo'): ?>
                <span style="color: red;"><b>¡Atención!</b></span> ¿Seguro que pesa menos de 10 kg? Revise la balanza.
            <?php elseif ($exito === 'creado'): ?>
                <span style="color: green;"><b>¡Lindo ejemplar!</b></span> Ya anoté al nuevo vacuno en el cuaderno.
            <?php elseif ($exito === 'editado'): ?>
                <span style="color: blue;"><b>¡Listo!</b></span> Ya actualicé los datos del animal como pidió.
            <?php elseif ($exito === 'eliminado'): ?>
                <span style="color: orange;"><b>¡Despachado!</b></span> El animal ya no figura más en nuestro cuaderno.
            <?php elseif (empty($listaVacas)): ?>
                ¡Buenas <?php echo ucfirst($_SESSION['usuario']); ?>! Aún no tengo nada de ganado anotado en mi cuaderno.
            <?php elseif ($error === 'peso_alto'): ?>
                <span style="color: red;"><b>¡Epa, amigo!</b></span><br>
                ¡Ese animal es un gigante! ¡Saque el pie de la balanza y vuelva a pesar!!!
            <?php else: ?>
                ¡Vea, amigo <?php echo ucfirst($_SESSION['usuario']); ?>! Aquí tenemos el detalle de nuestra hacienda.
            <?php endif; ?>
        </div>
        <img src="assets/img/DonSilicio-indice.png" class="img-silicio" alt="Don Silicio">
    </div>
</body>

</html>