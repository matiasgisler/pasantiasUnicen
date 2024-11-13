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
$sql = "SELECT apellido_nombre FROM formulario_etapas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if ($record) {
    $apellido_nombre = $record['apellido_nombre'];

    // Obtener todos los registros con el mismo apellido_nombre
    $sql_all_records = "SELECT * FROM formulario_etapas WHERE apellido_nombre = ? ORDER BY Fecha_egreso DESC";
    $stmt_all = $conn->prepare($sql_all_records);
    $stmt_all->bind_param("s", $apellido_nombre);
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
                'Ciudad de Residencia' => $row['ciudad'],
                'Situación Laboral' => $row['situacion_laboral'],
                'Empresa' => $row['empresa'],
                'Localidad Empresa' => $row['localidadempresa'],
                'Cargo' => $row['cargo'],
                'Área' => $row['area'],
                'Mail' => $row['mail'],
                'Vinculo con la FIO' => $row['vinculacion'],
                'Actividad' => $row['Actividad'],
                'Docente' => $row['Docente'],
                'Cargo Docente' => $row['cargo_docente'],
                'Departamento Docente' => $row['Departamento_docente'],
                'Becario' => $row['becario'],
                'No Docente' => $row['no_docente'],
                'Desocupado' => $row['desocupado'],
                'Capacitarse' => $row['capacitarse'],
                'Acompañar' => $row['acompanar']
            ];

            // Mostrar los campos del formulario
            foreach ($fields as $label => $value) {
                echo "<p><strong>{$label}:</strong> " . (!empty($value) ? htmlspecialchars($value) : 'Sin información') . "</p>";
            }

            echo "</div><hr>"; // Separador entre registros
        }
    } else {
        echo "<p>No se encontraron registros con el mismo nombre.</p>";
    }
} else {
    echo "<p>No se encontró el usuario con el ID proporcionado.</p>";
}

$stmt->close();
$conn->close();
?>
