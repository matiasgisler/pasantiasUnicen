<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "practicas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID del usuario desde el parámetro de la URL
$user_id = intval($_GET['id']);

// Obtener los datos específicos del usuario por su ID
$sql = "SELECT apellido_nombre, dni FROM formulario_etapas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    // Log para depuración
    error_log("Datos encontrados para el usuario ID: " . $user_id);
    
    echo '<div class="container-fluid mb-4">';
    echo '<h3 class="mb-3">Envío del formulario: ' . htmlspecialchars($user['Fecha_hora']) . '</h3>';

    // Información Personal
    echo '<div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">Información Personal</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="fw-bold text-primary">Apellido y Nombre:</label>
                        <p class="mb-2">' . htmlspecialchars($user['apellido_nombre']) . '</p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-primary">DNI:</label>
                        <p class="mb-2">' . htmlspecialchars($user['DNI']) . '</p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-primary">Ciudad de Residencia:</label>
                        <p class="mb-2">' . htmlspecialchars($user['ciudad']) . '</p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-primary">Carrera:</label>
                        <p class="mb-2">' . htmlspecialchars($user['carrera']) . '</p>
                    </div>
                </div>
            </div>
        </div>';

    // Información Laboral
    echo '<div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">Información Laboral</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="fw-bold text-primary">Empresa:</label>
                        <p class="mb-2">' . htmlspecialchars($user['empresa']) . '</p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-primary">Situación Laboral:</label>
                        <p class="mb-2">' . htmlspecialchars($user['situacion_laboral']) . '</p>
                    </div>
                </div>
            </div>
        </div>';

    // Información Adicional
    echo '<div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">Información Adicional</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="fw-bold text-primary">Vinculación con la Universidad:</label>
                        <p class="mb-2">' . htmlspecialchars($user['vinculacion']) . '</p>
                    </div>
                    <div class="col-12">
                        <label class="fw-bold text-primary">Temática le interesaría CAPACITARSE:</label>
                        <p class="mb-2">' . htmlspecialchars($user['capacitarse']) . '</p>
                    </div>
                    <div class="col-12">
                        <label class="fw-bold text-primary">Acompañar luego de su graduación:</label>
                        <p class="mb-2">' . htmlspecialchars($user['acompanar']) . '</p>
                    </div>
                </div>
            </div>
        </div>';

    echo '</div>';
} else {
    // Log para depuración
    error_log("No se encontraron datos para el usuario ID: " . $user_id);
    echo "<p class='alert alert-danger'>No se encontró el usuario con el ID proporcionado.</p>";
}

$stmt->close();
$conn->close();
?>
