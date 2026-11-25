<?php
// 1. Generamos el hash real
$hash_real = password_hash('12345678', PASSWORD_DEFAULT);
echo "<h3>El hash generado es:</h3>";
echo "<p>" . $hash_real . "</p>";

// 2. Lo guardamos en la base de datos automáticamente
$host = 'localhost';
$db   = 'salud_integral';
$user = 'root';
$pass = ''; // Pon tu contraseña de MySQL si tienes una

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    
    // Actualizamos el usuario administrador
    $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE email = 'wmollericona@gmail.com'");
    $stmt->execute([$hash_real]);
    
    echo "<h3>✅ Base de datos actualizada correctamente.</h3>";
    
    // 3. Verificamos que funcione
    $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE email = 'wmollericona@gmail.com'");
    $stmt->execute();
    $pass_bd = $stmt->fetchColumn();
    
    echo "<p>Prueba de verificación: ";
    if (password_verify('12345678', $pass_bd)) {
        echo "<strong style='color:green'>¡ÉXITO! bool(true)</strong></p>";
        echo "<p>Ya puedes ir a <a href='login.php'>login.php</a> y entrar con:<br>
              <b>Correo:</b> wmollericona@gmail.com<br>
              <b>Contraseña:</b> 12345678</p>";
    } else {
        echo "<strong style='color:red'>Falló (bool false)</strong></p>";
    }

} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>