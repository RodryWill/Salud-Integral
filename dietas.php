<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dietas y Alimentación — Salud Integral</title>
<link rel="stylesheet" href="Assets/CSS/estilos.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<?php include 'includes/cabecera.php'; ?>

<main class="dashboard">
  <section class="hero" style="text-align: center; margin-bottom: 2rem;">
    <h1 style="color: var(--primario);">Alimentación Saludable</h1>
    <p style="color: var(--suave);">Rutas nutricionales para complementar tu entrenamiento.</p>
  </section>

  <!-- Aquí se cargarán las dietas desde la base de datos -->
  <div id="listaDietas" class="grid-rutinas"></div>
</main>

<?php include 'includes/pie.php'; ?>

<script>
$(function () {
  $.getJSON('api/index.php', { accion: 'dietas' }, function (data) {
    if (data.length === 0) {
      $('#listaDietas').html('<p style="grid-column: 1/-1; text-align: center;">No hay dietas registradas aún. Ingresa al panel de administrador para agregar una.</p>');
      return;
    }
    
    // Generamos las tarjetas con el evento onclick para ir al detalle
    $('#listaDietas').html(data.map(d => `
      <article class="workout-card" onclick="window.location.href='dieta_detalle.php?id=${d.id}'" style="cursor: pointer;">
        <div class="wc-media">
          <img src="${d.imagen_url || 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?q=80&w=400&auto=format&fit=crop'}" alt="${d.titulo}">
          <span class="badge">${d.tipo} • ${d.calorias} kcal</span>
        </div>
        <div class="wc-body">
          <h3>${d.titulo}</h3>
          <p style="color: var(--suave); font-size: 0.95rem;">${d.descripcion || ''}</p>
        </div>
      </article>
    `).join(''));
  }).fail(function() {
    $('#listaDietas').html('<p style="grid-column: 1/-1; text-align: center; color: red;">Error al cargar las dietas.</p>');
  });
});
</script>
</body>
</html>