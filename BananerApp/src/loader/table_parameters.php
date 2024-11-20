<?php
$path_tablas = array(
    'Asignaturas' => 'files/Asignaturas_unicas.csv',
    'Prerequisitos' => 'files/Prerrequisitos_unicos.csv',
    'Planes' => 'files/Planes_unicos.csv',
    'Estudiantes' => 'files/Estudiantes_unicos.csv',
    'Notas' => 'files/Notas.csv',
    'Planeacion' => 'files/Planeacion_unicos.csv',
    'usuarios' => 'files/Usuarios_prueba.csv',
);

$tablas_iniciales = array(
    "personas" => "RUN VARCHAR(10),
        DV CHAR(1),
        NOMBRES TEXT,
        APELLIDO_PATERNO TEXT,
        APELLIDO_MATERNO TEXT,
        NOMBRE_COMPLETO TEXT,
        TELEFONO TEXT,
        CORREO_PERSONAL TEXT,
        CORREO_INSTITUCIONAL TEXT,
        PRIMARY KEY (RUN)",

    'asignaturas' => "PLAN TEXT,
        ASIGNATURA_ID TEXT,
        ASIGNATURA TEXT,
        NIVEL INT,
        PRIMARY KEY (ASIGNATURA_ID)",

    'prerequisitos' => "PLAN TEXT,
        ASIGNATURA_ID TEXT,
        ASIGNATURA TEXT,
        NIVEL INT,
        UNO TEXT,
        DOS TEXT,
        PRIMARY KEY (PLAN),
        FOREIGN KEY (ASIGNATURA_ID) REFERENCES asignaturas (ASIGNATURA_ID)",

    'planes' => "CODIGO_PLAN TEXT,
        FACULTAD TEXT,
        CARRERA TEXT,
        PLAN TEXT,
        JORNADA TEXT,
        SEDE TEXT,
        GRADO TEXT,
        MODALIDAD TEXT,
        INICIO_VIGENCIA TEXT,
        PRIMARY KEY (CODIGO_PLAN)",

    'estudiantes' => "CODIGO_PLAN TEXT,
        CARRERA TEXT,
        COHORTE TEXT,
        NUMERO_DE_ALUMNO INT,
        BLOQUEO CHAR(1),
        CAUSAL_BLQUEO TEXT,
        RUN VARCHAR(10),
        DV CHAR(1),
        PRIMER_NOMBRE TEXT,
        SEGUNDO_NOMBRE TEXT,
        PRIMER_APELLIDO TEXT,
        SEGUNDO_APELLIDO TEXT,
        LOGRO TEXT,
        FECHA_LOGRO VARCHAR(7),
        ULTIMA_CARGA VARCHAR(7) NULL,
        PRIMARY KEY (NUMERO_DE_ALUMNO),
        FOREIGN KEY (CODIGO_PLAN) REFERENCES planes (CODIGO_PLAN),
        FOREIGN KEY (RUN) REFERENCES personas (RUN)",
        
    'notas' => "CODIGO_PLAN TEXT,
        PLAN TEXT,
        COHORTE TEXT,
        SEDE TEXT,
        RUN VARCHAR(10),
        DV CHAR(1),
        NOMBRES TEXT,
        APELLIDO_PATERNO TEXT,
        APELLIDO_MATERNO TEXT,
        NUMERO_DE_ALUMNO INT,
        PERIODO_ASIGNATURA TEXT,
        CODIGO_ASIGNATURA TEXT,
        ASIGNATURA TEXT,
        CONVOCATORIA TEXT,
        CALIFICACION TEXT,
        NOTA TEXT,
        PRIMARY KEY (NUMERO_DE_ALUMNO, PERIODO_ASIGNATURA, CODIGO_ASIGNATURA),
        FOREIGN KEY (CODIGO_PLAN) REFERENCES planes (CODIGO_PLAN),
        FOREIGN KEY (NUMERO_DE_ALUMNO) REFERENCES estudiantes (NUMERO_DE_ALUMNO)",

    'planeacion' => "PERIODO VARCHAR(7),
        SEDE TEXT,
        FACULTAD TEXT,
        CODIGO_DEPARTAMENTO CHAR(5),
        DEPARTAMENTO TEXT,
        ID_ASIGNATURA TEXT,
        ASIGNATURA TEXT,
        SECCION INT,
        DURACION CHAR(1),
        JORNADA TEXT,
        CUPOS INT,
        INSCRITOS INT,
        DIA TEXT,
        HORA_INICIO TIME,
        HORA_FINAL TIME,
        FECHA_INICIO TEXT,
        FECHA_FIN TEXT,
        LUGAR TEXT NULL,
        EDIFICIO TEXT,
        PROFESOR_PRINCIPAL TEXT,
        RUN VARCHAR(10),
        NOMBRE_DOCENTE TEXT,
        APELLIDO_DOCENTE TEXT,
        SEGUNDO_APELLIDO_DOCENTE TEXT,
        JERARQUIZACION TEXT,
        PRIMARY KEY (ID_ASIGNATURA, HORA_INICIO),
        FOREIGN KEY (ID_ASIGNATURA) REFERENCES asignaturas (ASIGNATURA_ID)",


    'usuarios' => 
    'email VARCHAR(255) UNIQUE NOT NULL, 
    password VARCHAR(255) NOT NULL, 
    role VARCHAR(50) NOT NULL',
);
?>