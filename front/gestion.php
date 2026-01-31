<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// 1. Llamamos al modelo para traer los datos [cite: 2026-01-28]
require_once __DIR__ . '/../back/models/Establecimiento.php';
$modelo = new Establecimiento();
$listaEstablecimientos = $modelo->listarTodo();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión - Don Silicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between">
            <h1>Bienvenido, <?php echo $_SESSION['usuario']; ?></h1>
            <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
        <hr>

        <div class="card my-4">
            <div class="card-body">
                <form action="../back/controllers/EstablecimientoController.php" method="POST" class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="nombre_est" class="form-control" placeholder="Establecimiento (máx. 20 letras)" maxlength="20" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="ubicacion_est" class="form-control" placeholder="Ubicación (máx. 20 letras)" maxlength="20" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                if ($_GET['mensaje'] == 'eliminado') echo "✅ El establecimiento fue borrado.";
                if ($_GET['mensaje'] == 'ok') echo "💾 Guardado con éxito.";
                if ($_GET['mensaje'] == 'error') echo "❌ Hubo un error.";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

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
                <?php foreach ($listaEstablecimientos as $est): ?>
                    <tr>
                        <td class="text-center"><?php echo $est['id']; ?></td>
                        <td class="text-center"><?php echo $est['nombre']; ?></td>
                        <td class="text-center"><?php echo $est['ubicacion']; ?></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="ver_vacas.php?id=<?php echo $est['id']; ?>" class="btn btn-sm btn-success">Seleccionar</a>

                                <button type="button" class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                    ⚙️
                                </button>

                                <ul class="dropdown-menu shadow">
                                    <li>
                                        <a class="dropdown-item" href="editar_establecimiento.php?id=<?php echo $est['id']; ?>">✏️ Editar</a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <button class="dropdown-item text-danger" onclick="confirmarBorrado(<?php echo $est['id']; ?>, '<?php echo $est['nombre']; ?>')">
                                            🗑️ Borrar
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        function confirmarBorrado(id, nombre) {
            // Pedimos al usuario que escriba 'borrar' para confirmar [cite: 2026-01-24]
            let confirmacion = prompt("Para eliminar el establecimiento '" + nombre + "', escriba 'borrar':");

            if (confirmacion === 'borrar') {
                // Si escribió bien, lo mandamos al controlador [cite: 2026-01-28]
                window.location.href = "../back/controllers/EstablecimientoController.php?accion=eliminar&id=" + id;
            } else if (confirmacion !== null) {
                alert("Palabra incorrecta. No se eliminó el establecimiento.");
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>