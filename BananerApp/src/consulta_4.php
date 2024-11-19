<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>X</title>
    <link rel="stylesheet" href="styles/login.css">
</head>
<body>
    <h1>Consultas</h1>


    
    <form  method="POST" action="consultas/procesar_consulta.php">
        <label  for="columnas">Columnas A:</label>
        <input  type="text" id="columna" name="columna" required>
        

        <label  for="tabla">Tabla T:</label>
        <input  type="text" id="tabla" name="tabla" required>

        <label  for="condicion">Condicion C:</label>
        <input  type="text" id="condicion" name="condicion" required>
        
        <button class="form-button" type="submit">Ingresar</button>
    </form>
    </div>
</body>
</html>
