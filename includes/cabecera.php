<?php
$u = $_SESSION['usuario'] ?? null;
$en_modulos = (strpos($_SERVER['PHP_SELF'], '/modulos/') !== false);
$base = $en_modulos ? '../' : '';
?>
<header class="cabecera">
  <div style="display: flex; align-items: center; gap: 1rem;">
    <a class="logo" href="<?= $base ?>index.php">Salud Integral</a>
  </div>
  
  <nav style="display: flex; align-items: center; gap: 1.5rem;">
    <a href="<?= $base ?>index.php">Inicio</a>
    
    <?php if ($u): ?>
      <!-- Enlaces para TODOS los usuarios logueados (Admin y Lector) -->
      <a href="<?= $base ?>dietas.php">Dietas</a>
      <a href="<?= $base ?>calculadora.php">Calculadora IMC</a>
      
      <!-- Enlaces EXCLUSIVOS para el ADMINISTRADOR -->
      <?php if ($u['rol'] === 'admin'): ?>
        <a href="<?= $base ?>dashboard.php" style="color: var(--primario); font-weight: bold;">Mi Panel</a>
        <a href="<?= $base ?>modulos/rutinas.php">Rutinas</a>
        <a href="<?= $base ?>modulos/dietas_admin.php">Dietas Admin</a>
        <a href="<?= $base ?>modulos/crud.php?entidad=autores">Autores</a>
        <a href="<?= $base ?>modulos/crud.php?entidad=editoriales">Editoriales</a>
        <a href="<?= $base ?>modulos/crud.php?entidad=categorias">Categorías</a>
      <?php else: ?>
        <!-- Enlaces para el LECTOR -->
        <a href="<?= $base ?>lector_dashboard.php" style="color: var(--primario); font-weight: bold;">Mi Panel</a>
      <?php endif; ?>
      
      <a href="<?= $base ?>logout.php" style="color: #b91c1c; font-weight: bold;">Salir (<?= htmlspecialchars($u['nombre']) ?>)</a>
      
    <?php else: ?>
      <!-- Buscador Global (Solo visible si NO hay sesión, o puedes moverlo si quieres que esté siempre) -->
      <form action="index.php" method="GET" style="display: flex; align-items: center; margin-right: 1rem;">
        <input type="text" name="buscar" placeholder="Buscar rutina..." style="padding: 0.4rem 0.8rem; border-radius: 20px; border: 1px solid var(--borde); font-size: 0.9rem; width: 180px;">
      </form>
      
      <!-- Botones para invitados -->
      <a href="<?= $base ?>login.php" style="font-weight: bold;">Iniciar Sesión</a>
      <a href="<?= $base ?>registro.php" class="btn-primario" style="padding: 0.5rem 1rem; min-height: auto; font-size: 0.9rem;">Registrarse</a>
    <?php endif; ?>
    
    <!-- BOTÓN DE MODO OSCURO -->
    <button id="btnModoOscuro" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; padding: 0;" title="Cambiar Modo">🌙</button>
  </nav>
</header>