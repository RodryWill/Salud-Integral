<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Salud Integral — Bienestar para todos</title>
<link rel="stylesheet" href="Assets/CSS/estilos.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<style>
  /* Estilos exclusivos del index */
  .hero-section {
    background: linear-gradient(135deg, rgba(15, 118, 110, 0.9), rgba(20, 184, 166, 0.8)), url('https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?q=80&w=1600&auto=format&fit=crop') center/cover;
    color: white; padding: 5rem 2rem; text-align: center; border-radius: var(--radio); margin-bottom: 3rem;
  }
  .hero-section h1 { font-size: clamp(2.5rem, 5vw, 4rem); margin-bottom: 1rem; color: white; }
  .hero-section p { font-size: 1.2rem; max-width: 600px; margin: 0 auto 2rem auto; opacity: 0.9; }
  
  .section-title { text-align: center; font-size: 2rem; color: var(--primario); margin-bottom: 2rem; }
  
  /* Accesos rápidos */
  .quick-access { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 4rem; }
  @media(max-width: 768px) { .quick-access { grid-template-columns: 1fr; } }
  .access-card { background: white; padding: 2rem; border-radius: var(--radio); box-shadow: var(--sombra); display: flex; align-items: center; gap: 1.5rem; transition: 0.3s; text-decoration: none; color: var(--texto); }
  .access-card:hover { transform: translateY(-5px); box-shadow: var(--sombra-hover); border-left: 5px solid var(--primario); }
  .access-icon { font-size: 3rem; }
  .access-text h3 { margin: 0 0 0.5rem 0; color: var(--primario); }
  .access-text p { margin: 0; color: var(--suave); font-size: 0.9rem; }

  /* Testimonios */
  .testimonios-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
  .testimonio-card { background: white; padding: 1.5rem; border-radius: var(--radio); box-shadow: var(--sombra); border-left: 4px solid var(--primario); }
  .testimonio-card p { font-style: italic; color: var(--texto); margin-bottom: 1rem; }
  .testimonio-card strong { color: var(--suave); font-size: 0.9rem; }
  .form-testimonio { max-width: 600px; margin: 0 auto; background: white; padding: 2rem; border-radius: var(--radio); box-shadow: var(--sombra); }
</style>
</head>
<body>
<a href="#contenidoPrincipal" class="saltar">Saltar al contenido</a>
<?php include 'includes/cabecera.php'; ?>

<main id="contenidoPrincipal" class="dashboard" style="padding-top: 1rem;">
  
  <!-- HERO SECTION -->
  <section class="hero-section">
    <h1>Transforma tu bienestar</h1>
    <p>Plataforma profesional de salud integral, fisioterapia y rutinas adaptadas para todas las capacidades.</p>
    <?php if (!isset($_SESSION['usuario'])): ?>
      <a href="registro.php" class="btn-primario" style="background: white; color: var(--primario);">Comenzar Ahora</a>
    <?php else: ?>
      <a href="<?= $_SESSION['usuario']['rol'] === 'admin' ? 'dashboard.php' : 'lector_dashboard.php' ?>" class="btn-primario" style="background: white; color: var(--primario);">Ir a mi Panel</a>
    <?php endif; ?>
  </section>

  <!-- ACCESOS RÁPIDOS (DIETAS Y CALCULADORA) -->
  <h2 class="section-title">Herramientas Gratuitas</h2>
  <div class="quick-access">
    <a href="calculadora.php" class="access-card">
      <div class="access-icon">⚖️</div>
      <div class="access-text">
        <h3>Calculadora de IMC</h3>
        <p>Descubre tu Índice de Masa Corporal y tu peso ideal.</p>
      </div>
    </a>
    <a href="dietas.php" class="access-card">
      <div class="access-icon">🥗</div>
      <div class="access-text">
        <h3>Planes de Alimentación</h3>
        <p>Consejos y recetas para complementar tu entrenamiento.</p>
      </div>
    </a>
  </div>

  <!-- SECCIÓN DE RUTINAS (CATÁLOGO PÚBLICO) -->
  <h2 class="section-title" style="text-align: left;">Explora nuestras Rutinas</h2>
  <div class="filtros">
    <button class="pill activo" data-nivel="">Todas</button>
    <button class="pill" data-nivel="Principiante">Principiante</button>
    <button class="pill" data-nivel="Intermedio">Intermedio</button>
    <button class="pill" data-nivel="Avanzado">Avanzado</button>
  </div>
  <div id="listaRutinas" class="grid-rutinas" style="margin-bottom: 4rem;"></div>
    <!-- SECCIÓN QUIÉNES SOMOS (Misión y Visión) -->
  <h2 class="section-title">Sobre Nosotros</h2>
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 4rem;">
    <div class="access-card" style="flex-direction: column; text-align: center;">
      <h3 style="color: var(--primario); font-size: 1.5rem;">Nuestra Misión</h3>
      <p style="color: var(--suave); line-height: 1.6;">Proporcionar un acceso inclusivo y equitativo a programas de ejercicio y rehabilitación de alta calidad, utilizando tecnología accesible para mejorar la calidad de vida de las personas.</p>
    </div>
    <div class="access-card" style="flex-direction: column; text-align: center;">
      <h3 style="color: var(--primario); font-size: 1.5rem;">Nuestra Visión</h3>
      <p style="color: var(--suave); line-height: 1.6;">Ser la plataforma líder en bienestar digital, reconocida por su compromiso con la accesibilidad universal y la creación de una comunidad donde cada persona pueda alcanzar sus objetivos de salud.</p>
    </div>
  </div>

  <!-- CÓMO FUNCIONA -->
  <h2 class="section-title">¿Cómo funciona?</h2>
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 4rem;">
    <div style="text-align: center; padding: 2rem; background: white; border-radius: var(--radio); box-shadow: var(--sombra);">
      <div style="font-size: 3rem; margin-bottom: 1rem;">👤</div>
      <h3 style="color: var(--primario); margin-top: 0;">1. Regístrate</h3>
      <p style="color: var(--suave);">Crea tu cuenta para guardar tu progreso.</p>
    </div>
    <div style="text-align: center; padding: 2rem; background: white; border-radius: var(--radio); box-shadow: var(--sombra);">
      <div style="font-size: 3rem; margin-bottom: 1rem;">🏃</div>
      <h3 style="color: var(--primario); margin-top: 0;">2. Entrena</h3>
      <p style="color: var(--suave);">Elige rutinas adaptadas a tu nivel y capacidades.</p>
    </div>
    <div style="text-align: center; padding: 2rem; background: white; border-radius: var(--radio); box-shadow: var(--sombra);">
      <div style="font-size: 3rem; margin-bottom: 1rem;">📈</div>
      <h3 style="color: var(--primario); margin-top: 0;">3. Mejora</h3>
      <p style="color: var(--suave);">Sigue tu progreso y alcanza tus metas.</p>
    </div>
  </div>

  <!-- TESTIMONIOS -->
  <h2 class="section-title">Lo que dicen nuestros usuarios</h2>
  <div id="listaTestimonios" class="testimonios-grid"></div>

  <div class="form-testimonio">
    <h3 style="margin-top: 0; color: var(--primario);">Déjanos tu experiencia</h3>
    <form id="formTestimonio">
      <label>Tu Nombre</label>
      <input type="text" id="testNombre" required placeholder="Ej: Juan Pérez">
      
      <label>Tu Comentario</label>
      <textarea id="testComentario" rows="3" required placeholder="¿Cómo te ha ayudado la plataforma?" style="width:100%; padding:.9rem; border:1px solid var(--borde); border-radius:12px; font-family:inherit;"></textarea>
      
      <button type="submit" class="btn-primario" style="margin-top: 1rem; width: 100%;">Publicar Testimonio</button>
      <p id="msgTestimonio" style="text-align: center; color: var(--primario); font-weight: bold; margin-top: 1rem;"></p>
    </form>
  </div>

</main>

<?php include 'includes/pie.php'; ?>

<!-- Cargamos nuestro archivo global de JS (donde está el modo oscuro y las rutinas) -->
<script src="Assets/JS/app.js"></script>

<script>
// Scripts exclusivos para cargar Testimonios
$(function() {
  function cargarTestimonios() {
    $.getJSON('api/index.php', { accion: 'testimonios_listar' }, function(data) {
      if(!data.length) {
        $('#listaTestimonios').html('<p style="text-align:center; width:100%">Sé el primero en dejar un comentario.</p>');
        return;
      }
      $('#listaTestimonios').html(data.map(t => `
        <div class="testimonio-card">
          <p>"${t.comentario}"</p>
          <strong>— ${t.nombre}</strong>
        </div>
      `).join(''));
    });
  }
  cargarTestimonios();

  $('#formTestimonio').on('submit', function(e) {
    e.preventDefault();
    $.post('api/index.php', { 
      accion: 'testimonio_crear', 
      nombre: $('#testNombre').val(), 
      comentario: $('#testComentario').val() 
    }, function(resp) {
      if(resp.ok) {
        $('#msgTestimonio').text('¡Gracias por compartir tu experiencia!');
        $('#formTestimonio')[0].reset();
        cargarTestimonios();
        setTimeout(() => $('#msgTestimonio').text(''), 4000);
      }
    }, 'json');
  });
});
</script>
</body>
</html>