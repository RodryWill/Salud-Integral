<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ejercicios — Salud Integral</title>
<link rel="stylesheet" href="Assets/CSS/estilos.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<?php include 'includes/cabecera.php'; ?>

<main class="dashboard">
  <div style="text-align: center; margin-bottom: 2rem;">
    <h1 id="tituloRutina" style="color: var(--primario);">Cargando...</h1>
    <a href="index.php" class="btn-secundario" style="margin-top: 10px; display: inline-block; text-decoration: none;">&larr; Volver</a>
  </div>

  <h2>Selecciona un ejercicio para empezar</h2>
  <div id="listaEjercicios" class="grid-rutinas"></div>
</main>

<?php include 'includes/pie.php'; ?>

<script>
$(function () {
  const urlParams = new URLSearchParams(window.location.search);
  const rutinaId = urlParams.get('id');

  $.getJSON('api/index.php', { accion: 'detalle_rutina', id: rutinaId }, function (data) {
    if (data.error) { alert(data.error); return; }
    
    $('#tituloRutina').text(data.rutina.titulo);
    
    $('#listaEjercicios').html(data.ejercicios.map(e => `
      <article class="workout-card" onclick="window.location.href='ejercicio_detalle.php?id=${e.id}'" style="cursor: pointer;">
        <div class="wc-media">
          <img src="${e.imagen_url}" alt="${e.nombre}">
        </div>
        <div class="wc-body">
          <h3>${e.nombre}</h3>
          <p>${e.series}</p>
        </div>
      </article>
    `).join(''));
  });
});
</script>
</body>
</html>