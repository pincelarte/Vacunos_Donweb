<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nuevo Usuario - Don Silicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/estilos.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 400px;">
            <div class="card-header bg-success text-white">
                <h4>Registrar Nuevo Capataz</h4>
            </div>
            <div class="card-body">
                <form action="../back/controllers/RegistroController.php" method="POST">
                    <div class="mb-3">
                        <label>Nombre de Usuario</label>
                        <input type="text" name="nuevo_usuario" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Contraseña</label>
                        <input type="password" name="nueva_pass" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Crear Cuenta</button>
                    <div class="mt-3 text-center">
                        <a href="index.php">Volver al Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>