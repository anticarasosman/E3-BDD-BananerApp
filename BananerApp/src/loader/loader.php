<?php
function remove_duplicates($input_file, $output_file, $key_columns) {
    $rows = array();
    $unique_keys = array("");

    if (($handle = fopen($input_file, "r")) !== false) {
        $header = fgetcsv($handle, 1000, ";"); // Leer la cabecera
        while (($data = fgetcsv($handle, 1000, ";")) !== false) {
            $key_values = array();
            foreach ($key_columns as $key_column) {
                $key_values[] = $data[$key_column];
            }
            $key = implode("-", $key_values); // Crear una clave única combinando los valores de las columnas clave
            if (!in_array($key, $unique_keys)) {
                $unique_keys[] = $key;
                $rows[] = $data;
            }
        }
        fclose($handle);
    }

    if (($handle = fopen($output_file, "w")) !== false) {
        fputcsv($handle, $header, ";"); // Escribir la cabecera
        foreach ($rows as $row) {
            fputcsv($handle, $row, ";");
        }
        fclose($handle);
    }
}

// Usar la función para eliminar duplicados
echo "Eliminando duplicados...\n";
remove_duplicates('files/Prerrequisitos.csv', 'files/Prerrequisitos_unicos.csv', [0]);
echo "Prerrequisitos unicos LISTO\n";
remove_duplicates('files/Planes.csv', 'files/Planes_unicos.csv', [0]);
echo "Planes unicos LISTO\n";
remove_duplicates('files/Asignaturas.csv', 'files/Asignaturas_unicas.csv', [1]);
echo "Asignaturas unicos LISTO\n";
remove_duplicates('files/Planeacion.csv', 'files/Planeacion_unicos.csv', [5, 13]);
echo "Planeacion unicos LISTO\n";
remove_duplicates('files/Estudiantes.csv', 'files/Estudiantes_unicos.csv', [3]);
echo "Estudiantes unicos LISTO\n";
remove_duplicates('files/Docentes_Planificados.csv', 'files/Docentes_Planificados_unicos.csv', [0]);
echo "Docentes_Planificados unicos LISTO\n";

require_once('config/connection.php');
require_once('create_tables.php');
require_once('poblate_tables.php');

// Ejecutar el script para sincronizar la tabla de personas
echo "Sincronizando tabla de personas...\n";
require_once('sync_personas.php');
echo "TODO LISTO\n";

// Verificar si la tabla temporal 'acta' fue creada
try {
    $result = $db->query("SELECT 1 FROM acta LIMIT 1");
    if ($result !== false) {
        echo "La tabla temporal 'acta' fue creada correctamente.\n";
    }
} catch (Exception $e) {
    die("Error: La tabla temporal 'acta' no fue creada. " . $e->getMessage());
}

// Leer el archivo CSV y cargar los datos en la tabla temporal 'acta'
$csvFile = fopen('archivos_E3/notas adivinacion I.csv', 'r');
if ($csvFile === false) {
    die("No se pudo abrir el archivo CSV.");
}

echo "Iniciando la transacción para insertar datos en la tabla temporal 'acta'...\n";

// Iniciar la transacción
$db->beginTransaction();

try {
    // Leer y validar los datos del CSV
    $lineNumber = 0;
    while (($data = fgetcsv($csvFile, 1000, ";")) !== FALSE) {
        $lineNumber++;
        if ($lineNumber == 1) continue; // Saltar la cabecera

        $numero_de_alumno = $data[0];
        $run = $data[1];
        $asignatura = $data[2];
        $seccion = $data[3];
        $periodo = $data[4];
        $oportunidad_dic = str_replace(',', '.', $data[5]);
        $oportunidad_mar = isset($data[6]) ? str_replace(',', '.', $data[6]) : NULL;

        // Validar las notas
        if (!is_numeric($oportunidad_dic) || ($oportunidad_mar !== NULL && !is_numeric($oportunidad_mar))) {
            throw new Exception("Nota de $numero_de_alumno contiene un valor erróneo, corríjalo manualmente en el archivo de origen y vuelva a cargar.");
        }

        // Insertar los datos en la tabla temporal
        $insertQuery = $db->prepare("INSERT INTO acta (numero_de_alumno, run, asignatura, seccion, periodo, oportunidad_dic, oportunidad_mar) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertQuery->execute([$numero_de_alumno, $run, $asignatura, $seccion, $periodo, $oportunidad_dic, $oportunidad_mar]);
    }

    // Confirmar la transacción
    $db->commit();
    echo "Datos insertados correctamente en la tabla temporal 'acta'.";
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $db->rollBack();
    echo "Error al insertar datos: " . $e->getMessage();
}

// Cerrar el archivo CSV
fclose($csvFile);
?>