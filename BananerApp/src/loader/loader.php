<<<<<<< HEAD
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
            ($handle);
        }
    

        if (($handle = fopen($output_file, "w")) !== false) {
            fputcsv($handle, $header, ";"); // Escribir la cabecera
            foreach ($rows as $row) {
                fputcsv($handle, $row, ";");
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
    remove_duplicates('files/planeacion.csv', 'files/Planeacion_unicos.csv', [5, 13]);
    echo "Planeacion unicos LISTO\n";
    remove_duplicates('files/Estudiantes.csv', 'files/Estudiantes_unicos.csv', [3]);
    echo "Estudiantes unicos LISTO\n";

    require_once('config/connection.php');
    require_once('create_tables.php');
    require_once('poblate_tables.php');

    // Ejecutar el script para sincronizar la tabla de personas
    echo "Sincronizando tabla de personas...\n";
    require_once('sync_personas.php');
    echo "TODO LISTO\n";
?>