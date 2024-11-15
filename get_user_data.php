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
        // Mostrar cada registro completo en el modal
        while ($row = $result_all->fetch_assoc()) {
            echo "<div class='modal-grid'>";
            echo "<h3>Envío del formulario: " . htmlspecialchars($row['Fecha_hora']) . "</h3>";

            // Listado de campos de cada registro
            $fields = [
                'Apellido y Nombre' => $row['apellido_nombre'],
                'DNI' => $row['DNI'],
                'Ciudad de Residencia' => $row['ciudad'],
                'Situación Laboral' => $row['situacion_laboral'],
                'Empresa' => $row['empresa'],
                'Localidad Empresa' => $row['localidadempresa'],
                'Cargo que ocupa' => $row['cargo'],
                'Área' => $row['area'],
                'Mail laboral' => $row['mail'],
                'Vínculo con la FIO' => $row['vinculacion'],
                'Actividad' => $row['Actividad'],
                'Es Docente' => $row['Docente'],
                'Cargo de Docente' => $row['cargo_docente'],
                'Departamento de Docente' => $row['Departamento_docente'],
                'Si es BECARIO/A de Posgrado' => $row['becario'],
                'Si es NO-DOCENTE Indique a qué Agrupamiento Pertenece y qué Actividad Desarrolla' => $row['no_docente'],
                'Si está DESOCUPADO/A o es JUBILADO/A' => $row['desocupado'],
                'Temática le interesaría CAPACITARSE' => $row['capacitarse'],
                'Acompañar luego de su graduación' => $row['acompanar']
            ];

            // Mostrar los campos del formulario
            foreach ($fields as $label => $value) {
                echo "<p><strong>{$label}:</strong> " . (!empty($value) ? htmlspecialchars($value) : 'Sin información') . "</p>";
            }

            echo "</div><hr>"; // Separador entre registros
        }
    } else {
        echo "<p>No se encontraron registros con el DNI o nombre proporcionado.</p>";
    }
} else {
    echo "<p>No se encontró el usuario con el ID proporcionado.</p>";
}

$stmt->close();
$conn->close();
?>
