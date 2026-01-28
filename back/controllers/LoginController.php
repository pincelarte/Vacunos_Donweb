<?php
session_start();
require_once __DIR__ . '/../models/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['usuario_login'];
    $password = $_POST['pass_login'];

    $modeloUsuario = new Usuario();
    $usuarioEncontrado = $modeloUsuario->buscarPorNombre($nombre);

    if ($usuarioEncontrado) {
        // CORRECCIÓN: Usamos ['password'] que es el nombre real en tu DB [cite: 2026-01-24]
        if (password_verify($password, $usuarioEncontrado['password'])) {

            $_SESSION['usuario'] = $usuarioEncontrado['nombre_usuario'];

            // Redirigimos a la gestión de vacas
            header("Location: ../../front/gestion.php");
            exit();
        } else {
            echo "La contraseña es incorrecta.";
        }
    } else {
        echo "El usuario no existe.";
    }
}
