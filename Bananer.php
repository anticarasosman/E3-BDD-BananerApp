<?php
ini_set(option: 'memory_limit', value: '512M'); // Aumento la memoria máxima permitida para el script
function leer($archivo, $delimit = ";") 
{
    $data = [];
    if (($handle = fopen(filename: $archivo, mode: "r")) !== FALSE)
    {
        while (($row = fgetcsv(stream: $handle, length: 1000, separator: $delimit)) !== FALSE) 
        {
            $data[] = $row;
        }
        fclose(stream: $handle);
    }
    return $data;
}

function corregirNulos($data, $columnasNoNulas) 
{
    foreach ($data as &$row) 
    {
        foreach ($row as $index => &$valor) 
        {
            $valor = trim(string: $valor);
            if ($valor === "") 
            {
                $valor = NULL;
            }
            if (is_null(value: $valor) && in_array(needle: $index, haystack: $columnasNoNulas) && $valor !== "") 
            {
                $valor = "X"; // Los datos nulos simplemente les puse una X
            }
        }
    }
    return $data;
}

function corregirRango($data, $columnasRango, $rango)
{
    foreach ($data as &$row) 
    {
        foreach ($row as $index => &$valor) 
        {
            if (in_array(needle: $index, haystack: $columnasRango)) 
            {
                if (!in_array(needle: $valor, haystack: range(start: $rango[0], end: $rango[1]))) 
                {
                    if (strval(value: $valor) !== "k" && strval(value: $valor) !== "K") 
                    {
                        $valor = "X";
                    }
                }
            }
        }
    return $data;
    }
}

// Leer
$archivo1Data = leer(archivo: "Archivo1.csv");
$archivo2Data = leer(archivo: "archivo2.csv");

// Para definir las columnas que no pueden ser nulas, quiero definir un array con los indices de las columnas que no pueden ser nulas
$noNulos1 = [0, 1, 2, 3, 4, 5, 6, 7, 9, 10, 13, 14, 15, 16, 17, 18, 20, 21, 22];
$noNulos2 = [0, 1];

// Hago la corrección manual con las funciones definidas previamente
$archivo1DataCorregido = corregirNulos(data: $archivo1Data, columnasNoNulas: $noNulos1);
$archivo2DataCorregido = corregirNulos(data: $archivo2Data, columnasNoNulas: $noNulos2);

$archivo1DataCorregido = corregirRango(data: $archivo1DataCorregido, columnasRango: [19], rango: [1, 7]); //Notas A1

$archivo1DataCorregido = corregirRango(data: $archivo1DataCorregido, columnasRango: [5], rango: [0, 9]); //DV A1
$archivo2DataCorregido = corregirRango(data: $archivo2DataCorregido, columnasRango: [2], rango: [0, 9]); //DV A2



// 1. MATRIZ PERSONA
$personaData = [];
$runList = [];
foreach ($archivo1DataCorregido as $row) 
{
    $run = $row[4];
    if (!in_array(needle: $run, haystack: $runList)) 
    {
        $persona = 
        [
            'RUN' => $run,
            'DV' => $row[5],
            'Nombres' => $row[6],
            'Apellido Paterno' => $row[7],
            'Apellido Materno' => $row[8],
            'Nombre completo' => $row[9],
            'Telefono' => "X",
            'mail personal' => $row[11],
            'mail institucional' => $row[12]
        ];
        $personaData[] = $persona;
        $runList[] = $run;
    }
}

foreach ($archivo2DataCorregido as $row) 
{
    $run = $row[0];
    if (!in_array(needle: $run, haystack: $runList)) 
    {
        $persona = 
        [
            'RUN' => $run,
            'DV' => $row[1],
            'Nombres' => $row[4],
            'Apellido Paterno' => $row[2],
            'Apellido Materno' => $row[3],
            'Nombre completo' => "X",
            'Telefono' => $row[7],
            'mail personal' => $row[5],
            'mail institucional' => $row[6]
        ];
        $personaData[] = $persona;
        $runList[] = $run;
    }
}

// 2. MATRIZ ESTUDIANTE
$estudianteData = [];
$runList2 = [];
foreach ($archivo1DataCorregido as $row) 
{
    $run = $row[4];
    if (!in_array(needle: $run, haystack: $runList2)) 
    {
        $estudiante = 
        [
            'Cohorte' => $row[0],
            'Numero de estudiante' => $row[10],
            'Codigo Plan' => $row[1],
            'ultimo Logro' => $row[20],
            'Fecha Logro' => $row[21],
            'Ultima toma de ramos' => $row[22],

        ];
        $estudianteData[] = $estudiante;
        $runList2[] = $run;
    }
}

// 3. MATRIZ PROFESOR
$profesorData = [];
$runList3 = [];
foreach ($archivo2DataCorregido as $row) 
{
    $run = $row[0];
    if (!in_array(needle: $run, haystack: $runList3) && !is_null(value: $row[13])) 
    {
        $profesor = 
        [
            'RUN' => $run,
            'DV' => $row[1],
            'contrato' => $row[8],
            'Jornada' => !empty($row[9]) ? $row[9] : (!empty($row[10]) ? $row[10] : null),
            'dedicación' => $row[11],
            'grado academico' => $row[12],
        ];
        $profesorData[] = $profesor;
        $runList3[] = $run;
    }
}

// 4. MATRIZ ADMINISTRATIVO
$adminData = [];
$runList4 = [];
foreach ($archivo2DataCorregido as $row) 
{
    $run = $row[0];
    if (!in_array(needle: $run, haystack: $runList4) && !is_null(value: $row[14])) 
    {
        $admin = 
        [
            'RUN' => $run,
            'DV' => $row[1],
            'cargo' => $row[14],
        ];
        $adminData[] = $admin;
        $runList4[] = $run;
    }
}

// 5. MATRIZ CURSOS
$cursosData = [];
$sigla_seccionList5 = [];
foreach ($archivo1DataCorregido as $row)
{
    $sigla_seccion = [$row[14], $row[19]];
    if (!in_array(needle: $sigla_seccion[0], haystack: array_column(array: $sigla_seccionList5, column_key: 0))) {
        $sigla_seccionList5[] = $sigla_seccion;
    }
    if (!in_array(needle: $sigla_seccion, haystack: $sigla_seccionList5))
    {
        foreach ($sigla_seccionList5 as &$elemento) 
        {
            if ($sigla_seccion[0] == $elemento[0] && $sigla_seccion[1] >= $elemento[1]) // Guardo la seccion mas alta. Supondremos que ese es el nunmero de secciones total.
            {
                $elemento[1] = $sigla_seccion[1];
            }
        }
    }
}

foreach($archivo1DataCorregido as $row)
{
    $sigla_seccion = [$row[14], $row[19]];
    if (in_array(needle: $sigla_seccion, haystack: $sigla_seccionList5))
    {
        $curso = 
        [
            'sigla curso' => $row[14],
            'curso' => $row[15],
            'Secciones' => $row[19],
            'Nivel del curso' => $row[17],
        ];
        $cursosData[] = $curso;
    }
}

// 6. MATRIZ NOTAS
$notasData = [];
$List5 = [];
foreach ($archivo1DataCorregido as $row) 
{
    $nota = 
        [
        'Numero de estudiante' => $row[14],
        'sigla curso' => $row[15],
        'Periodo curso' => $row[13],
        'Calificación' => $row[18],
        'Nota' => $row[19],
        ];
    $notasData[] = $nota;
}

$hand = fopen(filename: 'php://stdin', mode: 'r');
echo "Búsqueda':\nCarga academica acumulada [Ingrese 1]\nLista de cursos [Ingrese 2]\n y presione Enter: ";
$input = trim(string: fgets(stream: $hand));

if ($input == 1) 
{
    echo "Ingrese RUN del estudiante que busca, sin puntos ni guión y sin DV: ";
    $run = trim(string: fgets(stream: $hand));
    if (!preg_match(pattern: '/^\d{7,8}$/', subject: $run)) // Para especificar que debe ser un RUN entre 7 y 8 digitos
    {
        echo "RUN inválido. \n";
        fclose(stream: $hand);
        exit;
    }
    $found = false; // Variable para verificar si se encontró
    foreach ($archivo1DataCorregido as $data) 
    {
        if ($data[4] == $run) // Se me hizo mas comodo buscar directo en el archivo, en vez de usar las matrices
        {
            $found = true;
            $periodo = $data[13]; 
            $siglaCurso = $data[14]; 
            $curso = $data[15]; 
            $nota = $data[19]; 
            $calificacion = $data[18]; 

            echo "Periodo: $periodo, Sigla Curso: $siglaCurso, Curso: $curso, Nota: $nota, Calificación: $calificacion\n";
        }
    }

    if (!$found) {
        echo "No se encontró el RUN ingresado.\n";
    }
}
else if ($input == 2) 
{
    echo "Ingrese el periodo del curso a buscar: ";
    $periodo = trim(string: fgets(stream: $hand));
    
    echo "Ingrese sigla del curso: ";
    $sigla = trim(string: fgets(stream: $hand));

    $found = false;

    foreach ($archivo1DataCorregido as $data) 
    {
        if ($data[13] == $periodo && $data[14] == $sigla)
        {
            $found = true;
            $cohorte = $data[0]; 
            $nombre_completo = $data[9]; 
            $run = $data[4]; 
            $numero = $data[10]; 

            echo "Cohorte: $cohorte, Nombre completo: $nombre_completo, RUN: $run, Número de estudiante: $numero\n";
        }
    }

    if (!$found) {
        echo "No se encontró ningún curso con el periodo y sigla proporcionados.\n";
    }
}
fclose(stream: $hand);
?>