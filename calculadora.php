<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Calculadora IMC — Salud Integral</title>
<link rel="stylesheet" href="Assets/CSS/estilos.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<style>
  .imc-container { display: flex; justify-content: center; padding: 2rem 0; }
  .imc-card { background: #fff; padding: 2rem; border-radius: var(--radio); box-shadow: var(--sombra); width: 100%; max-width: 500px; }
  .form-group { margin-bottom: 1.5rem; }
  .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--texto); }
  .form-group input { width: 100%; padding: 0.9rem; border: 1px solid var(--borde); border-radius: 8px; font-size: 1rem; outline: none; }
  .form-group input:focus { border-color: var(--primario); }
  
  /* Caja de resultados */
  #resultadoIMC { display: none; margin-top: 2rem; padding: 1.5rem; border-radius: 12px; text-align: center; background: var(--fondo); }
  #resultadoIMC h3 { margin-top: 0; font-size: 1.5rem; }
  #resultadoIMC p { color: var(--suave); margin-bottom: 1rem; }
  
  /* Colores según el estado */
  .bajo-peso { background: #e0f2fe; border: 2px solid #38bdf8; color: #0369a1; }
  .normal { background: #dcfce7; border: 2px solid #4ade80; color: #166534; }
  .sobrepeso { background: #fef9c3; border: 2px solid #facc15; color: #854d0e; }
  .obesidad { background: #fee2e2; border: 2px solid #f87171; color: #991b1b; }
  
  .recomendacion { background: #fff; padding: 1rem; border-radius: 8px; margin-top: 1rem; text-align: left; border-left: 4px solid var(--primario); box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
</style>
</head>
<body>
<?php include 'includes/cabecera.php'; ?>

<main class="dashboard">
  <div style="text-align: center; margin-bottom: 2rem;">
    <h1 style="color: var(--primario);">Calculadora de Índice de Masa Corporal (IMC)</h1>
    <p style="color: var(--suave);">Conoce cómo está tu salud según tu peso y altura.</p>
  </div>

  <div class="imc-container">
    <div class="imc-card">
      <form id="formIMC">
        <div class="form-group">
          <label for="peso">Peso (en kg):</label>
          <input type="number" id="peso" step="0.1" min="1" required placeholder="Ej: 70.5">
        </div>
        <div class="form-group">
          <label for="altura">Altura (en cm):</label>
          <input type="number" id="altura" step="1" min="50" required placeholder="Ej: 175">
        </div>
        <button type="submit" class="btn-primario" style="width: 100%;">Calcular mi IMC</button>
      </form>

      <!-- Aquí aparecerá el resultado -->
      <div id="resultadoIMC">
        <h3 id="tituloResultado"></h3>
        <p id="textoIMC"></p>
        <div id="cajaRecomendacion" class="recomendacion"></div>
      </div>
    </div>
  </div>
</main>

<?php include 'includes/pie.php'; ?>

<script>
$(function () {
  $('#formIMC').on('submit', function (e) {
    e.preventDefault();

    const peso = parseFloat($('#peso').val());
    const alturaCm = parseFloat($('#altura').val());
    
    if (peso <= 0 || alturaCm <= 0) {
      alert("Por favor, ingresa valores válidos.");
      return;
    }

    // Fórmula del IMC
    const alturaM = alturaCm / 100;
    const imc = peso / (alturaM * alturaM);
    const imcRedondeado = imc.toFixed(2);

    let titulo = '';
    let claseColor = '';
    let recomendacion = '';
    let pesoExtra = '';
    let diferenciaKilos = 0;

    // Calcular el peso ideal (usando un IMC de 22 como punto medio saludable)
    const pesoIdeal = 22 * (alturaM * alturaM);
    diferenciaKilos = (peso - pesoIdeal).toFixed(1);

    // Evaluar el IMC
    if (imc < 18.5) {
      titulo = 'Bajo peso';
      claseColor = 'bajo-peso';
      pesoExtra = `Te faltan aproximadamente <b>${Math.abs(diferenciaKilos)} kg</b> para alcanzar tu peso ideal.`;
      recomendacion = '<b>Recomendación:</b> Te sugerimos enfocarte en una rutina de <b>Fuerza y Ganancia Muscular</b> y aumentar tu ingesta calórica de forma saludable.';
    } else if (imc >= 18.5 && imc < 24.9) {
      titulo = 'Peso normal / Ideal';
      claseColor = 'normal';
      pesoExtra = `¡Estás en tu peso ideal! No te sobran kilos.`;
      recomendacion = '<b>¡Felicidades!</b> Te recomendamos mantenerte activo con nuestra rutina de <b>Cardio Adaptado</b> y ejercicios de movilidad.';
    } else if (imc >= 25 && imc < 29.9) {
      titulo = 'Sobrepeso';
      claseColor = 'sobrepeso';
      // Aquí calculamos cuánto le sobra para llegar a un IMC de 24.9 (el límite superior normal)
      const pesoLimiteNormal = 24.9 * (alturaM * alturaM);
      const kilosSobrantes = (peso - pesoLimiteNormal).toFixed(1);
      pesoExtra = `Te sobran aproximadamente <b>${kilosSobrantes} kg</b> para llegar a tu peso normal.`;
      recomendacion = '<b>Recomendación:</b> Te vendría bien aumentar tu actividad física. Te sugerimos empezar con nuestra rutina de <b>Cardio en Casa</b> (bajo impacto) y cuidar las porciones de tus comidas.';
    } else if (imc >= 30) {
      titulo = 'Obesidad';
      claseColor = 'obesidad';
      const pesoLimiteNormal = 24.9 * (alturaM * alturaM);
      const kilosSobrantes = (peso - pesoLimiteNormal).toFixed(1);
      pesoExtra = `Te sobran aproximadamente <b>${kilosSobrantes} kg</b> para llegar a tu peso normal.`;
      recomendacion = '<b>Recomendación:</b> Es importante que consultes con un médico. Para empezar a moverte, te recomendamos nuestra rutina de <b>Fisioterapia y Rehabilitación</b>.';
    }

    // Mostrar el resultado en pantalla
    $('#tituloResultado').text(`Tu IMC es: ${imcRedondeado} (${titulo})`);
    
    // Aquí mostramos la diferencia de kilos explicada
    $('#textoIMC').html(`
      Según la Organización Mundial de la Salud (OMS), un IMC entre 18.5 y 24.9 es saludable.<br><br>
      <strong style="font-size: 1.2rem; color: #1f2937;">${pesoExtra}</strong>
    `);
    
    $('#cajaRecomendacion').html(recomendacion);

    // Aplicar el color correspondiente
    const $resultado = $('#resultadoIMC');
    $resultado.removeClass('bajo-peso normal sobrepeso obesidad').addClass(claseColor);
    
    // Mostrar la caja con una pequeña animación
    $resultado.slideDown();
  });
});
</script>
</body>
</html>