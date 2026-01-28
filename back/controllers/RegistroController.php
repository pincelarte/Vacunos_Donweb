<?php
// 1. Importamos el modelo de Usuario para poder usar sus funciones
require_once __DIR__ . '/../models/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. Capturamos los datos del formulario de registro
    $nombre = $_POST['nuevo_usuario'];
    $pass = $_POST['nueva_pass'];

    // 3. Lógica de Seguridad: Encriptamos la clave
    // No guardamos "1234", sino una cadena de texto larga e ilegible
    $passHash = password_hash($pass, PASSWORD_BCRYPT);

    $modelo = new Usuario();

    // 4. Intentamos registrar (este método lo creamos en el siguiente paso)
    if ($modelo->registrar($nombre, $passHash)) {
        // Si sale bien, volvemos al login para que el usuario entre
        header("Location: ../../front/index.php?registro=exitoso");
        exit();
    } else {
        echo "Error: No se pudo crear la cuenta.";
    }
}
