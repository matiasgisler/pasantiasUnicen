<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "practicas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recuperar los filtros de la solicitud
$filters = isset($_GET['filters']) ? json_decode($_GET['filters'], true) : [];
$conditions = ["estado = 'activo'"]; // Incluir solo datos activos

// Aplicar condiciones según los filtros
foreach ($filters as $filter) {
    $filter_column = $conn->real_escape_string($filter['column']);
    $filter_value = $conn->real_escape_string($filter['value']);
    
    if ($filter_column === 'Fecha_egreso' && isset($filter['year_only']) && $filter['year_only']) {
        $conditions[] = "YEAR(Fecha_egreso) = '$filter_value'";
    } else {
        $conditions[] = "$filter_column LIKE '%$filter_value%'";
    }
}

// Construir la consulta SQL con filtros
$sql = "SELECT * FROM formulario_etapas";
if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Ejecutar la consulta
$result = $conn->query($sql);
if ($result === false) {
    die("Error en la consulta: " . $conn->error);
}

// Configurar encabezados para la exportación a CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="Inscriptos Unicen' . date('Ymd_His') . '.csv"');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

// Crear el archivo CSV
$output = fopen('php://output', 'w');

// Escribir encabezados en el archivo CSV
fputcsv($output, ['ID', 'Carrera', 'Apellido y Nombre', 'DNI', 'Fecha de Egreso', 'Teléfono', 'Correo', 'Ciudad', 'Empresa', 'Vinculación', 'Capacitarse', 'Acompañar']);

// Escribir los datos en el archivo CSV
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
