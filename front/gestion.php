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
                        <input type="text" name="nombre_est" class="form-control" placeholder="Nombre" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="ubicacion_est" class="form-control" placeholder="Ubicación" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <table class="table table-striped bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Ubicación</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listaEstablecimientos as $est): ?>
                    <tr>
                        <td><?php echo $est['id']; ?></td>
                        <td><?php echo $est['nombre']; ?></td>
                        <td><?php echo $est['ubicacion']; ?></td>
                        <td>
                            <a href="ver_vacas.php?id=<?php echo $est['id']; ?>" class="btn btn-sm btn-success">
                                Seleccionar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>