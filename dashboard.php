<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}
$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Panel de Administración — Salud Integral</title>
<link rel="stylesheet" href="Assets/CSS/estilos.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<style>
  .admin-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-top: 2rem; }
  @media (max-width: 768px) { .admin-grid { grid-template-columns: 1fr; } }
  .admin-card { background: #fff; border-radius: var(--radio); padding: 2rem; box-shadow: var(--sombra); margin-bottom: 2rem; }
  .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
  .stat-box { background: var(--fondo); padding: 1.5rem; border-radius: 12px; text-align: center; border: 1px solid var(--borde); }
  .stat-box h3 { margin: 0; color: var(--suave); font-size: 0.9rem; text-transform: uppercase; }
  .stat-box p { margin: 0.5rem 0 0 0; font-size: 2rem; font-weight: 800; color: var(--primario); }
  .noticia-item { border-bottom: 1px solid var(--borde); padding: 1rem 0; display: flex; gap: 1rem; align-items: center; }
  .noticia-item img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
  textarea { width: 100%; padding: 1rem; border: 1px solid var(--borde); border-radius: 12px; font-family: inherit; }
</style>
</head>
<body>
<?php include 'includes/cabecera.php'; ?>

<main class="dashboard">
  <div style="display: flex; justify-content: space-between; align-items: center;">
    <h1>Panel de Administración</h1>
    <p>Bienvenido de nuevo, <strong><?= htmlspecialchars($usuario['nombre']) ?></strong></p>
  </div>

  <!-- REPORTES Y ESTADÍSTICAS -->
  <div class="stats-grid" id="statsContainer">
    <div class="stat-box"><h3>Usuarios Registrados</h3><p id="statUsuarios">-</p></div>
    <div class="stat-box"><h3>Rutinas Creadas</h3><p id="statRutinas">-</p></div>
    <div class="stat-box"><h3>Ejercicios Totales</h3><p id="statEjercicios">-</p></div>
    <div class="stat-box"><h3>Minutos Entrenados (Global)</h3><p id="statMinutos">-</p></div>
  </div>

  <div class="admin-grid">
    <!-- COLUMNA IZQUIERDA: Gestión de Noticias y Rutinas -->
    <div>
      <!-- Gestor de Noticias -->
      <div class="admin-card">
        <h2>Publicar Nueva Noticia</h2>
        <form id="formNoticia">
          <label>Título de la noticia</label>
          <input type="text" id="notiTitulo" required placeholder="Ej: Nuevos horarios de clases">
          
          <label>URL de la Imagen (opcional)</label>
          <input type="url" id="notiImagen" placeholder="https://...">
          
          <label>Contenido</label>
          <textarea id="notiContenido" rows="4" required placeholder="Escribe el cuerpo de la noticia aquí..."></textarea>
          
          <button type="submit" class="btn-primario" style="margin-top: 1rem;">Publicar Noticia</button>
          <p id="msgNoticia" style="color: var(--primario); font-weight: bold;"></p>
        </form>
      </div>

      <!-- Lista de Noticias -->
      <div class="admin-card">
        <h2>Últimas Noticias Publicadas</h2>
        <div id="listaNoticias">Cargando...</div>
              <!-- Gestión de Testimonios -->
      <div class="admin-card">
        <h2>Testimonios de Usuarios</h2>
        <div id="listaTestimoniosAdmin">Cargando...</div>
      </div>
      </div>
    </div>

    <!-- COLUMNA DERECHA: Accesos Rápidos -->
    <div>
      <div class="admin-card">
        <h2>Gestión de Contenido</h2>
        <p style="color: var(--suave); margin-bottom: 1.5rem;">Desde aquí puedes editar las rutinas, añadir videos e imágenes.</p>
        
        <a href="modulos/rutinas.php" class="btn-primario" style="display: block; text-align: center; margin-bottom: 1rem; text-decoration: none;">Gestionar Rutinas e Imágenes</a>
        <a href="modulos/crud.php?entidad=categorias" class="btn-secundario" style="display: block; text-align: center; margin-bottom: 1rem; text-decoration: none;">Gestionar Categorías</a>
        <a href="index.php" class="btn-secundario" style="display: block; text-align: center; text-decoration: none;">Ver el Sitio Público</a>
      </div>
    </div>
  </div>
</main>

<?php include 'includes/pie.php'; ?>

<script>
$(function () {
  // 1. Cargar Reportes
  $.getJSON('api/index.php', { accion: 'admin_stats' }, function (data) {
    $('#statUsuarios').text(data.usuarios);
    $('#statRutinas').text(data.rutinas);
    $('#statEjercicios').text(data.ejercicios);
    $('#statMinutos').text(data.minutos);
  });

  // 2. Cargar Noticias
  function cargarNoticias() {
    $.getJSON('api/index.php', { accion: 'noticias_listar' }, function (data) {
      if(!data.length) { $('#listaNoticias').html('<p>No hay noticias aún.</p>'); return; }
      
      $('#listaNoticias').html(data.map(n => `
        <div class="noticia-item">
          ${n.imagen_url ? `<img src="${n.imagen_url}" alt="Noticia">` : ''}
          <div>
            <h4 style="margin: 0 0 0.5rem 0;">${n.titulo}</h4>
            <p style="margin: 0; font-size: 0.9rem; color: var(--suave);">${n.contenido.substring(0, 100)}...</p>
          </div>
        </div>
      `).join(''));
    });
  }
  cargarNoticias();

  // 3. Guardar Noticia
  $('#formNoticia').on('submit', function (e) {
    e.preventDefault();
    const payload = {
      accion: 'noticia_crear',
      titulo: $('#notiTitulo').val(),
      contenido: $('#notiContenido').val(),
      imagen_url: $('#notiImagen').val()
    };
    
    $.post('api/index.php', payload, function (resp) {
      if (resp.ok) {
        $('#msgNoticia').text('¡Noticia publicada!');
        $('#formNoticia')[0].reset();
        cargarNoticias();
        setTimeout(() => $('#msgNoticia').text(''), 3000);
      } else {
        $('#msgNoticia').text('Error: ' + resp.mensaje);
      }
    }, 'json');
  });
});
  // 4. Cargar y borrar Testimonios (Admin)
  function cargarTestimoniosAdmin() {
    $.getJSON('api/index.php', { accion: 'testimonios_listar' }, function(data) {
      if(!data.length) { $('#listaTestimoniosAdmin').html('<p>No hay testimonios.</p>'); return; }
      
      $('#listaTestimoniosAdmin').html(data.map(t => `
        <div class="noticia-item" style="justify-content: space-between;">
          <div>
            <strong>${t.nombre}</strong>
            <p style="margin: 0; font-size: 0.9rem;">${t.comentario}</p>
          </div>
          <button onclick="borrarTestimonio(${t.id})" class="btn-secundario" style="background: #fee2e2; color: #b91c1c; padding: 0.5rem;">Eliminar</button>
        </div>
      `).join(''));
    });
  }
  cargarTestimoniosAdmin();

  window.borrarTestimonio = function(id) {
    if(!confirm('¿Eliminar este testimonio?')) return;
    $.post('api/index.php', { accion: 'testimonio_eliminar', id: id }, function(resp) {
      if(resp.ok) cargarTestimoniosAdmin();
    }, 'json');
  };
</script>
</body>
</html>