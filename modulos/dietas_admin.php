<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Conexión a la base de datos
require_once '../config/conexion.php';

$mensaje = '';
$colorMensaje = 'green';

// Lógica para GUARDAR o EDITAR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion_dieta'])) {
    $id = (int)($_POST['id'] ?? 0);
    $titulo = trim($_POST['titulo'] ?? '');
    $tipo = $_POST['tipo'] ?? 'Almuerzo';
    $calorias = (int)($_POST['calorias'] ?? 0);
    $imagen = trim($_POST['imagen_url'] ?? '');
    $desc = trim($_POST['descripcion'] ?? '');
    $ingredientes = trim($_POST['ingredientes'] ?? '');
    $preparacion = trim($_POST['preparacion'] ?? '');
    $consejos = trim($_POST['consejos'] ?? '');

    try {
        if ($id > 0) {
            // Actualizar
            $stmt = $pdo->prepare('UPDATE dietas SET titulo=?, tipo=?, calorias=?, descripcion=?, imagen_url=?, ingredientes=?, preparacion=?, consejos=? WHERE id=?');
            $stmt->execute([$titulo, $tipo, $calorias, $desc, $imagen, $ingredientes, $preparacion, $consejos, $id]);
            $mensaje = 'Dieta actualizada correctamente.';
        } else {
            // Crear nueva
            $stmt = $pdo->prepare('INSERT INTO dietas (titulo, tipo, calorias, descripcion, imagen_url, ingredientes, preparacion, consejos) VALUES (?,?,?,?,?,?,?,?)');
            $stmt->execute([$titulo, $tipo, $calorias, $desc, $imagen, $ingredientes, $preparacion, $consejos]);
            $mensaje = 'Dieta creada correctamente.';
        }
    } catch (PDOException $e) {
        $mensaje = 'Error en BD: ' . $e->getMessage();
        $colorMensaje = 'red';
    }
}

// Lógica para ELIMINAR
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    $pdo->prepare('DELETE FROM dietas WHERE id = ?')->execute([$id]);
    header('Location: dietas_admin.php?msg=eliminado');
    exit;
}

// Obtener todas las dietas
$dietas = $pdo->query('SELECT * FROM dietas ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Gestión de Dietas</title>
<link rel="stylesheet" href="../Assets/CSS/estilos.css">
</head>
<body>
<?php include '../includes/cabecera.php'; ?>
<main class="dashboard">
  <h1>Gestión de Dietas</h1>
  
  <?php if ($mensaje): ?>
    <p style="color: <?= $colorMensaje ?>; font-weight: bold; background: <?= $colorMensaje === 'green' ? '#dcfce7' : '#fee2e2' ?>; padding: 1rem; border-radius: 8px;"><?= $mensaje ?></p>
  <?php endif; ?>
  <?php if (isset($_GET['msg']) && $_GET['msg'] === 'eliminado'): ?>
    <p style="color: #b91c1c; font-weight: bold; background: #fee2e2; padding: 1rem; border-radius: 8px;">Dieta eliminada.</p>
  <?php endif; ?>

  <form method="POST" action="dietas_admin.php" class="auth-card" style="max-width:100%; margin-bottom: 2rem;">
    <input type="hidden" name="accion_dieta" value="1">
    <input type="hidden" name="id" id="form_id" value="0">
    
    <label>Título</label>
    <input type="text" name="titulo" id="form_titulo" required maxlength="150">

    <label>Tipo de comida</label>
    <select name="tipo" id="form_tipo" required>
      <option value="Desayuno">Desayuno</option>
      <option value="Almuerzo">Almuerzo</option>
      <option value="Cena">Cena</option>
      <option value="Snack">Snack</option>
    </select>

    <label>Calorías (kcal)</label>
    <input type="number" name="calorias" id="form_calorias" min="1" required>

    <label>URL de la Imagen</label>
    <input type="url" name="imagen_url" id="form_imagen" placeholder="https://...">

    <label>Descripción corta</label>
    <input type="text" name="descripcion" id="form_desc">

    <label>Ingredientes</label>
    <textarea name="ingredientes" id="form_ingredientes" rows="3" style="width:100%; padding:.9rem 1rem; border:1px solid var(--borde); border-radius:12px; font:inherit;"></textarea>

    <label>Preparación paso a paso</label>
    <textarea name="preparacion" id="form_preparacion" rows="4" style="width:100%; padding:.9rem 1rem; border:1px solid var(--borde); border-radius:12px; font:inherit;"></textarea>

    <label>Consejos o Beneficios</label>
    <input type="text" name="consejos" id="form_consejos">

    <button type="submit" class="btn-primario" style="margin-top:1rem; width: 100%;">Guardar Dieta</button>
    <button type="button" class="btn-secundario" style="margin-top:1rem; width: 100%;" onclick="document.getElementById('form_id').value='0'; this.form.reset();">Limpiar / Nueva</button>
  </form>

  <!-- TABLA DE DIETAS -->
  <table class="tabla" style="width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: var(--sombra);">
    <thead style="background: var(--fondo); text-align: left;">
      <tr>
        <th style="padding: 1rem;">Título</th>
        <th style="padding: 1rem;">Tipo</th>
        <th style="padding: 1rem;">Calorías</th>
        <th style="padding: 1rem;">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($dietas as $d): ?>
      <tr style="border-bottom: 1px solid var(--borde);">
        <td style="padding: 1rem;"><?= htmlspecialchars($d['titulo']) ?></td>
        <td style="padding: 1rem;"><?= htmlspecialchars($d['tipo']) ?></td>
        <td style="padding: 1rem;"><?= htmlspecialchars($d['calorias']) ?> kcal</td>
        <td style="padding: 1rem;">
          <!-- Botón Editar con Javascript simple -->
          <button type="button" class="btn-secundario" onclick="editarDieta(
            <?= $d['id'] ?>, 
            '<?= addslashes($d['titulo']) ?>', 
            '<?= $d['tipo'] ?>', 
            <?= $d['calorias'] ?>, 
            '<?= addslashes($d['imagen_url'] ?? '') ?>', 
            '<?= addslashes($d['descripcion'] ?? '') ?>', 
            '<?= addslashes($d['ingredientes'] ?? '') ?>', 
            '<?= addslashes($d['preparacion'] ?? '') ?>', 
            '<?= addslashes($d['consejos'] ?? '') ?>'
          )">Editar</button>
          
          <!-- Botón Eliminar -->
          <a href="dietas_admin.php?eliminar=<?= $d['id'] ?>" class="btn-secundario" style="text-decoration:none; background:#fee2e2; color:#b91c1c; padding: 0.7rem 1rem;" onclick="return confirm('¿Eliminar esta dieta?')">Eliminar</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>

<script>
function editarDieta(id, titulo, tipo, calorias, imagen, desc, ingredientes, preparacion, consejos) {
  document.getElementById('form_id').value = id;
  document.getElementById('form_titulo').value = titulo;
  document.getElementById('form_tipo').value = tipo;
  document.getElementById('form_calorias').value = calorias;
  document.getElementById('form_imagen').value = imagen;
  document.getElementById('form_desc').value = desc;
  document.getElementById('form_ingredientes').value = ingredientes;
  document.getElementById('form_preparacion').value = preparacion;
  document.getElementById('form_consejos').value = consejos;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
</body>
</html>