<?php
// 1. Cargamos la herramienta (El Modelo)
require_once __DIR__ . '/../back/models/Establecimiento.php';

$modelo = new Establecimiento();

// Datos para la prueba
$nombreTest = "Campo de Prueba QA";
$ubicacionTest = "Sector Tandilin";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>QA Establecimientos - Navegador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h3 class="mb-0">Panel de Calidad: Establecimientos</h3>
            </div>
            <div class="card-body">

                <div class="mb-4">
                    <h5>Prueba 1: Creación de Registro</h5>
                    <?php if ($modelo->crear($nombreTest, $ubicacionTest)): ?>
                        <div class="alert alert-success">✅ <strong>PASADA:</strong> El establecimiento se guardó correctamente.</div>
                    <?php else: ?>
                        <div class="alert alert-danger">❌ <strong>FALLIDA:</strong> No se pudo insertar en la base de datos.</div>
                    <?php endif; ?>
                </div>

                <hr>

                <div class="mb-4">
                    <h5>Prueba 2: Regla de Negocio (20 caracteres)</h5>
                    <?php if (strlen($nombreTest) <= 20): ?>
                        <div class="alert alert-success">✅ <strong>PASADA:</strong> El nombre "<?php echo $nombreTest; ?>" cumple con el límite.</div>
                    <?php else: ?>
                        <div class="alert alert-warning">❌ <strong>FALLIDA:</strong> El nombre excede los 20 caracteres permitidos.</div>
                    <?php endif; ?>
                </div>

                <div class="mt-4 text-center">
                    <small class="text-muted">Pruebas de Integración Finalizadas</small>
                </div>