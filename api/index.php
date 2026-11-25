<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/conexion.php';

$accion = $_GET['accion'] ?? '';

// 1. KPIS
if ($accion === 'kpis' && isset($_SESSION['usuario'])) {
    $uid = $_SESSION['usuario']['id'];
    $stmt = $pdo->prepare('SELECT COALESCE(SUM(minutos),0) AS total FROM progreso WHERE usuario_id = ? AND fecha = CURDATE()');
    $stmt->execute([$uid]);
    $min = (int)$stmt->fetchColumn();
    echo json_encode(['minutos_hoy' => $min, 'calorias' => $min * 7, 'agua' => min(8, round($min/5))]);
    exit;
}

// 2. GRÁFICO
if ($accion === 'progreso_semana' && isset($_SESSION['usuario'])) {
    $stmt = $pdo->prepare("SELECT DAYOFWEEK(fecha) AS dia, SUM(minutos) AS total FROM progreso WHERE usuario_id = ? AND fecha >= CURDATE() - INTERVAL 6 DAY GROUP BY DAYOFWEEK(fecha)");
    $stmt->execute([$_SESSION['usuario']['id']]);
    $mapa = [];
    foreach ($stmt->fetchAll() as $fila) { $mapa[(int)$fila['dia']] = (int)$fila['total']; }
    $orden = [2,3,4,5,6,7,1];
    $etiquetas = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];
    $resp = [];
    foreach ($orden as $i => $dia) { $resp[] = ['dia' => $etiquetas[$i], 'minutos' => $mapa[$dia] ?? 0]; }
    echo json_encode($resp);
    exit;
}

// 3. RUTINAS
if ($accion === 'rutinas') {
    $nivel = $_GET['nivel'] ?? '';
    $sql = 'SELECT * FROM rutinas';
    $params = [];
    if (in_array($nivel, ['Principiante','Intermedio','Avanzado'])) {
        $sql .= ' WHERE nivel = ?'; $params[] = $nivel;
    }
    $stmt = $pdo->prepare($sql); $stmt->execute($params);
    echo json_encode($stmt->fetchAll());
    exit;
}

// 4. DIETAS
if ($accion === 'dietas') {
    $stmt = $pdo->query('SELECT * FROM dietas ORDER BY id DESC');
    echo json_encode($stmt->fetchAll());
    exit;
}

// 4.5 DETALLE DIETA
if ($accion === 'detalle_dieta') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM dietas WHERE id = ?'); 
    $stmt->execute([$id]);
    $dieta = $stmt->fetch();
    if (!$dieta) { echo json_encode(['error' => 'Dieta no encontrada']); exit; }
    echo json_encode($dieta);
    exit;
}

// 5. DETALLE RUTINA
if ($accion === 'detalle_rutina') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM rutinas WHERE id = ?'); $stmt->execute([$id]);
    $rutina = $stmt->fetch();
    if (!$rutina) { echo json_encode(['error' => 'Rutina no encontrada']); exit; }
    $stmt = $pdo->prepare('SELECT * FROM ejercicios WHERE rutina_id = ?'); $stmt->execute([$id]);
    echo json_encode(['rutina' => $rutina, 'ejercicios' => $stmt->fetchAll()]);
    exit;
}

// 6. DETALLE EJERCICIO
if ($accion === 'ejercicio_detalle') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM ejercicios WHERE id = ?'); $stmt->execute([$id]);
    $ejercicio = $stmt->fetch();
    if (!$ejercicio) { echo json_encode(['error' => 'Ejercicio no encontrado']); exit; }
    echo json_encode($ejercicio);
    exit;
}

// 7. GUARDAR PROGRESO
if ($accion === 'progreso_guardar' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario'])) {
    $min = (int)($_POST['minutos'] ?? 0);
    $stmt = $pdo->prepare('INSERT INTO progreso (usuario_id, minutos, fecha) VALUES (?, ?, CURDATE())');
    $stmt->execute([$_SESSION['usuario']['id'], $min]);
    echo json_encode(['ok' => true]);
    exit;
}

// 8. LOGIN Y REGISTRO
if ($accion === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ?'); $stmt->execute([$email]);
    $u = $stmt->fetch();
    if ($u && password_verify($password, $u['password'])) {
        $_SESSION['usuario'] = ['id' => $u['id'], 'nombre' => $u['nombre'], 'rol' => $u['rol']];
        echo json_encode(['ok' => true, 'redirect' => $u['rol'] === 'admin' ? 'dashboard.php' : 'lector_dashboard.php']);
    } else {
        echo json_encode(['ok' => false, 'mensaje' => 'Credenciales inválidas']);
    }
    exit;
}

if ($accion === 'registro' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    if (strlen($nombre) < 2 || !$email || strlen($password) < 1 || strlen($password) > 8) { echo json_encode(['ok' => false, 'mensaje' => 'Datos inválidos.']); exit; }
    $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?'); $stmt->execute([$email]);
    if ($stmt->fetch()) { echo json_encode(['ok' => false, 'mensaje' => 'El correo ya está registrado.']); exit; }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, email, password, rol) VALUES (?,?,?, "lector")');
    $stmt->execute([$nombre, $email, $hash]);
    echo json_encode(['ok' => true]);
    exit;
}

// 9. CRUD ADMINISTRADOR
if (strpos($accion, 'crud_') === 0 || in_array($accion, ['rutina_crear','rutina_actualizar','rutina_eliminar', 'dieta_crear', 'dieta_actualizar', 'dieta_eliminar'])) {
    if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') { 
        http_response_code(403); echo json_encode(['ok'=>false,'mensaje'=>'No autorizado']); exit; 
    }

    // --- CRUD RUTINAS ---
    if (in_array($accion, ['rutina_crear','rutina_actualizar','rutina_eliminar'])) {
        if ($accion === 'rutina_eliminar') {
            $pdo->prepare('DELETE FROM rutinas WHERE id = ?')->execute([(int)$_POST['id']]);
            echo json_encode(['ok' => true]); exit;
        }
        $titulo = trim($_POST['titulo'] ?? ''); $nivel = $_POST['nivel'] ?? ''; 
        $duracion = (int)($_POST['duracion_minutos'] ?? 0); $video = trim($_POST['video_url'] ?? ''); 
        $desc = trim($_POST['descripcion'] ?? ''); $imagen = trim($_POST['imagen_url'] ?? ''); 

        if ($accion === 'rutina_crear') {
            $pdo->prepare('INSERT INTO rutinas (titulo, nivel, duracion_minutos, video_url, descripcion, imagen_url) VALUES (?,?,?,?,?,?)')->execute([$titulo, $nivel, $duracion, $video, $desc, $imagen]);
        } else {
            $pdo->prepare('UPDATE rutinas SET titulo=?, nivel=?, duracion_minutos=?, video_url=?, descripcion=?, imagen_url=? WHERE id=?')->execute([$titulo, $nivel, $duracion, $video, $desc, $imagen, (int)$_POST['id']]);
        }
        echo json_encode(['ok' => true]); exit;
    }

    // --- CRUD DIETAS ---
    if (in_array($accion, ['dieta_crear','dieta_actualizar','dieta_eliminar'])) {
        if ($accion === 'dieta_eliminar') {
            $pdo->prepare('DELETE FROM dietas WHERE id = ?')->execute([(int)$_POST['id']]);
            echo json_encode(['ok' => true]); exit;
        }
        
        $titulo = trim($_POST['titulo'] ?? ''); $tipo = $_POST['tipo'] ?? 'Almuerzo'; 
        $calorias = (int)($_POST['calorias'] ?? 0); $desc = trim($_POST['descripcion'] ?? ''); 
        $imagen = trim($_POST['imagen_url'] ?? ''); 
        $ingredientes = trim($_POST['ingredientes'] ?? '');
        $preparacion = trim($_POST['preparacion'] ?? '');
        $consejos = trim($_POST['consejos'] ?? '');

        if ($accion === 'dieta_crear') {
            $pdo->prepare('INSERT INTO dietas (titulo, tipo, calorias, descripcion, imagen_url, ingredientes, preparacion, consejos) VALUES (?,?,?,?,?,?,?,?)')
                ->execute([$titulo, $tipo, $calorias, $desc, $imagen, $ingredientes, $preparacion, $consejos]);
        } else {
            $pdo->prepare('UPDATE dietas SET titulo=?, tipo=?, calorias=?, descripcion=?, imagen_url=?, ingredientes=?, preparacion=?, consejos=? WHERE id=?')
                ->execute([$titulo, $tipo, $calorias, $desc, $imagen, $ingredientes, $preparacion, $consejos, (int)$_POST['id']]);
        }
        echo json_encode(['ok' => true]); exit;
    }

    // --- CRUD AUTORES/EDITORIALES/CATEGORIAS ---
    $mapa = ['autores'=>['tabla'=>'autores','campos'=>['nombre','nacionalidad']], 'editoriales'=>['tabla'=>'editoriales','campos'=>['nombre','pais']], 'categorias'=>['tabla'=>'categorias','campos'=>['nombre']]];
    $entidad = $_REQUEST['entidad'] ?? '';
    if (!isset($mapa[$entidad])) { echo json_encode(['ok'=>false, 'mensaje'=>'Entidad no válida']); exit; }
    $tabla = $mapa[$entidad]['tabla']; $campos = $mapa[$entidad]['campos'];

    if ($accion === 'crud_listar') { echo json_encode($pdo->query("SELECT * FROM $tabla ORDER BY id DESC")->fetchAll()); exit; }
    if ($accion === 'crud_eliminar') { $pdo->prepare("DELETE FROM $tabla WHERE id = ?")->execute([(int)$_POST['id']]); echo json_encode(['ok'=>true]); exit; }

    $valores = []; foreach ($campos as $c) { $valores[$c] = trim($_POST[$c] ?? ''); }
    if ($accion === 'crud_crear') {
        $pdo->prepare("INSERT INTO $tabla (".implode(',', $campos).") VALUES (".implode(',', array_fill(0, count($campos), '?')).")")->execute(array_values($valores));
    } else {
        $sets = implode(',', array_map(fn($c) => "$c = ?", $campos));
        $params = array_values($valores); $params[] = (int)($_POST['id'] ?? 0);
        $pdo->prepare("UPDATE $tabla SET $sets WHERE id = ?")->execute($params);
    }
    echo json_encode(['ok'=>true]); exit;
}

http_response_code(404);
echo json_encode(['error' => 'Acción no encontrada']);