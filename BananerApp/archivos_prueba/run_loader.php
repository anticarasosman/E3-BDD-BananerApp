<?php
include_once 'DataLoader.php';
$dataLoader = new DataLoader();

// Archivos CSV a cargar
$files = [
    'Asignaturas' => 'files/Asignaturas.csv',
    'Docentes_planificados' => 'files/Docentes_planificados.csv',
    'Estudiantes' => 'files/Estudiantes.csv',
    'Malla' => 'files/Malla.csv',
    'Notas' => 'files/Notas.csv',
    'Planeacion' => 'files/Planeacion.csv',
    'Planes' => 'files/Planes.csv',
    'Prerrequisitos' => 'files/Prerrequisitos.csv'
];

foreach ($files as $tableName => $filePath) {
    $dataLoader->loadCSV(filePath: $filePath, tableName: $tableName);
}
?>