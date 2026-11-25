<?php
session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit; }
if ($_SESSION['usuario']['rol'] === 'admin') { header('Location: dashboard.php'); exit; }
$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Mi panel — Salud Integral</title>
<link rel="stylesheet" href="Assets/CSS/estilos.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
  <a href="#contenidoPrincipal" class="saltar">Saltar al contenido</a>
<?php include 'includes/cabecera.php'; ?>
<main class="dashboard">
  <section class="bienvenida">
    <h1>Mi bienestar, <?= htmlspecialchars($usuario['nombre']) ?></h1>
    <p>Rutinas recomendadas para ti y tu progreso semanal.</p>
  </section>

  <section class="panel-progreso" aria-label="Progreso semanal">
    <h2>Mi actividad — últimos 7 días</h2>
    <div id="graficoSemana" class="grafico-barras" role="img" aria-label="Gráfico de barras de minutos por día"></div>
    <p id="resumenSemana" class="resumen-semana"></p>
  </section>

  <section class="reproductor" aria-label="Rutina activa">
    <h2>Rutina de hoy</h2>
    <video id="videoRutina" width="100%" controls preload="metadata">
      <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4">
    </video>
    <div class="controles-rutina">
      <div id="cronometro" class="cronometro" aria-live="polite">00:00</div>
      <button id="btnIniciar" class="btn-primario" type="button">Iniciar / Pausar</button>
      <button id="btnReiniciar" class="btn-secundario" type="button">Reiniciar</button>
    </div>
    <div class="barra-progreso" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
      <div id="progreso" class="progreso-relleno"></div>
    </div>
  </section>

  <section class="catalogo" aria-label="Catálogo de rutinas">
    <h2>Explora rutinas</h2>
    <div class="filtros">
      <button class="pill activo" data-nivel="">Todas</button>
      <button class="pill" data-nivel="Principiante">Principiante</button>
      <button class="pill" data-nivel="Intermedio">Intermedio</button>
      <button class="pill" data-nivel="Avanzado">Avanzado</button>
    </div>
    <div id="listaRutinas" class="grid-rutinas"></div>
  </section>
</main>
<?php include 'includes/pie.php'; ?>
<script>
$(function () {
  // Temporizador (igual que dashboard)
  const video = document.getElementById('videoRutina');
  const cronometro = document.getElementById('cronometro');
  const progreso = document.getElementById('progreso');
  const barra = progreso.parentElement;
  let intervalo = null;

  function formato(seg) {
    const m = String(Math.floor(seg / 60)).padStart(2, '0');
    const s = String(Math.floor(seg % 60)).padStart(2, '0');
    return `${m}:${s}`;
  }
  function actualizarUI() {
    if (!video.duration) return;
    cronometro.textContent = formato(video.currentTime);
    const pct = (video.currentTime / video.duration) * 100;
    progreso.style.width = pct + '%';
    barra.setAttribute('aria-valuenow', Math.round(pct));
  }
  function iniciarIntervalo() { if (!intervalo) intervalo = setInterval(actualizarUI, 200); }
  function detenerIntervalo() { clearInterval(intervalo); intervalo = null; }

  $('#btnIniciar').on('click', function () {
    if (video.paused) { video.play(); iniciarIntervalo(); }
    else { video.pause(); detenerIntervalo(); }
  });
  $('#btnReiniciar').on('click', function () {
    video.pause(); video.currentTime = 0; detenerIntervalo();
    actualizarUI(); cronometro.textContent = '00:00'; progreso.style.width = '0%';
  });
  video.addEventListener('timeupdate', actualizarUI);
  video.addEventListener('ended', function () {
    detenerIntervalo();
    const minutos = Math.max(1, Math.round(video.duration / 60));
    $.post('api/index.php', { accion: 'progreso_guardar', minutos: minutos }, function () {
      cargarProgreso();
    }, 'json');
  });

  // Gráfico
  function cargarProgreso() {
    $.getJSON('api/index.php', { accion: 'progreso_semana' }, function (data) {
      const max = Math.max(1, ...data.map(d => d.minutos));
      $('#graficoSemana').html(data.map(d => {
        const alto = Math.round((d.minutos / max) * 100);
        return `<div class="barra-col">
          <div class="barra-valor">${d.minutos}</div>
          <div class="barra" style="height:${alto}%"></div>
          <span class="barra-dia">${d.dia}</span>
        </div>`;
      }).join(''));
      const total = data.reduce((s, d) => s + d.minutos, 0);
      $('#resumenSemana').text(`Total de la semana: ${total} minutos`);
    });
  }
  cargarProgreso();

  // Catálogo
  function cargarRutinas(nivel) {
    $('#listaRutinas').html('<p>Cargando…</p>');
    $.getJSON('api/index.php', { accion: 'rutinas', nivel: nivel }, function (data) {
      if (!data.length) { $('#listaRutinas').html('<p>No hay rutinas para este filtro.</p>'); return; }
      $('#listaRutinas').html(data.map(r => `
        <article class="workout-card">
          <div class="wc-media"><span class="badge">${r.nivel}</span></div>
          <div class="wc-body"><h3>${r.titulo}</h3><p>${r.duracion_minutos} min</p></div>
        </article>`).join(''));
    }).fail(function () { $('#listaRutinas').html('<p>Error al cargar rutinas.</p>'); });
  }
  cargarRutinas('');
  $('.filtros').on('click', '.pill', function () {
    $('.pill').removeClass('activo'); $(this).addClass('activo');
    cargarRutinas($(this).data('nivel'));
  });
});

</script>
</body>
</html>