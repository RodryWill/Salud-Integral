<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Contacto — Salud Integral</title>
<link rel="stylesheet" href="Assets/CSS/estilos.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<?php include 'includes/cabecera.php'; ?>

<main class="dashboard" style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
  <div class="auth-card" style="width: 100%; max-width: 500px;">
    <h1 style="color: var(--primario); text-align: center;">Contáctanos</h1>
    <p style="text-align: center; color: var(--suave); margin-bottom: 2rem;">¿Tienes alguna duda? Escríbenos.</p>
    
    <form id="formContacto">
      <label for="nombreC">Nombre completo</label>
      <input type="text" id="nombreC" required maxlength="100">

      <label for="emailC">Correo electrónico</label>
      <input type="email" id="emailC" required>

      <label for="mensajeC">Mensaje</label>
      <textarea id="mensajeC" rows="4" required style="width:100%; padding:.9rem 1rem; border:1px solid var(--borde); border-radius:12px; font:inherit; resize: vertical;"></textarea>

      <button type="submit" class="btn-primario" style="width: 100%; margin-top: 1rem;">Enviar Mensaje</button>
      <p id="msgContacto" style="text-align: center; color: var(--primario); font-weight: bold; margin-top: 1rem;"></p>
    </form>
  </div>
</main>

<?php include 'includes/pie.php'; ?>

<script>
$('#formContacto').on('submit', function (e) {
  e.preventDefault();
  // Simulamos el envío del mensaje
  $('#msgContacto').text('¡Mensaje enviado con éxito! Te responderemos pronto.');
  $(this)[0].reset();
});
</script>
</body>
</html>