<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Iniciar sesión — Salud Integral</title>
<link rel="stylesheet" href="Assets/CSS/estilos.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="auth-page">
<main class="auth-card">
  <h1>Acceso</h1>
  <form id="formLogin" novalidate>
    <label for="email">Correo</label>
    <input type="email" id="email" name="email" required autocomplete="username" placeholder="wmollericona@gmail.com">

    <label for="password">Contraseña (1 a 8 caracteres)</label>
    <input type="password" id="password" name="password" required minlength="1" maxlength="8" autocomplete="current-password">

    <button type="submit" class="btn-primario" style="margin-top: 1rem; width: 100%;">Entrar</button>
    <p id="mensajeLogin" role="alert" style="color: #b91c1c; text-align: center; margin-top: 1rem;"></p>
  </form>
  <p style="text-align: center; margin-top: 1rem;"><a href="registro.php" style="color: var(--primario);">Crear cuenta</a></p>
  <p style="text-align: center; margin-top: 1rem;">
  <a href="registro.php" style="color: var(--primario);">Crear cuenta</a>
</p>
<p style="text-align: center; margin-top: 0.5rem;">
  <!-- Nuevo enlace para volver al inicio -->
  <a href="index.php" style="color: var(--suave); font-size: 0.9rem; text-decoration: none;">&larr; Volver al inicio público</a>
</p>
</main>
<script src="Assets/JS/app.js"></script>
</body>
</html>