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

// Función para ordenar palabras en el nombre
function ordenar_nombre($nombre) {
    // Convertir a minúsculas, eliminar comas, puntos y otros caracteres de puntuación
    $nombre = strtolower($nombre);
    $nombre = preg_replace('/[,.]/', '', $nombre); // Eliminar comas y puntos
    $palabras = explode(' ', $nombre);
    sort($palabras); // Ordenar las palabras alfabéticamente

    // Capitalizar cada palabra
    $nombre_capitalizado = array_map('ucwords', $palabras);
    return implode(' ', $nombre_capitalizado);
}

// Función para validar y convertir fechas con hora
function formatear_fecha_hora($fecha_str) {
    $fecha = DateTime::createFromFormat('d/m/y H:i:s', $fecha_str);
    return $fecha ? $fecha->format('Y-m-d H:i:s') : null;
}

// Función para validar y convertir fechas sin hora
function formatear_fecha($fecha_str) {
    $fecha = DateTime::createFromFormat('d/m/y', $fecha_str);
    return $fecha ? $fecha->format('Y-m-d') : null;
}

// Ruta del archivo CSV
$csvFilePath = "C:\\Users\\matia\\Desktop\\Encuesta graduados (Respuestas) - Respuestas de formulario 1.csv";

// Intentar abrir el archivo CSV
if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    // Saltar la primera fila de encabezado
    fgetcsv($handle);

    // Recorrer el archivo CSV y leer los datos
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // Asignar Fecha_hora como texto desde el CSV y convertirla
        $Fecha_hora = !empty($data[0]) ? formatear_fecha_hora($data[0]) : null;
        $carrera = $data[1] ?? '';
        $apellido_nombre = ordenar_nombre($data[2] ?? '');

        // Convertir Fecha Egreso a formato compatible
        $fecha_egreso = !empty($data[3]) ? formatear_fecha($data[3]) : null;

        // Resto de los campos
        $telefono = $data[4] ?? '';
        $correo = $data[5] ?? '';
        $ciudad = $data[6] ?? '';
        $situacion_laboral = $data[7] ?? '';
        $empresa = $data[8] ?? '';
        $localidadempresa = $data[9] ?? '';
        $cargo = $data[10] ?? '';
        $area = $data[11] ?? '';
        $mail = $data[12] ?? '';
        $relaciontrabajo = $data[13] ?? '';
        $vinculacion = $data[14] ?? '';
        $actividad = $data[15] ?? '';
        $docente = $data[16] ?? '';
        $cargo_docente = $data[17] ?? '';
        $departamento_docente = $data[18] ?? '';
        $becario = $data[19] ?? '';
        $no_docente = $data[20] ?? '';
        $desocupado = $data[21] ?? '';
        $capacitarse = $data[22] ?? '';
        $acompanar = $data[23] ?? '';

        // *** NUEVA SECCIÓN: Verificar existencia y actualizar estado ***

        // Verificar si ya existe un registro con el mismo apellido_nombre y estado 'activo'
        $check_sql = "SELECT id FROM formulario_etapas WHERE apellido_nombre = ? AND estado = 'activo'";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $apellido_nombre);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // Si existe, actualizar su estado a 'inactivo'
            $update_sql = "UPDATE formulario_etapas SET estado = 'inactivo' WHERE apellido_nombre = ? AND estado = 'activo'";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("s", $apellido_nombre);
            if (!$update_stmt->execute()) {
                echo "Error al actualizar el estado del registro: " . $update_stmt->error . "<br>";
            }
            $update_stmt->close();
        }
        $check_stmt->close();

        // *** FIN DE LA NUEVA SECCIÓN ***

        // Inserción en la base de datos con estado 'activo'
        $sql = "INSERT INTO formulario_etapas (
                    Fecha_hora, carrera, apellido_nombre, Fecha_egreso, telefono, correo, ciudad, 
                    situacion_laboral, empresa, localidadempresa, cargo, area, mail, relaciontrabajo, 
                    vinculacion, Actividad, Docente, cargo_docente, Departamento_docente, becario, 
                    no_docente, desocupado, capacitarse, acompanar, estado
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'activo')";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssssssssssssssssssss",
            $Fecha_hora, $carrera, $apellido_nombre, $fecha_egreso, $telefono, $correo, $ciudad,
            $situacion_laboral, $empresa, $localidadempresa, $cargo, $area, $mail, $relaciontrabajo,
            $vinculacion, $actividad, $docente, $cargo_docente, $departamento_docente, $becario,
            $no_docente, $desocupado, $capacitarse, $acompanar
        );

        // Ejecutar la consulta e imprimir error si ocurre
        if (!$stmt->execute()) {
            echo "Error al insertar registro: " . $stmt->error . "<br>";
        }

        $stmt->close();
    }

    fclose($handle);
    echo "Importación completada correctamente.";
} else {
    echo "Error al abrir el archivo CSV.";
}

// Cerrar conexión
$conn->close();
?>
