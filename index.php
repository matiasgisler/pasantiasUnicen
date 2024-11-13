<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Etapas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">    
    <style>
        *{
            font-family: "Poppins", serif;
            font-weight: 400;
            font-style: normal;
        }
        body{
            background-image: linear-gradient(to top left,#97ae39, #004672);
        }
        form{
            background-color: #f8f9fa;
            padding: 2%;
            border-radius: 25px;
        }
        .progress-bar {
            position: relative;
        }

        .progress {
            height: 20px;
            background-color: #f8f9fa;
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
        }
    </style>
</head>
<body class="container mt-4">

    <form id="formulario" method="POST" action="procesar_formulario.php">
    <img src="https://virtual.fio.unicen.edu.ar/elearning2/pluginfile.php/1/theme_moove/logo/1728044449/logofio.svg" class="img-fluid rounded mx-auto d-block" alt="Logo FIO">
    <br>
    <h1 class="text-center">Formulario "Graduados FIO"</h1>
        <!-- Barra de progreso -->
        <div class="progress" >
            <div id="progress" class="progress-bar" style="width: 25%" role="progressbar" aria-label="Progreso de Formulario" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <label id="progress-label" class="text-center"></label>

        <!-- Etapa 1 -->
        <div class="step active" id="step1">
            <h2>Etapa 1 de 5</h2>

            <div class="mb-3">
                <label for="apellido_nombre" class="form-label">Nombre y Apellido:</label>
                <input type="text" id="apellido_nombre" name="apellido_nombre" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="DNI" class="form-label">Ingrese su Numero de Documento:</label>
                <input type="text" id="DNI" name="DNI" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="fecha_egreso" class="form-label">Fecha de Egreso:</label>
                <input type="date" id="fecha_egreso" name="fecha_egreso" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="telefono" class="form-label">Nº de Teléfono Celular:</label>
                <input type="text" id="telefono" name="telefono" class="form-control" required>
            </div> 

            <div class="mb-3">
                <label for="correo" class="form-label">Correo electronico:</label>
                <input type="email" id="correo" name="correo" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="ciudad" class="form-label">Ciudad de Residencia:</label>
                <input type="text" id="ciudad" name="ciudad" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="carrera" class="form-label">Carrera:</label>
                <select id="carrera" name="carrera" class="form-select" required>
                    <option value="Ingeniería en Agrimensura">Ingeniería en Agrimensura</option>
                    <option value="Ingeniería en Construcciones">Ingeniería en Construcciones (no vigente)</option>
                    <option value="Ingeniería civil">Ingeniería civil</option>
                    <option value="Ingeniería Electromecánica">Ingeniería Electromecánica</option>
                    <option value="Ingeniería Industrial">Ingeniería Industrial</option>
                    <option value="Ingeniería Química">Ingeniería Química</option>
                    <option value="Ingeniería en Seguridad e Higiene en el Trabajo">Ingeniería en Seguridad e Higiene en el Trabajo</option>
                    <option value="Licenciatura en Tecnología de los Alimentos">Licenciatura en Tecnología de los Alimentos</option>
                    <option value="Profesorado en Química">Profesorado en Química</option>
                    <option value="Técnico Universitario en Electromedicina">Técnico Universitario en Electromedicina</option>
                    <option value="Licenciatura en Tecnología Médica">Licenciatura en Tecnología Médica</option>
                    <option value="Licenciatura en Enseñanza de las Ciencias Naturales">Licenciatura en Enseñanza de las Ciencias Naturales</option>
                    <option value="Maestría en Enseñanza de las Ciencias Experimentales">Maestría en Enseñanza de las Ciencias Experimentales</option>
                    <option value="Maestría en Tecnología del Hormigón">Maestría en Tecnología del Hormigón</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="situacion_laboral" class="form-label">Situación Laboral:</label>
                <select id="situacion_laboral" name="situacion_laboral" class="form-select" required>
                    <option value="Trabajo por cuenta propia">Trabajo por Cuenta Propia</option>
                    <option value="Trabajo en relación de dependencia">Trabajo en Relación de Dependencia</option>
                    <option value="Desempleado/a">Desempleado/a</option>
                    <option value="Jubilado/a">Jubilado/a</option>
                </select>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="button" class="btn btn-primary me-md-2" onclick="nextStep()">SIGUIENTE</button>
            </div>        </div>

        <!-- Etapa 2 -->
         
        <div class="step" id="step2">
        
        </script>
            <h2>Etapa 2 de 5</h2>
            <div class="mb-3">
                <label for="empresa" class="form-label">Nombre de la Empresa / Organización:</label>
                <input type="text" id="empresa" name="empresa" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="localidadempresa" class="form-label">Localidad:</label>
                <input type="text" id="localidadempresa" name="localidadempresa" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="mail" class="form-label">Correo Electronico Laboral:</label>
                <input type="email" id="mail" name="mail" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="cargo" class="form-label">Cargo que Ocupa:</label>
                <input type="text" id="cargo" name="cargo" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="area" class="form-label">Área:</label>
                <input type="text" id="area" name="area" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="Actividad" class="form-label">Cuál de estás opciones describe mejor su actividad?:</label>
                <select id="Actividad" name="Actividad" class="form-select" required>
                    <option value="APE">Actividades profesionales específicas (APE)</option>
                    <option value="APNE">Actividades profesionales no específicas (APNE)</option>
                    <option value="AA">Actividad Académica (AA)</option>
                    <option value="AG">Actividades gerenciales (AG)</option>
                    <option value="OA">Otras actividades (OA)</option>
                </select>
            </div>            
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="button" class="btn btn-secondary me-md-2" onclick="prevStep()">ATRÁS</button>
                <button type="button" class="btn btn-primary" onclick="nextStep()">SIGUIENTE</button>
            </div>
        </div>

        <!-- Etapa 3 -->
        <div class="step" id="step3">

            <h2>Etapa 3 de 5</h2>

            <div class="mb-3">
                <label for="vinculacion" class="form-label">Actualmente tiene alguna vinculación con la Universidad?:</label>
                <select id="vinculacion" name="vinculacion" class="form-select" required>
                    <option value="SI">SI</option>
                    <option value="NO">NO</option>
                </select>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="button" class="btn btn-secondary me-md-2" onclick="prevStep()">ATRÁS</button>
                <button type="button" class="btn btn-primary" onclick="nextStep()">SIGUIENTE</button>
            </div>
        </div>

        <!-- Etapa 4 ------------------------------------------------------------------------------->
        <div class="step" id="step4">
            <h2>Etapa 4 de 5</h2>

            <div class="mb-3">
                <label for="Docente" class="form-label">Usted es Docente? (Indique sí o no en la Respuesta):</label>
                <select id="Docente" name="Docente" class="form-select" required onchange="toggleDocenteFields()">
                    <option value="SI">SI</option>
                    <option value="NO">NO</option>
                </select>
            </div>

            <div class="mb-3 docente-only">
                <label for="cargo_docente" class="form-label">Indique Cuál es el Cargo que Ocupa en la Universidad:</label>
                <select id="cargo_docente" name="cargo_docente" class="form-select">
                    <option value="Profesor Titular">Profesor Titular</option>
                    <option value="Profesor Asociado">Profesor Asociado</option>
                    <option value="Profesor Adjunto">Profesor Adjunto</option>
                    <option value="Jefe de Trabajos Prácticos">Jefe de Trabajos Prácticos</option>
                    <option value="Ayudante Diplomado">Ayudante Diplomado</option>
                </select>
            </div>

            <div class="mb-3 docente-only">
                <label for="Departamento_docente" class="form-label">Indique en qué Departamento Cumple Funciones de DOCENTE:</label>
                <input type="text" id="Departamento_docente" name="Departamento_docente" class="form-control">
            </div>

            <div class="mb-3">
                <label for="becario" class="form-label">Si es BECARIO/A de Posgrado: Indique Tipo, Duración y Entidad que otorga la Beca:
                </label>
                <input type="text" id="becario" name="becario" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="no_docente" class="form-label">Si es NO-DOCENTE Indique a qué Agrupamiento Pertenece y qué Actividad Desarrolla:</label>
                <input type="text" id="no_docente" name="no_docente" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="desocupado" class="form-label">Si está DESOCUPADO/A o es JUBILADO/A, indique qué tipo de actividad lo mantiene vinculado a la FIO.</label>
                <input type="text" id="desocupado" name="desocupado" class="form-control" required>
            </div>

            <!--------------------------------------------------------------------------------->
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="button" class="btn btn-secondary me-md-2" onclick="prevStep()">ATRÁS</button>
                <button type="button" class="btn btn-primary" onclick="nextStep()">SIGUIENTE</button>
            </div>     
        </div>

        <!-- Etapa 5 -->
        <div class="step" id="step5">
            <h2>Etapa 5 de 5</h2>
            <div class="mb-3">
                <label for="capacitarse" class="form-label">Sobre qué temática le interesaría CAPACITARSE, Indicar cada una, separada con ";":</label>
                <input type="text" id="capacitarse" name="capacitarse" class="form-control">
            </div>

            <div class="mb-3">
                <label for="acompanar" class="form-label">DE qué manera la FIO lo/la puede acompañar luego de su graduación. Indicar respuestas, separadas con ";":</label>
                <input type="text" id="acompanar" name="acompanar" class="form-control">
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="button" class="btn btn-secondary me-md-2" onclick="prevStep()">ATRÁS</button>
                <button type="submit" class="btn btn-success">MANDAR FORMULARIO</button>
                <button type="button" class="btn btn-danger" onclick="resetForm()">BORRAR FORMULARIO</button>
            </div>       
        </div>

    </form>

    <script>
            function toggleDocenteFields() {
            const docenteSelect = document.getElementById('Docente');
            const isDocente = docenteSelect.value === 'SI';

            const docenteFields = document.querySelectorAll('.docente-only');

            docenteFields.forEach(field => {
                if (isDocente) {
                    field.style.display = 'block';
                    field.querySelectorAll('input, select').forEach(input => {
                        input.required = true;
                    });
                } else {
                    field.style.display = 'none';
                    field.querySelectorAll('input, select').forEach(input => {
                        input.required = false;
                    });
                }
            });
            }
            // Llamada inicial para ocultar los campos si el usuario ya seleccionó "NO" previamente
            toggleDocenteFields();
        </script>    

    <script src="scripts.js?v=1.0" defer></script>
</body>
</html>
