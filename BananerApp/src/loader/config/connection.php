<?php
  try {
    #Pide las variables para conectarse a la base de datos.
    require('data.php'); 
    # Se crea la instancia de PDO
    $db = new PDO(dsn: "pgsql:dbname=$databaseName;host=localhost;port=5432;user=$user;password=$password");
    echo "Se Conecto a la DATABASE\n";
  } catch (Exception $e) {
    echo "No se pudo conectar a la base de datos: $e\n";
  }
?>