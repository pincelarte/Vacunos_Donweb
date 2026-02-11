<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

require_once __DIR__ . '/../back/models/Pesaje.php';
require_once __DIR__ . '/../back/models/Vacuno.php';

$id_pesaje = $_GET['id'] ?? null;

if (!$id_pesaje) {
    header("Location: historial_vaca.php?error=sin_id");
    exit();
}

// Obtener el pesaje actual
$pesaje = Pesaje::obtenerPorId($id_pesaje);

if (!$pesaje) {
    header("Location: historial_vaca.php?error=no_encontrado");
    exit();
}

// Obtener id_vaca directamente del pesaje (más confiable)
$id_vaca = $pesaje['id_vaca'] ?? null;

// Fallback: si id_vaca está vacío, buscar por caravana (retrocompatibilidad)
if (empty($id_vaca)) {
    $id_vaca = Vacuno::obtenerIdPorCaravana($pesaje['caravana_vacuno']);
}

function escapeHtml($data)
{
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_peso = $_POST['nuevo_peso'] ?? null;

    if ($nuevo_peso !== null && is_numeric($nuevo_peso)) {
        // Primero actualizar el pesaje
        if (Pesaje::corregir($id_pesaje, $nuevo_peso)) {
            // Luego actualizar el peso_actual del vacuno si tenemos id_vaca
            $actualizo_vacuno = true;
            if ($id_vaca) {
                $actualizo_vacuno = Vacuno::actualizarPesoSimple($id_vaca, $nuevo_peso);
            }

            if ($actualizo_vacuno) {
                header("Location: historial_vaca.php?id=" . ($id_vaca ?: '') . "&exito=editado");
                exit();
            } else {
                $error = "Se actualizó el pesaje pero no el peso de la vaca (ID: $id_vaca). Verifica que la vaca exista.";
            }
        } else {
            $error = "No se pudo guardar el pesaje. Error en base de datos.";
        }
    } else {
        $error = "El peso debe ser un número válido.";
    }
}

$caravana = $pesaje['caravana_vacuno'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pesaje - <?php echo escapeHtml($caravana); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/estilos.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning">
                        <h4>✏️ Corregir Pesaje</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Caravana:</strong> <?php echo escapeHtml($caravana); ?></p>
                        <p><strong>Fecha original:</strong> <?php echo date('d/m/Y H:i', strtotime($pesaje['fecha_pesaje'])); ?></p>
                        <p><strong>Peso original:</strong> <?php echo escapeHtml($pesaje['peso']); ?> kg</p>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nuevo Peso (kg)</label>
                                <input type="number" name="nuevo_peso" class="form-control"
                                    step="0.1" value="<?php echo escapeHtml($pesaje['peso']); ?>" required>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">💾 Guardar Cambios</button>
                                <a href="historial_vaca.php?id=<?php echo $id_vaca ?: ''; ?>" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>