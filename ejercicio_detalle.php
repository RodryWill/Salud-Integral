<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Detalle del Ejercicio — Salud Integral</title>
<link rel="stylesheet" href="Assets/CSS/estilos.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<style>
  .detalle-container { display: flex; flex-wrap: wrap; gap: 2rem; background: #fff; padding: 2rem; border-radius: var(--radio); box-shadow: var(--sombra); }
  .detalle-video { flex: 1; min-width: 300px; }
  .detalle-info { flex: 1; min-width: 300px; }
  .cronometro-grande { font-size: 4rem; font-weight: 900; color: var(--primario); text-align: center; margin: 1.5rem 0; font-variant-numeric: tabular-nums; }
  .pasos-lista { list-style: none; padding: 0; }
  .pasos-lista li { margin-bottom: 1rem; padding-left: 2rem; position: relative; }
  .pasos-lista li::before { content: "✓"; position: absolute; left: 0; color: var(--primario); font-weight: bold; }
</style>
</head>
<body>
<?php include 'includes/cabecera.php'; ?>

<main class="dashboard">
  <a href="javascript:history.back()" class="btn-secundario" style="margin-bottom: 1rem; display: inline-block; text-decoration: none;">&larr; Volver a los ejercicios</a>
  
  <div id="contenidoEjercicio" class="detalle-container">
    <p>Cargando...</p>
  </div>
</main>

<?php include 'includes/pie.php'; ?>

<script>
$(function () {
  const urlParams = new URLSearchParams(window.location.search);
  const ejercicioId = urlParams.get('id');
  let timerInterval = null;
  let segundos = 0;
  let corriendo = false;

  $.getJSON('api/index.php', { accion: 'ejercicio_detalle', id: ejercicioId }, function (e) {
    $('#contenidoEjercicio').html(`
      <div class="detalle-video">
        <video id="videoEjercicio" width="100%" controls style="border-radius: 12px; background: #000;">
          <source src="${e.video_url}" type="video/mp4">
        </video>
        <img src="${e.imagen_url}" alt="${e.nombre}" style="width: 100%; border-radius: 12px; margin-top: 1rem;">
      </div>
      <div class="detalle-info">
        <h1 style="color: var(--primario); margin-top: 0;">${e.nombre}</h1>
        <span class="badge" style="position: static; display: inline-block; margin-bottom: 1rem; background: var(--primario-claro); color: var(--primario);">${e.series}</span>
        <p style="font-size: 1.1rem; line-height: 1.6;">${e.descripcion}</p>
        
        <h3>Paso a paso:</h3>
        <ul class="pasos-lista">
          <li>Colócate en una posición cómoda y segura.</li>
          <li>Realiza el movimiento de forma controlada siguiendo el video.</li>
          <li>Respira profundamente, no aguantes la respiración.</li>
          <li>Si sientes dolor, detente inmediatamente.</li>
        </ul>

        <div class="cronometro-grande" id="cronometro">00:00</div>
        <div style="display: flex; gap: 1rem; justify-content: center;">
          <button id="btnPlay" class="btn-primario">Iniciar Tiempo</button>
          <button id="btnReset" class="btn-secundario">Reiniciar</button>
        </div>
      </div>
    `);

    // Lógica del cronómetro
    function formato(seg) {
      const m = String(Math.floor(seg / 60)).padStart(2, '0');
      const s = String(Math.floor(seg % 60)).padStart(2, '0');
      return `${m}:${s}`;
    }

    $('#btnPlay').on('click', function () {
      if (corriendo) {
        clearInterval(timerInterval);
        $(this).text('Reanudar');
        corriendo = false;
      } else {
        timerInterval = setInterval(() => {
          segundos++;
          $('#cronometro').text(formato(segundos));
        }, 1000);
        $(this).text('Pausar');
        corriendo = true;
      }
    });

    $('#btnReset').on('click', function () {
      clearInterval(timerInterval);
      segundos = 0;
      corriendo = false;
      $('#cronometro').text('00:00');
      $('#btnPlay').text('Iniciar Tiempo');
    });
  });
});
</script>
</body>
</html>