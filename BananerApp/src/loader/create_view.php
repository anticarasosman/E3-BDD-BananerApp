<?php
include('config/connection.php');

try {
    // Crear la stored procedure para crear la vista del acta de notas
    $createProcedureQuery = "
    CREATE OR REPLACE FUNCTION create_acta_view()
    RETURNS void AS $$
    BEGIN
        CREATE OR REPLACE VIEW acta_notas AS
        SELECT 
            a.numero_de_alumno,
            a.asignatura AS curso,
            a.periodo,
            e.nombre AS nombre_estudiante,
            p.nombre AS nombre_profesor,
            CASE 
                WHEN a.oportunidad_dic = 'NP' THEN 'NP'
                WHEN a.oportunidad_mar IS NULL THEN a.oportunidad_dic
                ELSE (a.oportunidad_dic::FLOAT + a.oportunidad_mar) / 2
            END AS nota_final
        FROM 
            acta a
        JOIN 
            estudiantes e ON a.numero_de_alumno = e.numero_de_alumno
        JOIN 
            docentes_planificados p ON a.asignatura = p.curso;
    END;
    $$ LANGUAGE plpgsql;
    ";
    $db->exec($createProcedureQuery);
    echo "Stored procedure 'create_acta_view' creada correctamente.\n";

    // Ejecutar la stored procedure para crear la vista
    $db->exec("SELECT create_acta_view();");
    echo "Vista 'acta_notas' creada correctamente.\n";
} catch (Exception $e) {
    echo "Error al crear la stored procedure o la vista: " . $e->getMessage();
}

// Mostrar todos los datos insertados
try {
    $result = $db->query("SELECT * FROM acta_notas");
    if ($result !== false) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
    }
} catch (Exception $e) {
    die("Error: No se pudo obtener los datos de la vista 'acta_notas'. " . $e->getMessage());
}
?>