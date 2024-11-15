<?php
// Datos de conexión
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "practicas";
$i = 1;

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Configuración de paginación
$limit = 50; // Número de registros por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Inicializar variables para ordenar
$order_by = isset($_GET['order_by']) ? $conn->real_escape_string($_GET['order_by']) : 'id';
$order_dir = isset($_GET['order_dir']) ? $conn->real_escape_string($_GET['order_dir']) : 'ASC';

// Preparar la consulta de filtro
$sql = "SELECT * FROM formulario_etapas";
$count_sql = "SELECT COUNT(*) AS total FROM formulario_etapas";
$conditions = ["estado = 'activo'"]; // Agregar la condición de estado activo

// Validar y procesar los filtros
if (isset($_GET['filters']) && !empty($_GET['filters'])) {
    $filters = json_decode($_GET['filters'], true);

    if (is_array($filters)) {
        foreach ($filters as $filter) {
            if (isset($filter['column']) && isset($filter['value'])) {
                $filter_column = $conn->real_escape_string($filter['column']);
                $filter_value = $conn->real_escape_string($filter['value']);

                if ($filter_column === 'Fecha_egreso' && isset($filter['year_only']) && $filter['year_only']) {
                    $conditions[] = "YEAR(Fecha_egreso) = '$filter_value'";
                } else {
                    $conditions[] = "$filter_column LIKE '%$filter_value%'";
                }
            }
        }
    } else {
        die("Error: Los filtros no tienen un formato válido.");
    }
}

// Aplicar todas las condiciones en la consulta SQL
if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
    $count_sql .= " WHERE " . implode(" AND ", $conditions);
}

// Aplicar orden y paginación
$sql_with_pagination = $sql . " ORDER BY $order_by $order_dir LIMIT $limit OFFSET $offset";

// Ejecutar la consulta
$result = $conn->query($sql_with_pagination);

// Obtener el total de registros para calcular el número de páginas
$count_result = $conn->query($count_sql);
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

if ($result === false) {
    die("Error en la consulta: " . $conn->error);
}

/**
 * Exporta los datos filtrados a un archivo CSV con las columnas deseadas.
 *
 * @param mysqli $conn Conexión a la base de datos.
 * @param string $sql Consulta SQL con filtros aplicados.
 * @param array $columns Columnas deseadas para exportar.
 */
function exportToCSV($conn, $sql, $columns)
{
    // Configurar encabezados para la exportación a CSV
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="Inscriptos_Unicen-' . date('d-m-y') . '.csv"');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');

    // Crear el archivo CSV
    $output = fopen('php://output', 'w');

    // Validar y preparar columnas
    if (empty($columns)) {
        die("Error: No se especificaron columnas para exportar.");
    }

    // Escribir encabezados personalizados en el archivo CSV
    fputcsv($output, $columns);

    // Construir la consulta SQL con columnas específicas
    $columns_sql = implode(", ", array_map(fn($col) => $conn->real_escape_string($col), $columns));
    $filtered_sql = preg_replace('/^SELECT \*/', "SELECT $columns_sql", $sql);

    // Ejecutar la consulta
    $result = $conn->query($filtered_sql);
    if ($result === false) {
        die("Error en la consulta para exportar: " . $conn->error);
    }

    // Escribir los datos en el archivo CSV
    while ($row = $result->fetch_assoc()) {
        $filtered_row = array_intersect_key($row, array_flip($columns));
        fputcsv($output, $filtered_row);
    }

    fclose($output);
    exit();
}

// Detectar si se solicita la exportación
if (isset($_GET['export']) && $_GET['export'] == 1) {
    $columns = isset($_GET['columns']) ? $_GET['columns'] : [];
    exportToCSV($conn, $sql, $columns);
}
?>




<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interfaz de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="administracion.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="administracion.js" defer></script>
</head>

<body class="container-fluid py-4">
    <h1>Interfaz de Administración</h1>

    <div class="accordion" id="accordionExample">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                    aria-expanded="true" aria-controls="collapseOne">
                    <p class="fs-3">Filtros</p>
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show">
                <div class="accordion-body">
                    <!-- Botones de aplicar y quitar filtros fuera del modal  -->
                    <!-- <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#filterModal">Filtrar</button> -->
                    <div class="modal-footer gap-3 col-gap-3">
                        <button type="button" class="btn btn-secondary" onclick="addFilter()">Agregar filtro</button>
                        <button type="button" class="btn btn-primary" onclick="applyFilters()">Aplicar filtros</button>
                        <button type="button" class="btn btn-secondary" onclick="clearFilters()">Quitar Filtros</button>
                    </div>
                    <!-- Modal de Filtros -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="filterModalLabel">Aplicar Filtros</h5>
                    </div>
                    <div class="modal-body">
                        <div id="filterContainer">
                            <div class="filter-group mb-3">
                                <div class="row g-2">
                                    <div class="col">
                                        <label>Filtrar por:</label>
                                        <select class="form-select filter-column"
                                            title="Seleccione columna para filtrar" onchange="updateFilterInput(this)">
                                            <option value="">Seleccione</option>
                                            <option value="carrera">Carrera</option>
                                            <option value="DNI">DNI</option>
                                            <option value="apellido_nombre">Apellido y Nombre</option>
                                            <option value="ciudad">Ciudad</option>
                                            <option value="situacion_laboral">Situación Laboral</option>
                                            <option value="Fecha_egreso">Año de Egreso</option>
                                            <option value="correo">Correo</option>
                                            <option value="empresa">Nombre de la Empresa</option>
                                        </select>
                                    </div>

                                    <div class="col filter-value">
                                        <input type="text" class="form-control mt-rem"
                                            placeholder="Ingrese aqui el valor">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
    <div>

        <form method="get" action="" id="filterForm" class="mb-4">
            <div class="row g-4 mt-rem">
                <div class="col-md-4">
                    <label for="order_by" class="form-label ">Ordenar por:</label>
                    <select name="order_by" id="order_by" class="form-select">
                        <option value="id" <?php echo ($order_by === 'id') ? 'selected' : ''; ?>>ID</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="order_dir" class="form-label">Dirección:</label>
                    <select name="order_dir" id="order_dir" class="form-select">
                        <option value="ASC" <?php echo ($order_dir === 'ASC') ? 'selected' : ''; ?>>Menor a
                            Mayor</option>
                        <option value="DESC" <?php echo ($order_dir === 'DESC') ? 'selected' : ''; ?>>Mayor
                            a menor</option>
                    </select>

                </div>
                <div class="col-md-4 ">
                    <button id="ordenarPor" type="button" class="btn btn-secondary mt-n1"
                        onclick="orderBy(event)">Ordenar</button>
                </div>
            </div>
            <div class="mt-rem">

                <form method="get" action="">
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header ">

                                <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                    <p class="fs-3">Seleccionar columnas para exportar y/o mostrar</p>
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <input type="hidden" name="filters"
                                        value='<?php echo isset($_GET['filters']) ? htmlspecialchars($_GET['filters']) : ""; ?>'>
                                    <input type="hidden" name="export" value="1">

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input column-toggle" type="checkbox"
                                                name="columns[]" value="id" id="column-id" checked data-column="id-col">
                                            <label class="form-check-label" for="column-id">ID</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input column-toggle" type="checkbox"
                                                name="columns[]" value="Fecha_hora" id="column-Fecha_hora" checked data-column="Fecha_hora-col">
                                            <label class="form-check-label" for="column-Fecha_hora">Fecha y hora Realizado</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input column-toggle" type="checkbox"
                                                name="columns[]" value="id" id="column-id" checked data-column="id-col">
                                            <label class="form-check-label" for="column-id">ID</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input column-toggle" type="checkbox"
                                                name="columns[]" value="apellido_nombre" id="column-apellido_nombre"
                                                checked data-column="apellido_nombre-col">
                                            <label class="form-check-label" for="column-apellido_nombre">Apellido y
                                                Nombre</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input column-toggle" type="checkbox"
                                                name="columns[]" value="carrera" id="column-carrera" checked
                                                data-column="carrera-col">
                                            <label class="form-check-label" for="column-carrera">Carrera</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input column-toggle" type="checkbox"
                                                name="columns[]" value="DNI" id="column-DNI" checked
                                                data-column="dni-col">
                                            <label class="form-check-label" for="column-DNI">DNI</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input column-toggle" type="checkbox"
                                                name="columns[]" value="Fecha_egreso" id="column-Fecha_egreso" checked
                                                data-column="fecha_egreso-col">
                                            <label class="form-check-label" for="column-Fecha_egreso">Fecha de
                                                Egreso</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input column-toggle" type="checkbox"
                                                name="columns[]" value="telefono" id="column-telefono" checked
                                                data-column="telefono-col">
                                            <label class="form-check-label" for="column-telefono">Teléfono</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input column-toggle" type="checkbox"
                                                name="columns[]" value="correo" id="column-correo" checked
                                                data-column="correo-col">
                                            <label class="form-check-label" for="column-correo">Correo</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input column-toggle" type="checkbox"
                                                name="columns[]" value="ciudad" id="column-ciudad" checked
                                                data-column="ciudad-col">
                                            <label class="form-check-label" for="column-ciudad">Ciudad de
                                                Residencia</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input column-toggle" type="checkbox"
                                                name="columns[]" value="empresa" id="column-empresa" checked
                                                data-column="empresa-col">
                                            <label class="form-check-label" for="column-empresa">Nombre de la
                                                Empresa</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input column-toggle" type="checkbox"
                                                name="columns[]" value="capacitarse" id="column-capacitarse" checked
                                                data-column="capacitarse-col">
                                            <label class="form-check-label" for="column-capacitarse">Sobre qué temática le interesaría CAPACITARSE</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input column-toggle" type="checkbox"
                                                name="columns[]" value="vinculacion" id="column-vinculacion" checked
                                                data-column="vinculacion-col">
                                            <label class="form-check-label" for="column-vinculacion">Vinculación con la
                                                Universidad</label>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success">Exportar a CSV</button>
                </form>

            </div>
        </div>
    </div>
    </div>

    </div>
    <p class="h1">Cantidad de Registros: <?php echo $total_rows?></p>

    <table class="table table-striped table-bordered w-100 table-hover">
        <thead class="table-primary">
            <tr>
                <th>Numero de la Fila</th>
                <th>Acciones</th>
                <th>Fecha y hora realizada</th>
                <th>ID</th>
                <th>Apellido y Nombre</th>
                <th>Carrera</th>
                <th>DNI</th>
                <th>Fecha de Egreso</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Ciudad de Residencia</th>
                <th>Nombre de la Empresa</th>
                <th>Sobre qué temática le interesaría CAPACITARSE.</th>
                <th>De qué manera la FIO lo/la puede acompañar luego de su graduación.</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($i++); ?></td>
                <td>
                    <button class="btn btn-primary btn-sm btn-space"
                        onclick="showModal(<?php echo htmlspecialchars($row['id']); ?>)">Ver</button>
                </td>
                <td class=""><?php echo htmlspecialchars($row['Fecha_hora']); ?></td>
                <td class=""><?php echo htmlspecialchars($row['id']); ?></td>
                <td class=""><?php echo htmlspecialchars($row['apellido_nombre']); ?></td>
                <td class=""><?php echo htmlspecialchars($row['carrera']); ?></td>
                <td class=""><?php echo htmlspecialchars($row['DNI']); ?></td>
                <td class=""><?php echo htmlspecialchars($row['Fecha_egreso']); ?></td>
                <td class=""><?php echo htmlspecialchars($row['telefono']); ?></td>
                <td class=""><?php echo htmlspecialchars($row['correo']); ?></td>
                <td class=""><?php echo htmlspecialchars($row['ciudad']); ?></td>
                <td class=""><?php echo htmlspecialchars($row['empresa']); ?></td>
                <td class=""><?php echo htmlspecialchars($row['capacitarse']); ?></td>
                <td class=""><?php echo htmlspecialchars($row['vinculacion']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>


    <!-- Paginación -->
    <div class="pagination mt-4 d-flex align-items-center justify-content-center">
        <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>&filters=<?php echo isset($_GET['filters']) ? htmlspecialchars($_GET['filters']) : ""; ?>"
            class="btn btn-secondary me-2">Anterior</a>
        <?php endif; ?>

        <form method="get" class="d-flex align-items-center">
            <input type="hidden" name="filters"
                value='<?php echo isset($_GET['filters']) ? htmlspecialchars($_GET['filters']) : ""; ?>'>
            <label class="me-2" for="page-number">Página</label>
            <input type="number" name="page" id="page-number" title="Número de página" placeholder="Página"
                value="<?php echo $page; ?>" min="1" max="<?php echo $total_pages; ?>" class="form-control me-2"
                style="width: 60px;">
            <input type="submit" value="Ir" class="btn btn-primary me-2">
            <span class="ms-2">de <?php echo $total_pages; ?></span>
        </form>

        <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>&filters=<?php echo isset($_GET['filters']) ? htmlspecialchars($_GET['filters']) : ""; ?>"
            class="btn btn-secondary ms-2">Siguiente</a>
        <?php endif; ?>
    </div>

    <!-- Modal para mostrar los datos de usuario -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel-unique" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel-unique">Detalles del Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Aquí se cargará el contenido dinámico del usuario -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>