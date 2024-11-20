<?php
include('config/connection.php');


// P.4.1
try {
    $createTableQuery = "CREATE TEMPORARY TABLE IF NOT EXISTS acta (
        numero_de_alumno INT,
        run VARCHAR(10),
        asignatura VARCHAR(10),
        seccion INT,
        periodo VARCHAR(7),
        oportunidad_dic VARCHAR(4),
        oportunidad_mar FLOAT
    )";
    $db->exec($createTableQuery);
    echo "La tabla temporal 'acta' fue creada correctamente.\n";
} catch (Exception $e) {
    die("Error: No se pudo crear la tabla temporal 'acta'. \n" . $e->getMessage());
}


try {
    $result = $db->query("SELECT 1 FROM acta LIMIT 1");
    if ($result !== false) {
        echo "La tabla temporal 'acta' fue verificada correctamente.\n";
    }
} catch (Exception $e) {
    die("Error: La tabla temporal 'acta' no fue creada. \n" . $e->getMessage());
}

$csvFile = fopen('files/archivos_E3/notas adivinacion I.csv', 'r');
if ($csvFile === false) {
    die("No se pudo abrir el archivo CSV.");
}

echo "Iniciando la transacción para insertar datos en la tabla temporal 'acta'...\n";

$db->beginTransaction();

try {

    $lineNumber = 0;
    while (($data = fgetcsv($csvFile, 1000, ";")) !== FALSE) {
        $lineNumber++;
        if ($lineNumber == 1) continue; 

        $numero_de_alumno = $data[0];
        if (empty($numero_de_alumno)) {
            continue; 
        }

        $run = $data[1];
        $asignatura = $data[2];
        $seccion = $data[3];
        $periodo = $data[4];
        $oportunidad_dic = $data[5];
        $oportunidad_mar = isset($data[6]) && $data[6] !== '' ? str_replace(',', '.', $data[6]) : NULL;

        if ($oportunidad_dic === 'NP' || is_numeric(str_replace(',', '.', $oportunidad_dic))) {
            if ($oportunidad_dic !== 'NP' && floatval(str_replace(',', '.', $oportunidad_dic)) > 4.0 && $oportunidad_mar !== NULL) {
                throw new Exception("Nota de $numero_de_alumno contiene un valor erróneo en oportunidad_mar, corríjalo manualmente en el archivo de origen y vuelva a cargar.\n");
            }
        } else {
            throw new Exception("Nota de $numero_de_alumno contiene un valor erróneo en oportunidad_dic, corríjalo manualmente en el archivo de origen y vuelva a cargar.\n");
        }

        $insertQuery = $db->prepare("INSERT INTO acta (numero_de_alumno, run, asignatura, seccion, periodo, oportunidad_dic, oportunidad_mar) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertQuery->execute([$numero_de_alumno, $run, $asignatura, $seccion, $periodo, $oportunidad_dic, $oportunidad_mar]);
    }

    $db->commit();
    echo "Datos insertados correctamente en la tabla temporal 'acta'.\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error al insertar datos: " . $e->getMessage();
}

echo "Los datos insertados en la tabla son:\n";
try {
    $result = $db->query("SELECT * FROM acta");
    if ($result !== false) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
    }
} catch (Exception $e) {
    die("Error: No se pudo obtener los datos de la tabla temporal. " . $e->getMessage());
}

// P.4.2

// require_once('create_view.php');

echo "Manteniendo la conexión abierta. Presiona Ctrl+C para salir.\n";
while (true) {
    sleep(1);
}

fclose($csvFile);
?>