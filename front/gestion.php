<?php
session_start();
// Si no hay sesión, lo mandamos de patitas a la calle (al login) [cite: 2026-01-24]
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Vacas - Don Silicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between">
            <h1>Bienvenido, <?php echo $_SESSION['usuario']; ?></h1>
            <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
        <hr>
    </div>
</body>

</html>