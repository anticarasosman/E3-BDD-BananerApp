<?php
$flag_file = 'sync_done.flag';

// Verificar si la sincronización ya se ha realizado
if (file_exists($flag_file)) {
    echo "La sincronización ya se ha realizado.\n";
    echo "TODO LISTO";
    exit;
}

// Conexión a la base de datos externa
$conn = pg_connect(("host=146.155.13.71 port=5432 dbname=e3profesores user=grupo49e3 password=pinkiynegra1"));
if (!$conn) {
    die("Conexion fallida: " . pg_last_error());
}

// Listar todas las tablas en la base de datos externa
$tables_result = pg_query($conn, "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
if (!$tables_result) {
    die("Error en la consulta de tablas: " . pg_last_error());
}

echo "Tablas en la base de datos externa:\n";
while ($table = pg_fetch_assoc($tables_result)) {
    echo $table['table_name'] . "\n";
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
    $update_query = "
        INSERT INTO personas (RUN, DV, Nombres, Apellido_Paterno, Apellido_Materno, Nombre_Completo, Telefono, Correo_Personal, Correo_Institucional)
        VALUES ('{$row['run']}', '', '{$row['nombre']}', '{$row['apellido1']}', '{$row['apellido2']}', '{$row['nombre']} {$row['apellido1']} {$row['apellido2']}', '{$row['telefono']}', '{$row['email_personal']}', '{$row['email_institucional']}')
        ON CONFLICT (RUN) DO UPDATE SET
            DV = EXCLUDED.DV,
            Nombres = EXCLUDED.Nombres,
            Apellido_Paterno = EXCLUDED.Apellido_Paterno,
            Apellido_Materno = EXCLUDED.Apellido_Materno,
            Nombre_Completo = EXCLUDED.Nombre_Completo,
            Telefono = EXCLUDED.Telefono,
            Correo_Personal = EXCLUDED.Correo_Personal,
            Correo_Institucional = EXCLUDED.Correo_Institucional;
    ";
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