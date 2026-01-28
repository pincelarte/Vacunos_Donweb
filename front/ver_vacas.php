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
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>