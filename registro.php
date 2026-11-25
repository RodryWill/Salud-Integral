<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Crear cuenta — Salud Integral</title>
<link rel="stylesheet" href="Assets/CSS/estilos.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="auth-page">
<main class="auth-card">
  <h1>Crear cuenta</h1>
  <form id="formRegistro" novalidate>
    <label for="nombre">Nombre</label>
    <input type="text" id="nombre" name="nombre" required maxlength="100">

    <label for="emailReg">Correo</label>
    <input type="email" id="emailReg" name="email" required autocomplete="email">

    <label for="passwordReg">Contraseña (1 a 8 caracteres)</label>
    <input type="password" id="passwordReg" name="password" required minlength="1" maxlength="8">

    <label for="passwordReg2">Repetir contraseña</label>
    <input type="password" id="passwordReg2" name="password2" required minlength="1" maxlength="8">

    <button type="submit" class="btn-primario">Registrarme</button>
    <p id="mensajeRegistro" role="alert"></p>
  </form>
  <p><a href="login.php">Ya tengo cuenta</a></p>
</main>
<script src="Assets/JS/app.js"></script>
</body>
</html>