<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// 1. Capturamos el ID que viene por la URL [cite: 2026-01-28]
$id = $_GET['id'];

require_once __DIR__ . '/../back/models/Establecimiento.php';
$modelo = new Establecimiento();

// IMPORTANTE: El modelo ahora devuelve una fila con 'nombre_est' [cite: 2026-01-28]
$datos = $modelo->obtenerPorId($id);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Establecimiento - Don Silicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/estilos.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h3>Editar: <?php echo $datos['nombre_est']; ?></h3>
            </div>
            <div class="card-body">
                <form action="../back/controllers/EstablecimientoController.php" method="POST">
                    <input type="hidden" name="id_est" value="<?php echo $id; ?>">
                    <input type="hidden" name="accion" value="actualizar">

                    <div class="mb-3">
                        <label class="form-label">Nombre (Max. 20 caracteres)</label>
                        <input type="text" name="nuevo_nombre" class="form-control"
                            value="<?php echo $datos['nombre_est']; ?>" maxlength="20" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    <a href="gestion.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>