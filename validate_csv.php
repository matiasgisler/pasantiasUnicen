<?php
function validateCSVStructure($filePath)
{
    // Expected headers (using the exact names from your CSV)
    $expectedHeaders = [
        'Marca temporal',
        'Carrera',
        'Apellido y nombre',
        'Fecha Egreso',
        'NÂº de TelÃ©fono',
        'Celular',
        'Email',
        'Ciudad de residencia',
        'SituaciÃ³n laboral',
        'Nombre de la empresa / organizaciÃ³n',
        'Localidad',
        'Cargo que ocupa',
        'Ãrea	Mail laboral',
        'Su trabajo estÃ¡ relacionado con la carrera?',
        'Actualmente tiene alguna vinculaciÃ³n con la Universidad?',
        'CuÃ¡l de estÃ¡s opciones describe mejor su actividad?',
        'Si es DOCENTE, indique cuÃ¡l es el cargo que ocupa en la Universidad',
        'Indique en quÃ© Departamento cumple funciones de DOCENTE',
        'Si es BECARIO/A de Posgrado, indique Tipo, DuraciÃ³n, y Entidad que otorga la beca.',
        'Si es NODOCENTE indique a quÃ© agrupamiento pertenece y quÃ© actividad desarrolla.',
        'Si estÃ¡ DESOCUPADO/A o es JUBILADO/A, indique quÃ© tipo de actividad lo mantiene vinculado a la FIO.',
        'Sobre qué temática le interesaría CAPACITARSE'
    ];

    $errors = [];

    // Verificar si el archivo existe y puede ser abierto
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        // Leer la primera fila (encabezados)
        // $headers = fgetcsv($handle);

        // Validar que todos los encabezados esperados estén presentes
        // $missingHeaders = array_diff($expectedHeaders, $headers);
        // if (!empty($missingHeaders)) {
        //     $errors[] = "Faltan los siguientes encabezados: " . implode(", ", $missingHeaders);
        // }

        // Validar el formato de los datos en cada fila
        // $rowNumber = 1; // Comenzar desde 1 para contar después de los encabezados
        // while (($data = fgetcsv($handle)) !== FALSE) {
        //     $rowNumber++;

        //     // Validar que la fila tenga la cantidad correcta de columnas
        //     if (count($data) !== count($expectedHeaders)) {
        //         $errors[] = "La fila $rowNumber tiene un número incorrecto de columnas";
        //         continue;
        //     }

        //     // Validar formato de fecha en 'Marca temporal'
        //     if (!empty($data[0]) && !strtotime($data[0])) {
        //         $errors[] = "Fila $rowNumber: Formato de fecha invílido en Marca temporal";
        //     }

        //     // Validar que Carrera no esté vacío
        //     if (empty($data[1])) {
        //         $errors[] = "Fila $rowNumber: El campo Carrera es obligatorio";
        //     }

        //     // Validar que Apellido y nombre no esté vacío
        //     if (empty($data[2])) {
        //         $errors[] = "Fila $rowNumber: El campo Apellido y nombre es obligatorio";
        //     }

        //     // Validar formato de fecha en 'Fecha Egreso'
        //     if (!empty($data[3])) {
        //         $fecha = DateTime::createFromFormat('d/m/y', $data[3]);
        //         if (!$fecha) {
        //             $errors[] = "Fila $rowNumber: Formato de fecha invílido en Fecha Egreso (debe ser dd/mm/yy)";
        //         }
        //     }

        //     // Validar formato de email
        //     if (!empty($data[5]) && !filter_var($data[5], FILTER_VALIDATE_EMAIL)) {
        //         $errors[] = "Fila $rowNumber: Formato de email invílido";
        //     }
        // }
        fclose($handle);
    } else {
        $errors[] = "No se pudo abrir el archivo CSV";
    }

    return [
        'isValid' => empty($errors),
        'errors' => $errors
    ];
}
