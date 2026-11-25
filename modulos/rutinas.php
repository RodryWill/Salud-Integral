<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Conexión a la base de datos para cargar la tabla
require_once '../config/conexion.php';

// Lógica para GUARDAR o EDITAR (Método POST tradicional)
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion_rutina'])) {
    $id = (int)($_POST['id'] ?? 0);
    $titulo = trim($_POST['titulo'] ?? '');
    $nivel = $_POST['nivel'] ?? '';
    $duracion = (int)($_POST['duracion'] ?? 0);
    $video = trim($_POST['video'] ?? '');
    $imagen = trim($_POST['imagen_url'] ?? '');
    $desc = trim($_POST['descripcion'] ?? '');

    if ($id > 0) {
        // Actualizar
        $stmt = $pdo->prepare('UPDATE rutinas SET titulo=?, nivel=?, duracion_minutos=?, video_url=?, descripcion=?, imagen_url=? WHERE id=?');
        $stmt->execute([$titulo, $nivel, $duracion, $video, $desc, $imagen, $id]);
        $mensaje = 'Rutina actualizada correctamente.';
    } else {
        // Crear nueva
        $stmt = $pdo->prepare('INSERT INTO rutinas (titulo, nivel, duracion_minutos, video_url, descripcion, imagen_url) VALUES (?,?,?,?,?,?)');
        $stmt->execute([$titulo, $nivel, $duracion, $video, $desc, $imagen]);
        $mensaje = 'Rutina creada correctamente.';
    }
}

// Lógica para ELIMINAR
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    $pdo->prepare('DELETE FROM rutinas WHERE id = ?')->execute([$id]);
    header('Location: rutinas.php?msg=eliminado');
    exit;
}

// Obtener todas las rutinas para la tabla
$rutinas = $pdo->query('SELECT * FROM rutinas ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Gestión de rutinas</title>
<link rel="stylesheet" href="../Assets/CSS/estilos.css">
</head>
<body>
<?php include '../includes/cabecera.php'; ?>
<main class="dashboard">
  <h1>Gestión de rutinas</h1>
  
  <?php if ($mensaje): ?>
    <p style="color: green; font-weight: bold; background: #dcfce7; padding: 1rem; border-radius: 8px;"><?= $mensaje ?></p>
  <?php endif; ?>
  <?php if (isset($_GET['msg']) && $_GET['msg'] === 'eliminado'): ?>
    <p style="color: #b91c1c; font-weight: bold; background: #fee2e2; padding: 1rem; border-radius: 8px;">Rutina eliminada.</p>
  <?php endif; ?>

  <!-- FORMULARIO TRADICIONAL (SIN JAVASCRIPT) -->
  <form method="POST" action="rutinas.php" class="auth-card" style="max-width:100%; margin-bottom: 2rem;">
    <input type="hidden" name="accion_rutina" value="1">
    <input type="hidden" name="id" id="form_id" value="0">
    
    <label>Título</label>
    <input type="text" name="titulo" id="form_titulo" required maxlength="150">

    <label>Nivel</label>
    <select name="nivel" id="form_nivel" required>
      <option value="Principiante">Principiante</option>
      <option value="Intermedio">Intermedio</option>
      <option value="Avanzado">Avanzado</option>
    </select>

    <label>Duración (min)</label>
    <input type="number" name="duracion" id="form_duracion" min="1" required>

    <label>URL del video (MP4)</label>
    <input type="url" name="video" id="form_video">

    <label>URL de la Imagen de Portada</label>
    <input type="url" name="imagen_url" id="form_imagen" placeholder="https://...">

    <label>Descripción</label>
    <input type="text" name="descripcion" id="form_desc">

    <button type="submit" class="btn-primario">Guardar Rutina</button>
    <button type="button" class="btn-secundario" onclick="document.getElementById('form_id').value='0'; this.form.reset();">Limpiar / Nueva</button>
  </form>

  <!-- TABLA DE RUTINAS -->
  <table class="tabla" style="width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: var(--sombra);">
    <thead style="background: var(--fondo); text-align: left;">
      <tr>
        <th style="padding: 1rem;">Título</th>
        <th style="padding: 1rem;">Nivel</th>
        <th style="padding: 1rem;">Min</th>
        <th style="padding: 1rem;">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rutinas as $r): ?>
      <tr style="border-bottom: 1px solid var(--borde);">
        <td style="padding: 1rem;"><?= htmlspecialchars($r['titulo']) ?></td>
        <td style="padding: 1rem;"><?= htmlspecialchars($r['nivel']) ?></td>
        <td style="padding: 1rem;"><?= htmlspecialchars($r['duracion_minutos']) ?></td>
        <td style="padding: 1rem;">
          <!-- Botón Editar que rellena el formulario con JavaScript -->
          <button type="button" class="btn-secundario" onclick="editarRutina(
            <?= $r['id'] ?>, 
            '<?= addslashes($r['titulo']) ?>', 
            '<?= $r['nivel'] ?>', 
            <?= $r['duracion_minutos'] ?>, 
            '<?= addslashes($r['video_url'] ?? '') ?>', 
            '<?= addslashes($r['imagen_url'] ?? '') ?>', 
            '<?= addslashes($r['descripcion'] ?? '') ?>'
          )">Editar</button>
          
          <a href="rutinas.php?eliminar=<?= $r['id'] ?>" class="btn-secundario" style="text-decoration:none; background:#fee2e2; color:#b91c1c;" onclick="return confirm('¿Eliminar esta rutina?')">Eliminar</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>

<script>
// Función para pasar los datos de la tabla al formulario de arriba
function editarRutina(id, titulo, nivel, duracion, video, imagen, desc) {
  document.getElementById('form_id').value = id;
  document.getElementById('form_titulo').value = titulo;
  document.getElementById('form_nivel').value = nivel;
  document.getElementById('form_duracion').value = duracion;
  document.getElementById('form_video').value = video;
  document.getElementById('form_imagen').value = imagen;
  document.getElementById('form_desc').value = desc;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
</body>
</html>