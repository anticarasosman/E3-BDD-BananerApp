<?php
    include('config/connection.php');
    require("table_parameters.php");
    require("utils.php");
    $tabla_actual = "";

    // Llamar a la función para poblar la tabla Personas
    poblar_tabla_personas($db);

    try {
        // Cambiar el datestyle para la sesión actual
        $db->exec("SET datestyle = 'DMY';");

        echo "INSERTANDO DATOS\n";
        foreach($path_tablas as $tabla => $path) {
            $file = fopen($path, "r");

            if ($file) {
                $header = fgetcsv($file); // Nos saltamos la primera linea
                while (($data = fgetcsv($file, 5000, ";")) !== false){
                    // Verificamos las restricciones antes de insertar
                    if ($tabla != $tabla_actual) {
                        echo ("TABLA ACTUAL: ".$tabla . "\n");
                        $tabla_actual = $tabla;
                    }
                    if ($tabla == "Asignaturas") {
                        Corregir_tabla_Asignaturas($data);
                    }
                    if ($tabla == "Prerequisitos") {
                        Corregir_tabla_prerequisitos($data);
                    }
                    if ($tabla == "Planes") {
                        Corregir_tabla_Planes($data);
                    }
                    if ($tabla == "Estudiantes") {
                        Corregir_tabla_Estudiantes($data);
                    }
                    if ($tabla == "Notas") {
                        Corregir_tabla_Notas($data);
                    }
                    if ($tabla == "Docentes_Planificados") {
                        Corregir_tabla_Docentes_Planificados($data);
                    }
                    if ($tabla == "Planeacion") {
                        Corregir_tabla_Planeacion($data);
                    }
                    //Restricciones globales
                    for ($i = 0; $i < count($data); $i++) {
                        if ($data[$i] == "") {
                            $data[$i] = Null; //Convertimos los campos vacios en NULL para evitar insertar datos vacios
                        }
                    }
                    insertar_en_tabla($db, $tabla, $data);
                }
                fclose($file);
            } else {
                echo "Error al abrir el archivo $path\n";
            }
        }
        //crear_y_poblar_tabla_personas($db);
    } catch (Exception $e) {
        echo "Error al cargar datos: " . $e->getMessage();
    }

    function Corregir_tabla_prerequisitos($data){
        // La primera columna (Plan) debe tener el formato (2 Letras)(1 numero)
        if (!preg_match('/^[A-Za-z]{2}\d$/', $data[0])) {
            $data[0] = "X";
        }
        // La segunda columna (Asignatura) debe tener el formato (2 Letras)(Cualquier numero)
        if (!preg_match('/^[A-Za-z]{2}\d+$/', $data[1])) {
            $data[1] = "X";
        }
        // La cuarta columna (Nivel) debe tener el formato (1 numero)
        if (!preg_match('/^\d$/', $data[3])) {
            $data[3] = "X";
        }
    }

    function Corregir_tabla_Notas($data){
        // La primera columna (Codigo Plan) debe tener el formato (2 Letras)(1 numero)
        if (!preg_match('/^[A-Za-z]{2}\d$/', $data[0])) {
            $data[0] = "X";
        }
        // La tercera y undecima columna (Cohorte y Periodo Asignatura) debe tener el formato (4 Numeros)"-"(2 numeros)
        if (!preg_match('/^\d{4}-\d{2}$/', $data[2])) {
            $data[2] = "X";
        }
        if (!preg_match('/^\d{4}-\d{2}$/', $data[10])) {
            $data[10] = "X";
        }
        // La cuarta columna (Sede) debe ser "Hogwarts", "BEAUXBATON" o "UAGADOU"
        if (!in_array(strtoupper($data[3]), array("HOGWARTS", "BEAUXBATON", "UAGADOU"))) {
            $data[3] = "X";
        }
        // La quinta columna (RUT) debe tener el formato (7 o 8 numeros)
        if (!preg_match('/^\d{7,8}$/', $data[4])) {
            $data[4] = "X";
        }
        // La sexta columna (DV) debe tener el formato (1 numero o K)
        if (!preg_match('/^\d|K$/', $data[5])) {
            $data[5] = "X";
        }
        // La decima columna (N de alumno) debe tener el formato (Cualquier numero)
        if (!preg_match('/^\d+$/', $data[9])) {
            $data[9] = "X";
        }
        // La duodecima columna (Codigo Asignatura) debe tener el formato (2 Letras)(Cualquier numero)
        if (!preg_match('/^[A-Za-z]{2}\d+$/', $data[1])) {
            $data[1] = "X";
        }
        // La decimocuarta columna (Convocatoria) debe ser una abreviacion de mes
        if (!in_array(strtoupper($data[13]), array("ENE", "FEB", "MAR", "ABR","MAY", "JUN", "JUL","AGO", "SEP", "OCT", "NOV", "DIC"))) {
            $data[13] = "X";
        }
        // La decimoquinta columna (Calificacion) debe ser una calificacion valida
        if (!in_array(strtoupper($data[14]), array("SO", "MB", "B", "SU", "I", "M", "MM", "P", "NP", "EX", "A", "R", "NULO"))) {
            $data[14] = "X";
        }
        // La decimosexta columna (Nota) debe ser un numero float entre 1 y 7
        if (!preg_match('/^\d+(\.\d+)?$/', $data[15]) || $data[15] <= 1 || $data[15] >= 7) {
            $data[15] = "X";
        }
    }

    function Corregir_tabla_Planes($data){
        // La primera columna (Codigo Plan) debe tener el formato (2 Letras)(1 numero)
        if (!preg_match('/^[A-Za-z]{2}\d$/', $data[0])) {
            $data[0] = "X";
        }
        // La quinta columna (Jornada) debe ser "Diurno" o "Vespertino"
        if (!in_array(strtoupper($data[4]), array("DIURNO", "VESPERTINO"))) {
            $data[4] = "X";
        }
        // La sexta columna (Sede) debe ser "Hogwarts", "BEAUXBATON" o "UAGADOU"
        if (!in_array(strtoupper($data[5]), array("HOGWARTS", "BEAUXBATON", "UAGADOU"))) {
            $data[5] = "X";
        }
        // La septima columna (Grado) debe ser "PROGRAMA ESPECIAL DE LICENCIATURA" o "PREGRADO" o "POSTGRADO"
        if (!in_array(strtoupper($data[6]), array("PROGRAMA ESPECIAL DE LICENCIATURA", "PREGRADO", "POSTGRADO"))) {
            $data[6] = "X";
        }
        // La octava columna (Modalidad) debe ser "PRESENCIAL" o "ONLINE" o "HIBRIDA"
        if (!in_array(strtoupper($data[7]), array("PRESENCIAL", "ONLINE", "HIBRIDA"))) {
            $data[7] = "X";
        }
        // La novena columna (Inicio Vigencia) debe tener el formato (2 Numeros)"/"(2 numeros)"/"(2 numeros)
        if (!preg_match('/^\d{2}\/\d{2}\/\d{2}$/', $data[8])) {
            $data[8] = "X";
        }
    }

    function Corregir_tabla_Asignaturas($data){
        // La primera columna (Plan) debe tener el formato (2 Letras)(1 numero)
        if (!preg_match('/^[A-Za-z]{2}\d$/', $data[0])) {
            $data[0] = "X";
        }
        // La segunda columna (Asignatura ID) debe tener el formato (2 Letras)(Cualquier numero)
        if (!preg_match('/^[A-Za-z]{2}\d+$/', $data[1])) {
            $data[1] = "X";
        }
    }

    function Corregir_tabla_Estudiantes($data){
        // La primera columna (Codigo Plan) debe tener el formato (2 Letras)(1 numero)
        if (!preg_match('/^[A-Za-z]{2}\d$/', $data[0])) {
            $data[0] = "X";
        }
        // La tercera, decimocuarta y decimoquinta columna (Cohorte, Fecha logro, Ultima carga) deben tener el formato (4 Numeros)"-"(2 numeros)
        if (!preg_match('/^\d{4}-\d{2}$/', $data[2])) {
            $data[2] = "X";
        }
        if (!preg_match('/^\d{4}-\d{2}$/', $data[13])) {
            $data[13] = "X";
        }
        if (!preg_match('/^\d{4}-\d{2}$/', $data[14]) || $data[14] == null) {
            $data[14] = "X";
        }
        // La cuarta columna (Numero de Alumno) debe tener el formato (Cualquier numero)
        if (!preg_match('/^\d+$/', $data[3])) {
            $data[3] = "X";
        }
        // La quinta columna debe ser N o S
        if (!in_array(strtoupper($data[4]), array("N", "S"))) {
            $data[4] = "X";
        }
        // La septima columna (RUN) debe tener el formato (7 o 8 numeros)
        if (!preg_match('/^\d{7,8}$/', $data[6])) {
            $data[6] = "X";
        }
        // La octava columna (DV) debe tener el formato (1 numero o K)
        if (!preg_match('/^\d|K$/', $data[7])) {
            $data[7] = "X";
        }
    }

    function Corregir_tabla_Planeacion($data){
        //La primera columna (Periodo) debe tener el formato (4 Numeros)"-"(2 numeros)
        if (!preg_match('/^\d{4}-\d{2}$/', $data[0])) {
            $data[0] = "X";
        }
        // La segunda columna (Sede) debe ser "HOGWARTS", "BEAUXBATON" o "UAGADOU"
        if (!in_array(strtoupper($data[1]), array("HOGWARTS", "BEAUXBATON", "UAGADOU"))) {
            $data[1] = "X";
        }
        // La cuarta columna (Codigo Depto) debe tener el formato (5 numeros)
        if (!preg_match('/^\d{5}$/', $data[3])) {
            $data[3] = "X";
        }
        // La sexta columna (ID Asignatura) debe tener el formato (2 Letras)(Cualquier numero)
        if (!preg_match('/^[A-Za-z]{2}\d+$/', $data[5])) {
            $data[5] = "X";
        }
        // La octava columna (Seccion) debe ser un numero
        if (!preg_match('/^\d+$/', $data[7])) {
            $data[7] = "X";
        }
        //La decima columna (Jornada) debe ser "Diurno" o "Vespertino"
        if (!in_array(strtoupper($data[9]), array("DIURNO", "VESPERTINO"))) {
            $data[9] = "X";
        }
        // La decimoprimera columna (Cupos) debe ser un numero
        if (!preg_match('/^\d+$/', $data[10])) {
            $data[10] = "X";
        }
        // La decimosegunda columna (Inscritos) debe ser un numero
        if (!preg_match('/^\d+$/', $data[11])) {
            $data[11] = "X";
        }
        // La decimotercera columna (Dia) debe ser un dia de la semana
        $dia_normalizado = normalizar_tildes(strtoupper($data[12]));
        if (!in_array($dia_normalizado, array("LUNES", "MARTES", "MIERCOLES", "JUEVES", "VIERNES", "SABADO", "DOMINGO"))) {
            $data[12] = "X";
        }
        // La decimocuarta y decimoquinta columna (Hora Inicio y Final) debe tener el formato (2 numeros)":"(2 numeros)
        if (!preg_match('/^\d{2}:\d{2}$/', $data[13])) {
            $data[13] = "X";
        }
        if (!preg_match('/^\d{2}:\d{2}$/', $data[14])) {
            $data[14] = "X";
        }
        // La decimosexta y decimoséptima columna (Fecha Inicio y Final) debe tener el formato (2 Numeros)"/"(2 numeros)"/"(2 numeros)
        if (!preg_match('/^\d{2}\-\d{2}\-\d{4}$/', $data[15])) {
            $data[15] = "X";
        }
        if (!preg_match('/^\d{2}\-\d{2}\-\d{4}$/', $data[16])) {
            $data[16] = "X";
        }
        // La Vigésimaprimera columna (RUN) debe tener el formato (7 o 8 numeros)
        if (!preg_match('/^\d{7,8}$/', $data[20])) {
            $data[20] = "X";
        }
    }

    function Corregir_tabla_Docentes_Planificados($data){
        // La primera columna (RUN) debe tener el formato (7 o 8 numeros)
        if (!preg_match('/^\d{7,8}$/', $data[0])) {
            $data[0] = "X";
        }
        // La cuarta columna (Telefono) debe tener el formato (Cualquier numero)
        if (!preg_match('/^\d+$/', $data[3])) {
            $data[3] = "X";
        }
        // La sexta columna sigue el formato de (cualquier texto)("@lamejor.com")
        if (!preg_match('/^.+@lamejor.com$/', $data[5])) {
            $data[5] = "X";
        }
        // La octava columna (Dedicacion) debe ser un numero menor a 40
        if (!preg_match('/^\d+$/', $data[7]) || $data[7] >= 40) {
            $data[7] = "X";
        }
        // La novena columna (Contrato) debe ser o (”FULL TIME”, ”PART TIME” u ”HONORARIO”)
        if (!in_array(strtoupper($data[8]), array("FULL TIME", "PART TIME", "HONORARIO"))) {
            $data[8] = "X";
        }
        // La decima y undecima columna (Diurno y Vespertino) deben ser "Diurno" o "Vespertino" respectivamente o nulos
        if (strtoupper($data[9]) == "DIURNO" || $data[9] == null) {
            $data[9] = "X";
        }
        if (strtoupper($data[10]) == "VESPERTINO" || $data[10] == null) {
            $data[10] = "X";
        }
        // La duodecima columna (Sede) debe ser "Hogwarts", "BEAUXBATON" o "UAGADOU"
        if (!in_array(strtoupper($data[11]), array("HOGWARTS", "BEAUXBATON", "UAGADOU"))) {
            $data[11] = "X";
        }
        // La catorceava columna (Grado academico) debe ser  (”PROGRAMA ESPECIAL DE LICENCIATURA”, ”PREGRADO” o ”POSTGRADO”)
        if (!in_array(strtoupper($data[13]), array("PROGRAMA ESPECIAL DE LICENCIATURA", "PREGRADO", "POSTGRADO"))) {
            $data[13] = "X";
        }
        // La quinceava columna (Jerarquia) debe ser una jerarquia valida
        if (!producto_cruzado(array("ASISTENTE", "ASOCIADO", "ASOCIADA", "INSTRUCTOR", "INSTRUCTORA", "TITULAR"), array("DOCENTE", "REGULAR"), strtoupper($data[14])) || in_array(strtoupper($data[14]), array("SIN JERARQUIZAR", "COMISION SUPERIOR", "PROFESOR"))) {
            $data[14] = "X";
        }
    }

    function normalizar_tildes($cadena) {
        $originales = 'ÁÉÍÓÚáéíóú';
        $modificadas = 'AEIOUaeiou';
        return strtr($cadena, $originales, $modificadas);
    }

    function producto_cruzado($array1, $array2, $jerarquia) {
        foreach ($array1 as $elemento1) {
            foreach ($array2 as $elemento2) {
                $resultado = $elemento1 . " " . $elemento2;
                if ($resultado == $jerarquia) {
                    return True;
                }
            }
        }
        return False;
    }

    function poblar_tabla_personas($db) {
        try {
            echo "Poblando tabla Personas...\n";

            // Poblar desde la tabla Docentes
            $query = "INSERT INTO personas (RUN, DV, Nombres, Apellido_Paterno, Apellido_Materno, Nombre_Completo, Telefono, Correo_Personal, Correo_Institucional)
                      SELECT RUN, '', NOMBRE, APELLIDO_PATERNO, '', NOMBRE || ' ' || APELLIDO_PATERNO, TELEFONO, EMAIL_PERSONAL, EMAIL_INSTITUCIONAL
                      FROM docentes_planificados";
            $db->exec($query);

            // Poblar desde la tabla Estudiantes
            $query = "INSERT INTO personas (RUN, DV, Nombres, Apellido_Paterno, Apellido_Materno, Nombre_Completo, Telefono, Correo_Personal, Correo_Institucional)
                      SELECT RUN, DV, PRIMER_NOMBRE || ' ' || SEGUNDO_NOMBRE, PRIMER_APELLIDO, SEGUNDO_APELLIDO, PRIMER_NOMBRE || ' ' || SEGUNDO_NOMBRE || ' ' || PRIMER_APELLIDO || ' ' || SEGUNDO_APELLIDO, '', '', ''
                      FROM estudiantes";
            $db->exec($query);

            echo "Tabla Personas poblada con éxito.\n";
        } catch (Exception $e) {
            echo "Error al poblar la tabla Personas: " . $e->getMessage();
        }
    }
?>