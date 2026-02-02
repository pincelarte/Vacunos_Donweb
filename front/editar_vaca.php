<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

require_once __DIR__ . '/../back/models/Vacuno.php';

// Atrapamos la caravana que viene por la URL
$caravana = $_GET['caravana'] ?? null;
$vaca = Vacuno::obtenerPorCaravana($caravana);

if (!$vaca) {
    echo "Vaca no encontrada.";
    exit();
}

// --- LÓGICA DE DESGLOSE TEMPORAL [cite: 2026-01-31] ---
// Convertimos la fecha de la base de datos en un objeto de tiempo
$fecha_nac = new DateTime($vaca['edad']);
$hoy = new DateTime();
$dif = $hoy->diff($fecha_nac);

// Calculamos los meses totales para el valor por defecto del input
$meses_totales = ($dif->y * 12) + $dif->m;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Vacuno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/estilos.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-warning">
                <h3>Editar Vacuno: <?php echo htmlspecialchars($vaca['caravana']); ?></h3>
            </div>
            <div class="card-body">
                <form action="../back/controllers/VacunoController.php" method="POST">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="caravana_original" value="<?php echo htmlspecialchars($vaca['caravana']); ?>">
                    <input type="hidden" name="id_establecimiento" value="<?php echo $vaca['id_establecimiento']; ?>">

                    <div class="mb-3">
                        <label>Historial / Observaciones:</label>
                        <textarea name="historial" class="form-control" rows="4"><?php echo htmlspecialchars($vaca['historial']); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Peso Actual (kg):</label>
                            <input type="number" step="0.01" name="peso" class="form-control" value="<?php echo $vaca['peso_actual']; ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Edad (Ajustar si es necesario):</label>
                            <div class="input-group">
                                <input type="number" name="cantidad_edad" class="form-control" value="<?php echo $meses_totales; ?>">
                                <select name="unidad_edad" class="form-select">
                                    <option value="days">Días</option>
                                    <option value="months" selected>Meses</option>
                                    <option value="years">Años</option>
                                </select>
                            </div>
                            <small class="text-muted">
                                <?php echo "{$dif->y} años, {$dif->m} meses y {$dif->d} días"; ?>
                            </small>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                    <a href="ver_vacas.php?id=<?php echo $vaca['id_establecimiento']; ?>" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>