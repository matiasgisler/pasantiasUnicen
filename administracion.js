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
                <option value="DNI">DNI</option>
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

document.querySelectorAll('.column-toggle').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const columnClass = this.getAttribute('data-column');
        const columnCells = document.querySelectorAll(`.${columnClass}`);
        columnCells.forEach(cell => {
            cell.style.display = this.checked ? '' : 'none';
        });
    });
});

function orderBy(event) {
    event.preventDefault();
    const column = document.getElementById("order_by").value;
    const direction = document.getElementById("order_dir").value;

    if (column && direction) {
        // Crear un objeto con los parámetros actuales de la URL
        const urlParams = new URLSearchParams(window.location.search);

        // Actualizar o agregar los parámetros de ordenación
        urlParams.set('order_by', column);
        urlParams.set('order_dir', direction);

        // Construir la nueva URL
        const newUrl = window.location.pathname + '?' + urlParams.toString();

        // Realizar una petición AJAX
        fetch(newUrl)
            .then(response => response.text())
            .then(html => {
                // Crear un elemento temporal para parsear el HTML
                const tempElement = document.createElement('div');
                tempElement.innerHTML = html;

                // Actualizar solo la tabla y la paginación
                document.querySelector('table').outerHTML = tempElement.querySelector('table')
                    .outerHTML;
                document.querySelector('.pagination').outerHTML = tempElement.querySelector(
                    '.pagination').outerHTML;

                // Actualizar la URL del navegador sin recargar la página
                window.history.pushState({}, '', newUrl);
            })
            .catch(error => {
                console.error('Error:', error);
            });
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