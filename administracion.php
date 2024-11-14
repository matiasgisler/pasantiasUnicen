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
 * Exporta los datos filtrados a un archivo CSV y los descarga.
 *
 * @param mysqli $conn Conexión a la base de datos.
 * @param string $sql Consulta SQL con filtros aplicados.
 */
function exportToCSV($conn, $sql)
{
    // Configurar encabezados para la exportación a CSV
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="Inscriptos_Unicen-' . date('d-m-y') . '.csv"');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');

    // Crear el archivo CSV
    $output = fopen('php://output', 'w');

    // Escribir encabezados en el archivo CSV
    fputcsv($output, ['ID', 'Carrera', 'Apellido y Nombre', 'DNI', 'Fecha de Egreso', 'Teléfono', 'Correo', 'Ciudad', 'Empresa', 'Vinculación', 'Capacitarse', 'Acompañar']);

    // Ejecutar la consulta
    $result = $conn->query($sql);
    if ($result === false) {
        die("Error en la consulta para exportar: " . $conn->error);
    }

    // Escribir los datos en el archivo CSV
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit();
}

// Detectar si se solicita la exportación
if (isset($_GET['export']) && $_GET['export'] == 1) {
    exportToCSV($conn, $sql);
}
?>



<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interfaz de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .mt-rem {
        margin-top: 1.5rem !important;
    }

    #ordenarPor {
        margin-top: 2rem !important
    }

    .modal-content {
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        font-size: 16px;
        color: #333;
    }

    .modal-grid p {
        margin-bottom: 10px;
        padding: 5px 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .modal-title {
        font-size: 1.8rem;
        font-weight: bold;
        color: #007BFF;
        margin-bottom: 20px;
    }

    .modal-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .modal-grid p strong {
        color: #007BFF;
    }

    body {
        font-family: 'Poppins', sans-serif;
    }

    h1 {
        color: #007BFF;
        text-align: center;
        margin: 20px 0;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .btn-primary {
        background-color: #007BFF;
    }

    .btn-lime {
        background-color: #32CD32;
        color: white;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 123, 255, 0.1);
    }

    .modal-dialog {
        max-width: 80%;
    }

    .table .correo-col {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .filter-options {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .filter-column {
        flex: 1 1 calc(33.33% - 10px);
    }

    .btn-space {
        margin-right: 10px;
    }

    .table-hover tbody tr:hover td {
        background: lightgreen;
    }
    </style>
</head>

<body class="container-fluid py-4">
    <h1>Interfaz de Administración</h1>

    <div class="accordion" id="accordionExample">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                    aria-expanded="true" aria-controls="collapseOne">
                    Filtros
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
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
        <p>Cantidad de Registros: <?php echo $total_rows?></p>
        <form method="get" action="" id="filterForm" class="mb-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <label for="order_by" class="form-label">Ordenar por:</label>
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
                        onclick="orderBy()">Ordenar</button>
                </div>
            </div>
            <div>
                <form method="get" action="">
                    <input type="hidden" name="filters"
                        value='<?php echo isset($_GET['filters']) ? htmlspecialchars($_GET['filters']) : ""; ?>'>
                    <input type="hidden" name="export" value="1">
                    <button type="submit" class="btn btn-success">Exportar a CSV</button>
                </form>

            </div>
        </form>
    </div>
    <table class="table table-striped table-bordered w-100 table-hover">
        <thead class="table-primary">
            <tr>
                <th>Numero de la Fila</th>
                <th>Acciones</th>
                <th>Carrera</th>
                <th>Apellido y Nombre</th>
                <th>DNI</th>
                <th>Fecha de Egreso(YY/MM/DD)</th>
                <th>Teléfono</th>
                <th class="correo-col">Correo</th>
                <th>Ciudad de Residencia</th>
                <th>Nombre de la Empresa / Organización:</th>
                <th>Vinculación con la Universidad</th>
                <th>Qué Temática le Interesaría CAPACITARSE</th>
                <th>DE qué Manera la FIO lo/la Puede Acompañar Luego de su Graduación</th>
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
                <td><?php echo !empty($row['carrera']) ? htmlspecialchars($row['carrera']) : '-'; ?></td>
                <td><?php echo !empty($row['apellido_nombre']) ? htmlspecialchars(ucwords(strtolower($row['apellido_nombre']))) : '-'; ?>
                </td>
                <td><?php echo !empty($row['DNI']) ? htmlspecialchars($row['DNI']) : '-'; ?></td>
                <td><?php echo !empty($row['Fecha_egreso']) ? htmlspecialchars($row['Fecha_egreso']) : '-'; ?></td>
                <td><?php echo !empty($row['telefono']) ? htmlspecialchars($row['telefono']) : '-'; ?></td>
                <td class="correo-col"><?php echo !empty($row['correo']) ? htmlspecialchars($row['correo']) : '-'; ?>
                </td>
                <td><?php echo !empty($row['ciudad']) ? htmlspecialchars($row['ciudad']) : '-'; ?></td>
                <td><?php echo !empty($row['empresa']) ? htmlspecialchars($row['empresa']) : '-'; ?></td>
                <td><?php echo !empty($row['vinculacion']) ? htmlspecialchars($row['vinculacion']) : '-'; ?></td>
                <td><?php echo !empty($row['capacitarse']) ? htmlspecialchars($row['capacitarse']) : '-'; ?></td>
                <td><?php echo !empty($row['acompanar']) ? htmlspecialchars($row['acompanar']) : '-'; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
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



    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function updateFilterInput(selectElement) {
        const filterValueContainer = selectElement.closest('.filter-group').querySelector('.filter-value');
        filterValueContainer.innerHTML = '';

        if (selectElement.value === 'carrera') {
            filterValueContainer.innerHTML = `
                <select class="form-control mt-rem" name="filter_value" title="Seleccione el valor a filtrar">
                    <option value="Ingeniería en Agrimensura">Ingeniería en Agrimensura</option>
                    <option value="Ingeniería en Construcciones">Ingeniería en Construcciones (no vigente)</option>
                    <option value="Ingeniería civil">Ingeniería civil</option>
                    <option value="Ingeniería Electromecánica">Ingeniería Electromecánica</option>
                    <option value="Ingeniería Industrial">Ingeniería Industrial</option>
                    <option value="Ingeniería Industrial">Ingeniería Industrial</option>
                    <option value="Profesorado en Matemática y Física">Profesorado en Matemática y Física</option>
                    <option value="Profesorado en Química Y Merceología">Profesorado en Química Y Merceología</option>
                    <option value="Químico">Químico</option>
                    <option value="Ingeniería en Seguridad e Higiene en el Trabajo">Ingeniería en Seguridad e Higiene en el Trabajo</option>
                    <option value="Licenciatura en Tecnología de los Alimentos">Licenciatura en Tecnología de los Alimentos<    /option>
                    <option value="Profesorado en Química">Profesorado en Química</option>
                    <option value="Técnico Universitario en Electromedicina">Técnico Universitario en Electromedicina</option>
                    <option value="Licenciatura en Tecnología Médica">Licenciatura en Tecnología Médica</option>
                    <option value="Licenciatura en Enseñanza de las Ciencias Naturales">Licenciatura en Enseñanza de las Ciencias Naturales</option>
                    <option value="Maestría en Enseñanza de las Ciencias Experimentales">Maestría en Enseñanza de las Ciencias Experimentales</option>
                    <option value="Maestría en Tecnología del Hormigón">Maestría en Tecnología del Hormigón</option>
                </select>
            `;
        } else if (selectElement.value === 'situacion_laboral') {
            filterValueContainer.innerHTML = `
                <select class="form-control mt-rem" name="filter_value" title="Seleccione el valor a filtrar">
                    <option value="Trabajo por cuenta propia">Trabajo por Cuenta Propia</option>
                    <option value="Trabajo en relación de dependencia">Trabajo en Relación de Dependencia</option>
                    <option value="Desempleado/a">Desempleado/a</option>
                    <option value="Jubilado/a">Jubilado/a</option>
                </select>
            `;
        } else if (selectElement.value === 'Fecha_egreso') {
            filterValueContainer.innerHTML = `
                <input type="year" class="form-control mt-rem" name="filter_value" title="Seleccione el año de egreso">
            `;
        } else {
            filterValueContainer.innerHTML = `
                <input type="text" class="form-control mt-rem" name="filter_value" placeholder="Valor a filtrar" title="Ingrese el valor a filtrar">
            `;
        }
    }

    function showModal(userId) {
        var modalContent = document.getElementById("modalContent");
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "get_user_data.php?id=" + userId, true);

        xhr.onload = function() {
            if (xhr.status === 200) {
                modalContent.innerHTML = xhr.responseText;
                var userModal = new bootstrap.Modal(document.getElementById('userModal'));
                userModal.show();
            } else {
                console.error('Error al cargar datos del usuario:', xhr.statusText);
                modalContent.innerHTML = "<p>Error al cargar datos del usuario.</p>";
            }
        };

        xhr.onerror = function() {
            console.error('Error de conexión con el servidor.');
            modalContent.innerHTML = "<p>Error de conexión con el servidor.</p>";
        };

        xhr.send();
    }

    function addFilter() {
        const filterContainer = document.getElementById('filterContainer');
        const newFilterGroup = document.createElement('div');
        newFilterGroup.classList.add('filter-group', 'mb-3');
        newFilterGroup.innerHTML = `
        <div class="row g-2">
            <div class="col">
                <label>Filtrar por:</label>
                <select class="form-select filter-column" onchange="updateFilterInput(this)" title="Seleccione la columna para filtrar">
                    <option value="">Seleccione</option>
                    <option value="carrera">Carrera</option>
                    <option value="apellido_nombre">Apellido y Nombre</option>
                    <option value="ciudad">Ciudad</option>
                    <option value="situacion_laboral">Situación Laboral</option>
                    <option value="Fecha_egreso">Fecha de Egreso</option>
                    <option value="empresa">Nombre de la Empresa</option>
                </select>
            </div>
            <div class="col filter-value">
                <input type="text" class="form-control mt-rem" placeholder="Valor a filtrar" title="Ingrese el valor a filtrar">
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger mt-rem" onclick="removeFilter(this)">Eliminar</button>
            </div>
        </div>
    `;
        filterContainer.appendChild(newFilterGroup);
    }

    function removeFilter(button) {
        button.closest('.filter-group').remove();
    }

    function orderBy() {
        const column = document.getElementById("order_by").value;
        const direction = document.getElementById("order_dir").value;

        console.log("Ordenar por:", column, "Dirección:", direction); // Para verificar los valores

        if (column && direction) {
            document.getElementById("filterForm").submit(); // Enviar el formulario
        }
    }

    function applyFilters() {
        const filters = [];
        document.querySelectorAll('.filter-group').forEach(group => {
            const column = group.querySelector('.filter-column').value;
            const value = group.querySelector('.filter-value select, .filter-value input').value;
            if (column && value) {
                filters.push({
                    column,
                    value
                });
            }
        });

        const form = document.createElement('form');
        form.method = 'get';
        form.action = '';
        const filtersInput = document.createElement('input');
        filtersInput.type = 'hidden';
        filtersInput.name = 'filters';
        filtersInput.value = JSON.stringify(filters);
        form.appendChild(filtersInput);

        document.body.appendChild(form);
        form.submit();
    }

    function clearFilters() {
        window.location.href = window.location.pathname; // Recargar la página sin parámetros de filtro
    }
    </script>

</body>

</html>