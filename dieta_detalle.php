<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Detalle de la Dieta — Salud Integral</title>
<link rel="stylesheet" href="Assets/CSS/estilos.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<style>
  .dieta-detalle { background: #fff; padding: 2rem; border-radius: var(--radio); box-shadow: var(--sombra); max-width: 800px; margin: 0 auto; }
  .dieta-header { display: flex; gap: 2rem; margin-bottom: 2rem; flex-wrap: wrap; }
  .dieta-img { width: 100%; max-width: 350px; border-radius: 12px; object-fit: cover; }
  .dieta-info { flex: 1; min-width: 300px; }
  .badge-tipo { background: var(--primario-claro); color: var(--primario); padding: 0.4rem 1rem; border-radius: 99px; font-weight: bold; font-size: 0.9rem; display: inline-block; margin-bottom: 1rem; }
  .seccion-texto { margin-bottom: 2rem; }
  .seccion-texto h2 { font-size: 1.3rem; color: var(--primario); margin-bottom: 1rem; border-bottom: 2px solid var(--primario-claro); padding-bottom: 0.5rem; }
  .seccion-texto ul, .seccion-texto ol { padding-left: 1.5rem; line-height: 1.8; color: var(--texto); }
</style>
</head>
<body>
<?php include 'includes/cabecera.php'; ?>

<main class="dashboard">
  <div style="margin-bottom: 1rem;">
    <a href="dietas.php" class="btn-secundario" style="text-decoration: none;">&larr; Volver al catálogo de dietas</a>
  </div>

  <div id="detalleDieta" class="dieta-detalle">
    <p>Cargando información de la dieta...</p>
  </div>
</main>

<?php include 'includes/pie.php'; ?>

<script>
$(function () {
  const urlParams = new URLSearchParams(window.location.search);
  const dietaId = urlParams.get('id');

  if (!dietaId) {
    $('#detalleDieta').html('<p>Dieta no especificada.</p>');
    return;
  }

  $.getJSON('api/index.php', { accion: 'detalle_dieta', id: dietaId }, function (d) {
    if (d.error) {
      $('#detalleDieta').html('<p>' + d.error + '</p>');
      return;
    }

    // Convertir saltos de línea en etiquetas <li> para listas
    const ingredientesHTML = d.ingredientes ? d.ingredientes.split('\n').map(i => `<li>${i}</li>`).join('') : '<li>Ingredientes no especificados.</li>';
    const preparacionHTML = d.preparacion ? d.preparacion.split('\n').map(p => `<li>${p}</li>`).join('') : '<li>Preparación no especificada.</li>';

    $('#detalleDieta').html(`
      <div class="dieta-header">
        <img src="${d.imagen_url || 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?q=80&w=600&auto=format&fit=crop'}" alt="${d.titulo}" class="dieta-img">
        <div class="dieta-info">
          <span class="badge-tipo">${d.tipo} • ${d.calorias} kcal</span>
          <h1 style="color: var(--texto); margin-top: 0; font-size: 2.2rem;">${d.titulo}</h1>
          <p style="color: var(--suave); font-size: 1.1rem; line-height: 1.6;">${d.descripcion || ''}</p>
        </div>
      </div>

      <div class="seccion-texto">
        <h2>Ingredientes</h2>
        <ul>${ingredientesHTML}</ul>
      </div>

      <div class="seccion-texto">
        <h2>Preparación paso a paso</h2>
        <ol>${preparacionHTML}</ol>
      </div>

      ${d.consejos ? `
      <div class="seccion-texto" style="background: #dcfce7; padding: 1.5rem; border-radius: 12px; border-left: 4px solid var(--primario);">
        <h2 style="margin-top: 0; border: none; font-size: 1.1rem;">💡 Consejos / Beneficios</h2>
        <p style="margin: 0; color: #166534;">${d.consejos}</p>
      </div>
      ` : ''}
    `);
  }).fail(function () {
    $('#detalleDieta').html('<p>Error al cargar la dieta.</p>');
  });
});
</script>
</body>
</html>