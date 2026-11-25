$(function () {
  
  // ==========================================
  // 1. MODO OSCURO
  // ==========================================
  const btnModo = document.getElementById('btnModoOscuro');
  if (btnModo) {
    if (localStorage.getItem('modoOscuro') === 'true') {
      document.body.classList.add('dark-mode');
      btnModo.textContent = '☀️';
    }
    btnModo.addEventListener('click', () => {
      document.body.classList.toggle('dark-mode');
      if (document.body.classList.contains('dark-mode')) {
        localStorage.setItem('modoOscuro', 'true');
        btnModo.textContent = '☀️';
      } else {
        localStorage.setItem('modoOscuro', 'false');
        btnModo.textContent = '🌙';
      }
    });
  }

  // ==========================================
  // 2. CATÁLOGO DE RUTINAS (Página de Inicio)
  // ==========================================
  function cargarRutinas(nivel) {
    // Si no existe el contenedor en esta página, no hacemos nada
    if ($('#listaRutinas').length === 0) return; 

    $('#listaRutinas').html('<p style="text-align:center; width:100%;">Cargando rutinas...</p>');
    
    // Petición a la API
    $.getJSON('api/index.php', { accion: 'rutinas', nivel: nivel }, function (data) {
      
      // Si la API devuelve un error o viene vacía
      if (!data || !data.length) {
        $('#listaRutinas').html('<p style="text-align:center; width:100%;">No hay rutinas disponibles en este momento.</p>');
        return;
      }

      // Generar las tarjetas
      const html = data.map(r => {
        // Imagen por defecto si la rutina no tiene una
        const imgUrl = r.imagen_url ? r.imagen_url : 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?q=80&w=400&auto=format&fit=crop';
        
        return `
        <article class="workout-card" onclick="window.location.href='ejercicios.php?id=${r.id}'" style="cursor: pointer;">
          <div class="wc-media">
            <img src="${imgUrl}" alt="Rutina ${r.nivel}">
            <span class="badge">${r.nivel}</span>
          </div>
          <div class="wc-body">
            <h3>${r.titulo}</h3>
            <p>${r.duracion_minutos} min — ${r.descripcion || ''}</p>
          </div>
        </article>`;
      }).join('');
      
      $('#listaRutinas').html(html);

    }).fail(function (jqxhr, textStatus, error) {
      // Si la API falla (error 404, 500, etc.)
      console.error("Error en la API:", textStatus, error);
      $('#listaRutinas').html('<p style="text-align:center; width:100%; color: #b91c1c;">Error al cargar rutinas. Revisa la consola o la API.</p>');
    });
  }

  // Cargar todas las rutinas al inicio
  cargarRutinas('');

  // Evento de los filtros (Principiante, Intermedio, etc.)
  $('.filtros').on('click', '.pill', function () {
    $('.pill').removeClass('activo');
    $(this).addClass('activo');
    cargarRutinas($(this).data('nivel'));
  });

  // ==========================================
  // 3. LOGIN Y REGISTRO
  // ==========================================
  $('#formLogin').on('submit', function (e) {
    e.preventDefault();
    const email = $('#email').val().trim();
    const password = $('#password').val();
    if (!email || password.length < 1 || password.length > 8) {
      $('#mensajeLogin').text('Revisa el correo y la contraseña (1–8 caracteres).');
      return;
    }
    $.ajax({
      url: 'api/index.php?accion=login',
      method: 'POST',
      data: { email, password },
      dataType: 'json',
      beforeSend: function () { $('#mensajeLogin').text('Validando…'); },
      success: function (resp) {
        if (resp.ok) { window.location.href = resp.redirect; }
        else { $('#mensajeLogin').text(resp.mensaje || 'Credenciales incorrectas.'); }
      },
      error: function () { $('#mensajeLogin').text('Error de conexión.'); }
    });
  });

  $('#formRegistro').on('submit', function (e) {
    e.preventDefault();
    const nombre = $('#nombre').val().trim();
    const email = $('#emailReg').val().trim();
    const password = $('#passwordReg').val();
    const password2 = $('#passwordReg2').val();
    
    if (nombre.length < 2 || !email) {
      $('#mensajeRegistro').text('Revisa tu nombre y correo.');
      return;
    }
    if (password !== password2) {
      $('#mensajeRegistro').text('Las contraseñas no coinciden.');
      return;
    }
    
    $.ajax({
      url: 'api/index.php?accion=registro',
      method: 'POST',
      data: { nombre, email, password },
      dataType: 'json',
      beforeSend: function () { $('#mensajeRegistro').text('Creando cuenta…'); },
      success: function (resp) {
        if (resp.ok) { window.location.href = 'login.php'; }
        else { $('#mensajeRegistro').text(resp.mensaje || 'No se pudo registrar.'); }
      },
      error: function () { $('#mensajeRegistro').text('Error de conexión.'); }
    });
  });

});