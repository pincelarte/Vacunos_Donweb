<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

require_once __DIR__ . '/../back/models/Vacuno.php';
require_once __DIR__ . '/../back/models/Pesaje.php';

// CAMBIO: Ahora usamos el ID numérico del animal [cite: 2026-01-24]
$id_vaca = $_GET['id'] ?? null;

if (!$id_vaca) {
    echo "Falta el identificador del animal.";
    exit();
}

// CAMBIO: Buscamos al animal por su ID único
$vaca = Vacuno::obtenerPorId($id_vaca);

if (!$vaca) {
    echo "Vaca no encontrada.";
    exit();
}

// Obtenemos la caravana para los pesajes
$caravana = $vaca['caravana'];
$historial = Pesaje::obtenerHistorial($id_vaca);

function escapeHtml($data)
{
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial - <?php echo escapeHtml($caravana); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/estilos.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Historial de: <?php echo escapeHtml($caravana); ?></h1>
            <div>
                <button type="button" class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#formPesaje">
                    Nuevo Pesaje
                </button>
                <a href="ver_vacas.php?id=<?php echo $vaca['id_establecimiento']; ?>" class="btn btn-secondary">Volver</a>
            </div>
        </div>

        <div class="collapse mb-4" id="formPesaje">
            <div class="card card-body shadow-sm">
                <form action="../back/controllers/VacunoController.php" method="POST" class="row g-3 align-items-end">
                    <input type="hidden" name="accion" value="registrar_pesaje_produccion">

                    <input type="hidden" name="caravana" value="<?php echo escapeHtml($caravana); ?>">
                    <input type="hidden" name="id_vaca" value="<?php echo $id_vaca; ?>">
                    <input type="hidden" name="id_establecimiento" value="<?php echo $vaca['id_establecimiento']; ?>">

                    <div class="col-md-4">
                        <label class="form-label small">Nuevo Peso (kg)</label>
                        <input type="number" name="nuevo_peso" class="form-control" step="0.1" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Guardar Pesaje</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">Datos del Animal</div>
                    <div class="card-body">
                        <p><strong>Tipo:</strong> <?php echo escapeHtml($vaca['tipo']); ?></p>
                        <p><strong>Raza:</strong> <?php echo escapeHtml($vaca['raza']); ?></p>
                        <p><strong>Peso Actual:</strong> <?php echo escapeHtml($vaca['peso_actual']); ?> kg</p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">Evolución de Pesos</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Fecha y Hora</th>
                                        <th>Peso Registrado</th>
                                        <th>Diferencia</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($historial)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No hay registros de pesaje.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php
                                        $ultimoPeso = null;
                                        $historialReversed = array_reverse($historial);
                                        $indice = 0;
                                        foreach ($historialReversed as $p):
                                            $dif = ($ultimoPeso !== null) ? ($p['peso'] - $ultimoPeso) : 0;
                                            $colorDif = ($dif > 0) ? 'text-success' : 'text-danger';
                                            $esOriginal = ($indice === 0);
                                        ?>
                                            <tr>
                                                <td><?php echo date('d/m/Y H:i', strtotime($p['fecha_pesaje'])); ?></td>
                                                <td><strong><?php echo escapeHtml($p['peso']); ?> kg</strong></td>
                                                <td class="<?php echo $colorDif; ?>">
                                                    <?php echo ($ultimoPeso !== null) ? ($dif > 0 ? '+' : '') . $dif . ' kg' : '---'; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2" style="min-width: 140px;">
                                                        <a href="editar_pesaje.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-warning flex-grow-1 text-nowrap">Editar</a>

                                                        <?php if (!$esOriginal): ?>
                                                            <a href="../back/controllers/VacunoController.php?accion=eliminar_pesaje&id_pesaje=<?php echo $p['id']; ?>&id_vaca=<?php echo $id_vaca; ?>"
                                                                class="btn btn-sm btn-outline-danger flex-grow-1 text-nowrap"
                                                                onclick="return confirm('¿Estás seguro de eliminar este registro de peso?')">
                                                                Borrar
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php
                                            $ultimoPeso = $p['peso'];
                                            $indice++;
                                        endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>