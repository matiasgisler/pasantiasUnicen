<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "practicas";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Guardar el color
if (isset($_POST['id']) && isset($_POST['color'])) {
    $id = intval($_POST['id']);
    $color = $conn->real_escape_string($_POST['color']);
    $sql = "UPDATE formulario_etapas SET color = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $color, $id);
    $stmt->execute();
    $stmt->close();
}

// Cerrar conexión
$conn->close();
?>
