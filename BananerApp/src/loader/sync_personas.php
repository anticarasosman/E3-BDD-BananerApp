<?php
$flag_file = 'sync_done.flag';

// Verificar si la sincronización ya se ha realizado
if (file_exists($flag_file)) {
    echo "La sincronización ya se ha realizado.\n";
    exit;
}

// Conexión a la base de datos externa
$conn = pg_connect(("host=localhost port=5432 dbname=e3profesores user=grupo49e3 password=contraseña"));
if (!$conn) {
    die("Conexion fallida: " . pg_last_error());
}

// Consulta a la base de datos externa
$result = pg_query($conn, "SELECT * FROM profesores");
if (!$result) {
    die("Error en la consulta: " . pg_last_error());
}

// Conexión a la base de datos local
$local_conn = pg_connect("host=localhost dbname=grupo49 user=grupo49 password=1234");
if (!$local_conn) {
    die("Error al conectar con la base de datos locales: " . pg_last_error());
}

// Iterar sobre los resultados y actualizar la base de datos local "Personas"
while ($row = pg_fetch_assoc($result)) {
    // Assuming $row contains relevant fields you need
    $update_query = "UPDATE Personas SET column1 = '{$row['column1']}', column2 = '{$row['column2']}' WHERE condition";
    $update_result = pg_query($local_conn, $update_query);
    if (!$update_result) {
        echo "Error al actualizar tabla Personas: " . pg_last_error() . "\n";
    }
}

// Cerrar conexiones
pg_close($conn);
pg_close($local_conn);

// Crear el archivo de bandera para indicar que la sincronización se ha realizado
file_put_contents($flag_file, '');

// Mensaje de finalización
echo "Sincronización de datos COMPLETADA.\n";
?>