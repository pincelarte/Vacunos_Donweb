<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login - Donweb</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Ingreso al Sistema</h4>
                    </div>

                    <div class="card-body">
                        <form action="../back/controllers/LoginController.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Usuario</label>
                                <input type="text" name="usuario_login" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="pass_login" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Entrar</button>

                            <div class="mt-3 text-center">
                                <p>¿No tenés cuenta? <a href="registro.php">Crear nuevo usuario</a></p>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

</body>

</html>