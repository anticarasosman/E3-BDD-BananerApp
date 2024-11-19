<?php
include('config/connection.php');
require('table_parameters.php');

// Crear las tablas
foreach($tablas_iniciales as $tabla => $atributos) {
    try {
        echo "Creando tabla $tabla...\n";
        $db->beginTransaction();
        $createTableQuery = "CREATE TABLE IF NOT EXISTS $tabla ($atributos);";
        $db->exec($createTableQuery);
        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        echo "Error al crear la tabla $tabla: " . $e->getMessage();
    }
}
// Crear tablas temporales
foreach($tablas_temporales as $tabla => $atributos) {
    try {
        echo "Creando tabla temporal $tabla...\n";
        $db->beginTransaction();
        $createTableQuery = "CREATE TEMPORARY TABLE IF NOT EXISTS $tabla ($atributos);";
        $db->exec($createTableQuery);
        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        echo "Error al crear la tabla temporal $tabla: " . $e->getMessage();
    }
}
?>