<?php
session_start();

// Verificar si el usuario ya está autenticado
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: administracion.php'); // Redirigir al admin.php si ya está autenticado
    exit;
}
    
// Procesar el formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verificar credenciales (en este caso, 'admin' y 'admin')
    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['logged_in'] = true;
        header('Location: administracion.php'); // Redirigir al admin.php si las credenciales son correctas
        exit;
    } else {
        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de sesión</title>
</head>
<body>
    <h2>Inicio de sesión</h2>
    <form method="post" action="auth.php">
        <label for="username">Usuario:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <input type="submit" value="Iniciar sesión">
    </form>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
</body>
</html>
