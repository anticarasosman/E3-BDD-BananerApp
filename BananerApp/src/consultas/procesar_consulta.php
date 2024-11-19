<?php
include('../loader/config/connection.php'); 

$columna= $_POST['columna'];
$tabla = $_POST['tabla'];
$condicion = $_POST['condicion'];



 if (empty($columna) || empty(($tabla))){
    die("ERROR tabla o coluimna vacia");
 }

 //EXISTENCIA DE TABLA
 try{


    $query = $db->prepare("SELECT tablename FROM pg_tables WHERE schemaname = 'public' AND tablename = :tabla");
    $query->bindParam(':tabla',$tabla,PDO:: PARAM_STR);
    $query->execute();

    if ($query-> rowCount() === 0){
        die("ERROR: la tabla no existe");

    }
 } catch (PDOException  $e){
    die("ERROR CON LA TABLA". $e->getMessage());
 }



 try{


    $query = $db->prepare("SELECT column_name FROM information_schema.columns  WHERE  table_name= :tabla");
    $query->bindParam(':tabla',$tabla,PDO:: PARAM_STR);
    $query->execute();
    $validos_c = $query->fetchAll(PDO::FETCH_COLUMN);

    $c_i = array_map("trim",explode(",",$columna));


    foreach($c_i as $columna_ingre){

        if (!in_array($columna_ingre,$validos_c)){
            die("ERROR COLUMNA '$columna_ingre' NO PERTENECE");
        }
    }
 } catch (PDOException  $e ){
    die("ERROR CON LA Columna". $e->getMessage());
 }







 $sql = "SELECT $columna FROM $tabla";
 $parametro = [];
 if (!empty($condicion)){
    $sql .= " WHERE ";
    $oper = '/^([\w]+)\s*(=|>=|<=|>|<|LIKE|IN|IS NULL|IS NOT NULL|EXISTS|NOT EXISTS|BETWEEN)\s*(.+)$/i';




    $condiciones = preg_split("/\s+(AND|OR)\s+/i",$condicion,-1,PREG_SPLIT_DELIM_CAPTURE);
    $cantidad_condicion = [];
    $operadores = [];



    foreach($condiciones as $condi){
         $condi = trim($condi);
         if (strtoupper($condi) === 'AND' || strtoupper($condi) === 'OR'){
            $operadores[] = strtoupper($condi);
         }elseif (preg_match($oper,$condi,$matches)){

            $colum_condic = $matches[1];
            $operador = strtoupper(trim($matches[2]));
            
            $valorr = trim($matches[3]);

            if (!in_array($colum_condic,$validos_c)){
                echo($validos_c);
                die("ERROR COLUMNAa '$colum_condic' No es valida");
            }


            if ($operador === "BETWEEN"){
                if (preg_match('/^.+\s+AND\s+.+$/i',$valorr,$bw)){
                    $b1 =   ':valor' . count($parametro);   
                    $b2 =   ':valor' . (count($parametro)+1);
                    $cantidad_condicion[] = "$colum_condic $operador $b1 AND $b2";
                    $parametro[$b1] = trim($bw[1]);
                    $parametro[$b2] = trim($bw[2]);
            }


            }elseif (in_array($operador,['IN','NOT IN'])){
                if (!preg_match('/^\(.+\)$/',$valorr)){
                    die("ERROR: operador debe estar entre parentesis");
                }
                $cantidad_condicion[] =  "$colum_condic $operador $valorr";


            }elseif (in_array($operador,['IS NULL',' IS NOT NULL'])) {
                $cantidad_condicion[] = "$colum_condic $operador ";
            }else{

                $pp = ':valor'. count($parametro);
                $cantidad_condicion[] =  "$colum_condic $operador $pp";
                $parametro[$pp] = $valorr;
            }


         }else{
            die("ERROR");
         }


    }
    foreach($cantidad_condicion as $i => $subi){
        if ($i > 0 ){
            $sql .= " ".array_shift($operadores) . " ";
        }
        $sql .= $subi;
    }


    

    

 }


try{
    $query = $db->prepare($sql);
    
    


    foreach($parametro as $clave => $valor){
        $query->bindParam($clave,$valor,PDO::PARAM_STR);
    }
        

    $query->execute();
    $resultados = $query->fetchAll(PDO::FETCH_ASSOC);
    echo "<h1>Resultados </h1>";

    if ($resultados){

        echo "<table border = '1'>";

        echo "<tr>";

        foreach(array_keys($resultados[0])as $column){

            echo "<th>$column</th>";
        }

        echo "<tr>";



        foreach($resultados as $file){
            echo "<tr>";
            

            foreach($file as $value){
                
                echo "<th>$value</th>";
            }
            echo "<tr>";
        }

        echo "</table>";
        



    }

}catch (PDOException  $e ){
    die("ERROR CON LA Columna". $e->getMessage());
 }

?>