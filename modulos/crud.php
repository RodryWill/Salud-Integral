<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ../login.php'); exit;
}
$entidad = $_GET['entidad'] ?? 'autores';
$permitidas = ['autores','editoriales','categorias'];
if (!in_array($entidad, $permitidas, true)) { header('Location: rutinas.php'); exit; }

$config = [
  'autores'     => ['titulo'=>'Autores',     'campos'=>['nombre','nacionalidad'], 'etiquetas'=>['Nombre','Nacionalidad']],
  'editoriales' => ['titulo'=>'Editoriales', 'campos'=>['nombre','pais'],         'etiquetas'=>['Nombre','País']],
  'categorias'  => ['titulo'=>'Categorías',  'campos'=>['nombre'],                'etiquetas'=>['Nombre']]
][$entidad];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $config['titulo'] ?> — Salud Integral</title>
<link rel="stylesheet" href="../Assets/CSS/estilos.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<?php include '../includes/cabecera.php'; ?>
<main class="dashboard">
  <h1>Gestión de <?= $config['titulo'] ?></h1>

  <form id="formCrud" class="auth-card" style="max-width:100%">
    <input type="hidden" id="crudId">
    <input type="hidden" id="crudEntidad" value="<?= $entidad ?>">
    <?php foreach ($config['campos'] as $i => $campo): ?>
      <label for="<?= $campo ?>"><?= $config['etiquetas'][$i] ?></label>
      <input type="text" id="<?= $campo ?>" name="<?= $campo ?>" maxlength="150">
    <?php endforeach; ?>
    <button type="submit" class="btn-primario">Guardar</button>
    <button type="button" id="btnLimpiarCrud" class="btn-secundario">Limpiar</button>
    <p id="msgCrud" role="alert"></p>
  </form>

  <div id="tablaCrud"></div>
</main>
<script>
$(function () {
  const entidad = $('#crudEntidad').val();
  const campos = <?= json_encode($config['campos']) ?>;

  function listar() {
    $.getJSON('../api/index.php', { accion: 'crud_listar', entidad: entidad }, function (data) {
      if (!data.length) { $('#tablaCrud').html('<p>Sin registros aún.</p>'); return; }
      const cabecera = campos.map(c => `<th>${c}</th>`).join('');
      const filas = data.map(r => {
        const celdas = campos.map(c => `<td>${r[c] ?? ''}</td>`).join('');
        return `<tr>${celdas}<td>
          <button class="btn-secundario btn-edit-crud" data-id="${r.id}"
            ${campos.map(c => `data-${c}="${(r[c] ?? '').toString().replace(/"/g,'&quot;')}"`).join(' ')}>Editar</button>
          <button class="btn-secundario btn-del-crud" data-id="${r.id}">Eliminar</button>
        </td></tr>`;
      }).join('');
      $('#tablaCrud').html(`<table class="tabla"><thead><tr>${cabecera}<th>Acciones</th></tr></thead><tbody>${filas}</tbody></table>`);
    });
  }
  listar();

  $('#formCrud').on('submit', function (e) {
    e.preventDefault();
    const id = $('#crudId').val();
    const payload = { accion: id ? 'crud_actualizar' : 'crud_crear', entidad, id };
    campos.forEach(c => payload[c] = $('#' + c).val().trim());

    $.post('../api/index.php', payload, function (resp) {
      $('#msgCrud').text(resp.ok ? 'Guardado.' : 'Error: ' + resp.mensaje);
      if (resp.ok) { $('#formCrud')[0].reset(); $('#crudId').val(''); listar(); }
    }, 'json').fail(function () { $('#msgCrud').text('Error de conexión.'); });
  });

  $('#btnLimpiarCrud').on('click', function () {
    $('#formCrud')[0].reset(); $('#crudId').val(''); $('#msgCrud').text('');
  });

  $('#tablaCrud').on('click', '.btn-edit-crud', function () {
    $('#crudId').val($(this).data('id'));
    campos.forEach(c => $('#' + c).val($(this).data(c)));
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  $('#tablaCrud').on('click', '.btn-del-crud', function () {
    if (!confirm('¿Eliminar registro?')) return;
    $.post('../api/index.php', { accion: 'crud_eliminar', entidad, id: $(this).data('id') },
      function (resp) { if (resp.ok) listar(); }, 'json');
  });
});
</script>
</body>
</html>