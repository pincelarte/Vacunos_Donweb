<?php
// 1. Iniciamos la sesión para que el servidor cree un espacio de memoria [cite: 2026-01-24].
session_start();

require_once __DIR__ . '/../models/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['usuario_login'];
    $password = $_POST['pass_login'];

    $modeloUsuario = new Usuario();
    $usuarioEncontrado = $modeloUsuario->buscarPorNombre($nombre);

    if ($usuarioEncontrado) {
        // En LoginController.php, buscá esta línea y dejala así:
        if (password_verify($password, $usuarioEncontrado['password'])) {
        
            // 3. Guardamos el usuario en la memoria de sesión [cite: 2026-01-24].
            $_SESSION['usuario'] = $usuarioEncontrado['nombre_usuario'];

            // 4. Redirigimos físicamente al navegador hacia la gestión [cite: 2026-01-24].
            header("Location: ../../front/gestion.php");
            exit();
        } else {
            echo "La contraseña es incorrecta.";
        }
    } else {
        echo "El usuario no existe.";
    }
}
