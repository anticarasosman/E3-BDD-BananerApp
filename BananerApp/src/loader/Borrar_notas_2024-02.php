<?php

//ESTE CODIGO SOLO SE EJECUTA PARA ELIMINAR TODAS LAS TUPLAS CON EL PERIODO 2024-02 DE LA TABLA NOTAS
$rows = array();

if (($handle = fopen("files/Notas.csv", "r")) !== false) {
    $header = fgetcsv($handle, 1000, ";"); // Leer la cabecera
    while (($data = fgetcsv($handle, 1000, ";")) !== false) {
        if ($data[10] != "2024-02") {
            $rows[] = $data;
        }
    }
    ($handle);
}

if (($handle = fopen("files/archivos_E3/Notas_modificadas.csv", "w")) !== false) {
    fputcsv($handle, $header, ";"); // Escribir la cabecera
    foreach ($rows as $row) {
        fputcsv($handle, $row, ";");
    }
    fclose($handle);
}