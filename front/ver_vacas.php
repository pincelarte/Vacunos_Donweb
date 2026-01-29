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
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Vacunos - <?php echo $nombreEst; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .contenedor-asistente {
            position: fixed;
            top: 10px;
            right: 0;
            /* Ahora lo pegamos al borde derecho [cite: 2026-01-28] */
            z-index: 1000;
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        .img-silicio {
            width: 300px;
            height: auto;
            /* Invertimos la imagen para que mire hacia la izquierda (hacia el texto) [cite: 2026-01-28] */
            transform: scaleX(-1);
            filter: drop-shadow(3px 3px 5px rgba(0, 0, 0, 0.3));
        }

        .burbuja-silicio {
            background: #ffffff;
            border: 2px solid #2c3e50;
            border-radius: 15px;
            padding: 8px 12px;
            /* Ajustá este número: mientras más negativo, más se acerca al paisano [cite: 2026-01-28] */
            margin-right: -55px;
            max-width: 280px;
            box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);
            font-size: 0.85rem;
            position: relative;
            /* Esto asegura que la burbuja quede por "encima" de la imagen [cite: 2026-01-28] */
            z-index: 1001;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-4">
        <h1>Vacunos en: <?php echo $nombreEst; ?></h1>
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
                            <td><small><?php echo $vaca['historial'] ?: 'Sin datos'; ?></small></td>
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

            <?php if (empty($listaVacas)): ?>
                ¡Buenas <?php echo ucfirst($_SESSION['usuario']); ?>!
                Aún no tengo nada de ganado anotado en mi cuaderno.

            <?php else: ?>
                ¡Vea, amigo <?php echo ucfirst($_SESSION['usuario']); ?>!
                Aquí tenemos el detalle de nuestra hacienda.
            <?php endif; ?>

        </div>
        <img src="assets/img/DonSilicio-indice.png" class="img-silicio" alt="Don Silicio">
    </div>
</body>

</html>