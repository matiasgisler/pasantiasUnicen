<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "root";
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
$record = $result->fetch_assoc();

if ($record) {
    $apellido_nombre = $record['apellido_nombre'];
    $dni = $record['dni'];

    // Si el DNI está disponible, usarlo como criterio de búsqueda principal
    if (!empty($dni)) {
        $sql_all_records = "SELECT * FROM formulario_etapas WHERE dni = ? ORDER BY id DESC";
        $stmt_all = $conn->prepare($sql_all_records);
        $stmt_all->bind_param("s", $dni);
    } else {
        // Si no hay DNI, usar apellido_nombre como criterio de búsqueda
        $sql_all_records = "SELECT * FROM formulario_etapas WHERE apellido_nombre = ? ORDER BY id DESC";
        $stmt_all = $conn->prepare($sql_all_records);
        $stmt_all->bind_param("s", $apellido_nombre);
    }

    $stmt_all->execute();
    $result_all = $stmt_all->get_result();

    if ($result_all->num_rows > 0) {
        echo '<div id="carouselExample" class="carousel slide position-relative">';


        // Generar los indicadores del carrusel
        echo '<div class="carousel-indicators" style="bottom: -2.5rem !important;">';
        for ($i = 0; $i < $result_all->num_rows; $i++) {
            $activeClass = $i === 0 ? 'active' : '';
            echo "<button type='button' data-bs-target='#carouselExampleCaptions' data-bs-slide-to='$i' class='$activeClass bg-primary ' aria-current='true' aria-label='Slide " . ($i + 1) . "'></button>";
        }
        echo '</div>';



        echo '<div class="carousel-inner">';

        $isActive = true; // Indica si la primera iteración es la activa

        while ($row = $result_all->fetch_assoc()) {
            $activeClass = $isActive ? 'active' : '';
            $isActive = false; // Después del primer registro, ya no es activo

            echo "<div class='carousel-item $activeClass'>";

            // Fecha de envío
            $fecha_hora = isset($row['Fecha_hora']) ? htmlspecialchars($row['Fecha_hora']) : 'No disponible';
            echo '<div class="container-fluid mb-4">';
            echo '<h3 class="mb-3">Envío del formulario: ' . $fecha_hora . '</h3>';

            // Información Personal
            echo '<div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">Información Personal</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="fw-bold text-primary">Apellido y Nombre:</label>
                        <p class="mb-2">' . htmlspecialchars($row['apellido_nombre']) . '</p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-primary">DNI:</label>
                        <p class="mb-2">' . htmlspecialchars($row['DNI']) . '</p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-primary">Ciudad de Residencia:</label>
                        <p class="mb-2">' . htmlspecialchars($row['ciudad'] ?? 'No disponible') . '</p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-primary">Carrera:</label>
                        <p class="mb-2">' . htmlspecialchars($row['carrera'] ?? 'No disponible') . '</p>
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
                        <p class="mb-2">' . htmlspecialchars($row['empresa'] ?? 'No disponible') . '</p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-primary">Situación Laboral:</label>
                        <p class="mb-2">' . htmlspecialchars($row['situacion_laboral'] ?? 'No disponible') . '</p>
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
                        <p class="mb-2">' . htmlspecialchars($row['vinculacion'] ?? 'No disponible') . '</p>
                    </div>
                    <div class="col-12">
                        <label class="fw-bold text-primary">Temática le interesaría CAPACITARSE:</label>
                        <p class="mb-2">' . htmlspecialchars($row['capacitarse'] ?? 'No disponible') . '</p>
                    </div>
                    <div class="col-12">
                        <label class="fw-bold text-primary">Acompañar luego de su graduación:</label>
                        <p class="mb-2">' . htmlspecialchars($row['acompanar'] ?? 'No disponible') . '</p>
                    </div>
                </div>
            </div>
        </div>';

            echo '</div>'; // Cierra la tarjeta actual
            echo '</div>'; // Cierra el item del carrusel
        }

        echo '</div>'; // Cierra carousel-inner

        // Botones de control del carrusel con áreas extendidas y fondo gris
        echo '<button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev" style="top: -5.8rem;height: 6%;width: 5%;background-color: rgb(147 130 210 / 80%);; left:18rem;">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next" style="top: -5.8rem;height: 6%;width: 5%;background-color: rgb(147 130 210 / 80%); left:23rem;">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>';

        echo '</div>'; // Cierra el carrusel
    } else {
        echo "<p>No se encontraron registros con el DNI o nombre proporcionado.</p>";
    }
} else {
    echo "<p>No se encontró el usuario con el ID proporcionado.</p>";
}

$stmt->close();
$conn->close();
?>