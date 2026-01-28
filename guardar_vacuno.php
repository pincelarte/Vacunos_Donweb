<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/back/models/Vacuno.php';

// 1. Capturamos los datos (Agregamos el ID del establecimiento)
$caravana = $_POST['caravana'];
$tipo     = $_POST['tipo'];
$raza     = $_POST['raza'];
$edad     = $_POST['edad'];
$peso     = $_POST['peso'];
$id_est   = $_POST['id_establecimiento']; // <--- Capturamos el nuevo campo

// 2. Creamos el objeto pasando los 6 argumentos
// Importante: El orden debe ser el mismo que definiste en el __construct de Vacuno.php
$animal = new Vacuno($tipo, $caravana, $raza, $edad, $peso, $id_est);

// 3. Ejecutamos el guardado
$resultado = $animal->insertar();

echo "<h1>" . $resultado . "</h1>";
echo "<a href='index.php'>Volver al formulario</a>";
