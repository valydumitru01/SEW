<?php
session_start();
class BaseDatos
{
    private $servername = "localhost";
    private $username = "DBUSER2021";
    private $password = "DBPSWD2021";
    private $conn;
    private $pst;


    public function __construct()
    {
        $this->datosInsertar["dni"] = "";
        $this->datosInsertar["nombre"] = "";
        $this->datosInsertar["apellidos"] = "";
        $this->datosInsertar["email"] = "";
        $this->datosInsertar["telefono"] = "";
        $this->datosInsertar["edad"] = "";
        $this->datosInsertar["sexo"] = "";
        $this->datosInsertar["nivel"] = "";
        $this->datosInsertar["tiempo"] = "";
        $this->datosInsertar["correcto"] = "";
        $this->datosInsertar["comentarios"] = "";
        $this->datosInsertar["propuestas"] = "";
        $this->datosInsertar["valoracion"] = "";

        $this->datosModificar["dni"] = "";
        $this->datosModificar["nombre"] = "";
        $this->datosModificar["apellidos"] = "";
        $this->datosModificar["email"] = "";
        $this->datosModificar["telefono"] = "";
        $this->datosModificar["edad"] = "";
        $this->datosModificar["sexo"] = "";
        $this->datosModificar["nivel"] = "";
        $this->datosModificar["tiempo"] = "";
        $this->datosModificar["correcto"] = "";
        $this->datosModificar["comentarios"] = "";
        $this->datosModificar["propuestas"] = "";
        $this->datosModificar["valoracion"] = "";
    }
    public function connect()
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        if (!mysqli_select_db($this->conn, "myDB")) {
            die("Cant select database");
        }
    }
    public function disconnect()
    {
        if ($this->pst != null) {
            $this->pst->close();
            $this->pst = null;
        }
        if($this->conn!=null){
            $this->conn->close();
            $this->conn=null;
        }
        
    }
    public function crearBaseDatos()
    {
        $this->connect();
        $createDb = "CREATE DATABASE myDB";
        if ($this->conn->query($createDb) === FALSE) {
            //echo "Error creating database: " . mysqli_error($this->conn);
        }
        $this->disconnect();
    }
    public function crearTabla()
    {
        $this->connect();
        $createTable = "CREATE TABLE PruebasUsabilidad (
            dni VARCHAR(9) PRIMARY KEY,
            nombre VARCHAR(30) NOT NULL,
            apellidos VARCHAR(30) NOT NULL,
            email VARCHAR(50),
            telefono int(9),
            edad int(3) NOT NULL,
            sexo VARCHAR(30) NOT NULL,
            nivel int(2) NOT NULL,
            tiempo VARCHAR(30) NOT NULL,
            correcto tinyint(1) NOT NULL,
            comentarios VARCHAR(1000) NOT NULL,
            propuestas VARCHAR(1000) NOT NULL,
            valoracion int(2) NOT NULL
            )";
        if ($this->conn->query($createTable) === FALSE) {
            //echo "Error creating table: " . $this->conn->error;
        }
        $this->disconnect();
    }
    /**
     * @param array string $datos
     */
    public function insertarEnTabla($datos)
    {
        $this->connect();
        $insert = "INSERT INTO PruebasUsabilidad 
        (dni, nombre, apellidos,email,telefono,edad,sexo,nivel,
        tiempo,correcto,comentarios,propuestas,
        valoracion)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $this->pst = $this->conn->prepare($insert);
        if ($this->pst === FALSE) {
            echo "<br>ERROR: " . $insert . " <br>" . $this->conn->error . "<br>";
        }
        try {
            $this->pst->bind_param(
                "ssssiisisissi",
                $datos["dni"],
                $datos["nombre"],
                $datos["apellidos"],
                $datos["email"],
                $datos["telefono"],
                $datos["edad"],
                $datos["sexo"],
                $datos["nivel"],
                $datos["tiempo"],
                $datos["correcto"],
                $datos["comentarios"],
                $datos["propuestas"],
                $datos["valoracion"]
            );
        } catch (Exception $err) {

            //echo "ERROR: " . $err;
        }
        $this->datosInsertarYaExisten = false;
        if ($this->pst->execute() === FALSE) {
            //echo "Error: " . $insert . "<p>" . $this->conn->error . "</p>";
            if (explode(" ", $this->conn->error)[0] == "Duplicate") {
                $this->datosInsertarYaExisten = true;
            }
        }
        $this->disconnect();
    }
    public $datosInforme;
    public function generarInforme()
    {


        $this->connect();

        $select = "SELECT nombre, apellidos,email,telefono,edad,sexo,nivel,
        tiempo,correcto,comentarios,propuestas,
        valoracion 
        FROM PruebasUsabilidad";

        $this->pst = $this->conn->prepare($select);
        $edades = [];
        $sexos = [];
        $niveles = [];
        $tiempos = [];
        $correctos = [];
        $valoraciones = [];


        if ($this->pst->execute() === FALSE) {
            //echo "Error: " . $select . "<p>" . $this->conn->error . "</p>";
        } else {
            $nombre = "";
            $apellidos = "";
            $email = "";
            $telefono = 0;
            $edad = 0;
            $sexo = "";
            $nivel = -1;
            $tiempo = "";
            $correcto = -1;
            $comentarios = "";
            $propuestas = "";
            $valoracion = -1;
            $this->pst->bind_result(
                $nombre,
                $apellidos,
                $email,
                $telefono,
                $edad,
                $sexo,
                $nivel,
                $tiempo,
                $correcto,
                $comentarios,
                $propuestas,
                $valoracion
            );
            while ($this->pst->fetch()) {
                array_push($edades, $edad);
                array_push($sexos, $sexo);
                array_push($niveles, $nivel);
                array_push($tiempos, $tiempo);
                array_push($correctos, $correcto);
                array_push($valoraciones, $valoracion);
            }

            $datosParaMostrar = "\nINFORME DE LOS DATOS: ";
            $edadMedia = "\n\t- Edad media de los usuarios: " . $this->media($edades);
            $frecuenciaSexoMasculino = "\n\t- Frecuencia sexo masculino: " . $this->frecuencia($sexos, "Hombre");
            $frecuenciaSexoFemenino = "\n\t- Frecuencia sexo femenino: " . $this->frecuencia($sexos, "Mujer");
            $valorMedioNiveles = "\n\t- Valor medio del nivel o pericia informatica: " . $this->media($niveles);
            $valorMedioTiempos = "\n\t- Valor medio del tiempo de tarea: " . $this->mediaTiempo($tiempos);
            $porcentajeTareasCorrectas = "\n\t- Porcentaje de usuarios que han completado la tarea correctamente: " . $this->frecuencia($correctos, 1);
            $valorPuntuacionMedia = "\n\t- Valor medio de la puntuacion de los usuarios sobre la aplicacion: " . $this->media($valoraciones);

            $datosParaMostrar .= $edadMedia . $frecuenciaSexoMasculino . $frecuenciaSexoFemenino
                . $valorMedioNiveles . $valorMedioTiempos . $porcentajeTareasCorrectas . $valorPuntuacionMedia;
            $this->datosInforme = $datosParaMostrar;
        }
        $this->disconnect();
    }

    public function frecuencia($arr, $valor)
    {
        $nums = 0;
        foreach ($arr as $value) {
            if ($value === $valor)
                $nums++;
        }
        return ($nums * 100) / sizeof($arr);
    }
    public function mediaTiempo($arr)
    {
        return date('H:i:s', array_sum(array_map('strtotime', $arr)) / count($arr));
    }
    public function media($arr)
    {
        $total = 0;
        $nums = 0;
        foreach ($arr as $value) {
            $total += $value;
            $nums++;
        }
        return $total / $nums;
    }
    public $datosParaMostrar = "";
    public function buscarDatos($identificador)
    {
        $identificadorParam = (string)$identificador;
        $this->datosParaMostrar = "";
        $this->connect();
        $select = "SELECT nombre, apellidos,email,telefono,edad,sexo,nivel,
        tiempo,correcto,comentarios,propuestas,
        valoracion 
        FROM PruebasUsabilidad
        WHERE dni=?";
        $this->datosBuscarNoExisten = false;
        $this->pst = $this->conn->prepare($select);

        $this->pst->bind_param("s", $identificadorParam);

        if ($this->pst->execute() === FALSE) {
            //echo "Error: " . $select . "<p>" . $this->conn->error . "</p>";
        } else {
            $nombre = "";
            $apellidos = "";
            $email = "";
            $telefono = 0;
            $edad = 0;
            $sexo = "";
            $nivel = -1;
            $tiempo = "";
            $correcto = -1;
            $comentarios = "";
            $propuestas = "";
            $valoracion = -1;
            $this->pst->bind_result(
                $nombre,
                $apellidos,
                $email,
                $telefono,
                $edad,
                $sexo,
                $nivel,
                $tiempo,
                $correcto,
                $comentarios,
                $propuestas,
                $valoracion
            );
            while ($this->pst->fetch()) {
                if ($correcto === 1) {
                    $correcto = "Si";
                } else {
                    $correcto = "No";
                }
                $this->datosParaMostrar = "dni: " . $identificador
                    . "\n - Nombre: " . $nombre
                    . "\n - Apellidos: " . $apellidos
                    . "\n - Email:" . $email
                    . "\n - Telefono: " . $telefono
                    . "\n - Edad: " . $edad
                    . "\n - Sexo: " . $sexo
                    . "\n - Nivel de informatica: " . $nivel
                    . "\n - Segundos de prueba: " . $tiempo
                    . "\n - Se realizo la prueba correctamente?: " . $correcto
                    . "\n - Comentarios: " . $comentarios
                    . "\n - Propuestas de mejora: " . $propuestas
                    . "\n - Valoracion de la prueba segun el usuario: " . $valoracion;
            }


            if ($this->datosParaMostrar == "")
                $this->datosBuscarNoExisten = true;
            else $this->datosBuscarNoExisten = false;
        }
        $this->disconnect();
    }
    public function comprobarDatosBuscar()
    {
    }
    public function modificarDatos($datos)
    {
        $this->connect();
        $update = "UPDATE PruebasUsabilidad SET nombre = ?, apellidos=?,email=?,telefono=?,edad=?,sexo=?,nivel=?,
        tiempo=?,correcto=?,comentarios=?,propuestas=?,valoracion=?
        WHERE dni=?";
        $this->pst = $this->conn->prepare($update);
        $this->pst->bind_param(
            "sssiisisissis",
            $datos["nombre"],
            $datos["apellidos"],
            $datos["email"],
            $datos["telefono"],
            $datos["edad"],
            $datos["sexo"],
            $datos["nivel"],
            $datos["tiempo"],
            $datos["correcto"],
            $datos["comentarios"],
            $datos["propuestas"],
            $datos["valoracion"],
            $datos["dni"]
        );


        if ($this->pst->execute() === FALSE) {
            //echo "Error updating record: " . $this->conn->error;
        }
        if ($this->conn->affected_rows !== 0) {
            $this->datosModificarNoExisten = false;
        } else {
            $this->datosModificarNoExisten = true;
        }
        $this->disconnect();
    }
    public function eliminarDatos($identificador)
    {
        $this->connect();
        $delete = "DELETE FROM PruebasUsabilidad WHERE dni=?";
        $this->pst = $this->conn->prepare($delete);
        if ($this->pst === FALSE) {
            //echo "Error prepare borrar: " . $this->conn->error;
        }
        if ($this->pst->bind_param("s", $identificador) === FALSE) {
            //echo "Error bind param borrar: " . $this->conn->error;
        }
        if ($this->pst->execute() === FALSE) {
            //echo "Error updating record: " . $this->conn->error;
        }
        if ($this->conn->affected_rows !== 0) {
            $this->datosBorrarNoExisten = false;
        } else {
            $this->datosBorrarNoExisten = true;
        }

        $this->disconnect();
    }

    public function cargarCSV()
    {
        if($_FILES){
            print_r($_FILES);
            $this->connect();
            $myfile = fopen($_FILES['archivoImport']['tmp_name'],"r") or die("Unable to open file!");
            print_r($_FILES);
            while (!feof($myfile)) {
                $datos = explode(",", fgets($myfile));
                if(isset($datos[0])&&isset($datos[1])
                &&isset($datos[2])&&isset($datos[3])
                &&isset($datos[4])&&isset($datos[5])
                &&isset($datos[6])&&isset($datos[7])
                &&isset($datos[8])&&isset($datos[9])
                &&isset($datos[10])&&isset($datos[11])
                &&isset($datos[12])){
                    if ($datos[0] != "dni") {
                        $datos["dni"] = $datos[0];
                        $datos["nombre"] = $datos[1];
                        $datos["apellidos"] = $datos[2];
                        $datos["email"] = $datos[3];
                        $datos["telefono"] = $datos[4];
                        $datos["edad"] = $datos[5];
                        $datos["sexo"] = $datos[6];
                        $datos["nivel"] = $datos[7];
                        $datos["tiempo"] = $datos[8];
                        $datos["correcto"] = $datos[9];
                        $datos["comentarios"] = $datos[0];
                        $datos["propuestas"] = $datos[11];
                        $datos["valoracion"] = $datos[12];
                        
                        $this->insertarEnTabla($datos);
                    }
                }
            }
            fclose($myfile);
            $this->disconnect();
        }
    }
    public function exportarACSV()
    {
        $this->connect();
        $select = "SELECT *
        FROM PruebasUsabilidad";
        $writtenText = "";
        $filename="export.csv";
        $myfile = fopen($filename,"w") or die("Unable to open file!");
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');
        $result = $this->conn->query($select);
        if ($result === FALSE) {
            //echo "Error creating table: " . $this->conn->error;
        } else {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    /*$writtenText .= $row["dni"]
                        . "," . $row["nombre"]
                        . "," . $row["apellidos"]
                        . "," . $row["email"]
                        . "," . $row["telefono"]
                        . "," . $row["edad"]
                        . "," . $row["sexo"]
                        . "," . $row["nivel"]
                        . "," . $row["tiempo"]
                        . "," . $row["correcto"]
                        . "," . $row["comentarios"]
                        . "," . $row["propuestas"]
                        . "," . $row["valoracion"]."\n";*/
                    fputcsv($myfile, $row, ",");
                }
                
            }
        }
        
        $myfileExport=fopen($filename,"r");

        header('Content-Type: application/csv; charset=UTF-8');

        header('Content-Disposition: attachment;filename="'.$filename.'";');

        fpassthru($myfileExport);
        fclose($myfile);
        fclose($myfileExport);
        $this->disconnect();
        exit;
    }

    public $datosModificar;
    public $dniBuscar;
    public $dniBorrar;
    public $datosInsertar;

    public $datosInsertarCompletos = true;
    public $datosBuscarCompletos = true;
    public $datosModificarCompletos = true;
    public $datosBorrarCompletos = true;

    public $datosInsertarCorrectos = true;
    public $datosBuscarCorrectos = true;
    public $datosModificarCorrectos = true;
    public $datosBorrarCorrectos = true;

    public $datosInsertarYaExisten;
    public $datosModificarNoExisten;
    public $datosBorrarNoExisten;

    public $datosBuscarNoExisten;
    public function comprobarDatos()
    {
        //---------------------INSERTAR-------------------------
        if (isset($this->datosInsertar['correcto'])) {
            $this->datosInsertar['correcto'] = 1;
        } else $this->datosInsertar['correcto'] = 0;



        //echo "<br>DATOS INSERTAR<br>";
        foreach ($this->datosInsertar as $key => $value) {
            //echo "&emsp;&emsp;dato de tabla: ";
            //echo  $key . ": " . $value . "<br>";
            if (strlen($value) == 0) {
                $this->datosInsertarCompletos = false;
            } else {
                $this->datosInsertarCompletos = true;
            }
        }

        if ($this->datosInsertarCompletos) {
            if (
                strlen($this->datosInsertar["dni"]) < 9 ||
                strlen($this->datosInsertar["telefono"]) < 9 ||
                $this->datosInsertar["edad"] < 18
            ) {
                $this->datosInsertarCorrectos = false;
            } else {
                $this->datosInsertarCorrectos = true;
            }
        }
        //------------------------------------------------------------

        //--------------------------MODIFICAR-------------------------
        if (isset($this->datosModificar['correcto'])) {
            $this->datosModificar['correcto'] = 1;
        } else $this->datosModificar['correcto'] = 0;



        //echo "<br>DATOS MODIFICAR<br>";
        foreach ($this->datosModificar as $key => $value) {
            //echo "&emsp;&emsp;dato de tabla: ";
            //echo  $key . ": " . $value . "<br>";
            if (strlen($value) == 0) {
                $this->datosModificarCompletos = false;
            } else {
                $this->datosModificarCompletos = true;
            }
        }
        if ($this->datosModificarCompletos) {
            if (
                strlen($this->datosModificar["dni"]) != 9 ||
                strlen($this->datosModificar["telefono"]) != 9 ||
                $this->datosModificar["edad"] < 18
            ) {
                $this->datosModificarCorrectos = false;
            } else {
                $this->datosModificarCorrectos = true;
            }
        }
        //---------------------------------------------------------------


        //-----------------BUSCAR-----------------
        if (strlen($this->dniBuscar) == 0)
            $this->datosBuscarCompletos = false;
        else $this->datosBuscarCompletos = true;
        if (strlen($this->dniBuscar) != 9)
            $this->datosBuscarCorrectos = false;
        else $this->datosBuscarCorrectos = true;
        //-----------------------------------------

        //------------------ELIMINAR---------------------------------------
        if (strlen($this->dniBorrar) != 9) {
            $this->datosBorrarCorrectos = false;
        } else $this->datosBorrarCorrectos = true;

        if (strlen($this->dniBorrar) == 0)
            $this->datosBorrarCompletos = false;
        else $this->datosBorrarCompletos = true;
        //-----------------------------------------------------------------
    }
    public function botonPulsado($boton)
    {
        $this->comprobarDatos();
        switch ($boton) {
            case "Crear base de datos":
                $this->crearBaseDatos();
                break;
            case "Crear tabla":
                $this->crearTabla();
                break;
            case "Insertar":
                if ($this->datosInsertarCompletos)
                    $this->insertarEnTabla($this->datosInsertar);
                break;
            case "Buscar datos":
                $this->buscarDatos($this->dniBuscar);
                break;
            case "Modificar datos":
                if ($this->datosModificarCompletos)
                    $this->modificarDatos($this->datosModificar);
                break;
            case "Eliminar datos":
                $this->eliminarDatos($this->dniBorrar);
                break;
            case "Generar informe":
                $this->generarInforme();
                break;
            case "Cargar datos":
                $this->cargarCSV();
                break;
            case "Exportar datos":
                $this->exportarACSV();
                break;
            default:
                break;
        }
    }

    public function setDatosInsertar($datosInsertar)
    {
        $this->datosInsertar = $datosInsertar;
    }
    public function setDniBuscar($dni)
    {
        $this->dniBuscar = $dni;
    }
    public function getDatosInsertar()
    {
        return $this->datosInsertar;
    }

    public function setDatosModificar($datosModificar)
    {
        $this->datosModificar = $datosModificar;
    }
    public function getDatosModificar()
    {
        return $this->datosModificar;
    }

    public function setdniBorrar($dniBorrar)
    {
        $this->dniBorrar = $dniBorrar;
    }
}
if (isset($_SESSION['Ejercicio6'])) {
    $basedatos = $_SESSION['Ejercicio6'];
} else {
    $basedatos = new BaseDatos();
    $_SESSION['Ejercicio6'] = $basedatos;
}

if (count($_POST) > 0) {
    $boton = $_POST['boton'];
    $datosParaInsertar = $_POST['insertar'];

    $dniParaBorrar = $_POST['eliminar']["dni"];

    $dniBuscar = $_POST['buscar']["dni"];
    $datosParaModificar = $_POST['modificar'];


    $basedatos->setDatosInsertar($datosParaInsertar);
    $basedatos->setDatosModificar($datosParaModificar);
    $basedatos->setDniBuscar($dniBuscar);
    $basedatos->setdniBorrar($dniParaBorrar);


    $basedatos->botonPulsado($boton);
}




?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Ejercicio 6">
    <meta name="keywords" content="Base de datos, tablas, ejercicio 6, valentin">
    <meta name="author" content="Valentin">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="Ejercicio6.css">
    <title>Ejercicio6: Base de datos</title>
</head>

<body>
    <header>
        <h1>
            Base de datos: Pruebas Usabilidad
        </h1>
    </header>
    <main>
        <form action="#" method="post" name="Ejercicio6Form" enctype='multipart/form-data'>
            <ul>
                <li>
                    <label for="crearBaseDatos">Crear base de datos</label>
                    <input type="submit" name="boton" value="Crear base de datos" id="crearBaseDatos">
                </li>




                <li>
                    <label for="crearTabla">Crear tabla</label>
                    <input type="submit" name="boton" value="Crear tabla" id="crearTabla">
                </li>




                <li>
                    <label for="insertarEnTabla">Insertar datos en una tabla</label>
                    <input type="submit" name="boton" value="Insertar" id="insertarEnTabla">

                    <fieldset>
                        <!-- INSERTAR _______________________________________________________________________________-->
                        <legend>Datos que se quieren insertar: </legend>

                        <fieldset>
                            <label for="dni">dni: </label>
                            <input type="text" name="insertar[dni]" value="<?php echo $basedatos->getDatosInsertar()["dni"] ?>" id="dni">

                            <label for="nombre">Nombre: </label>
                            <input type="text" name="insertar[nombre]" value="<?php echo $basedatos->getDatosInsertar()["nombre"] ?>" id="nombre">

                            <label for="apellidos">Apellidos: </label>
                            <input type="text" name="insertar[apellidos]" value="<?php echo $basedatos->getDatosInsertar()["apellidos"] ?>" id="apellidos">

                        </fieldset>
                        <fieldset>
                            <label for="email">Email: </label>
                            <input type="text" name="insertar[email]" value="<?php echo $basedatos->getDatosInsertar()["email"] ?>" id="email">

                            <label for="telefono">Telefono: </label>
                            <input type="tel" name="insertar[telefono]" value="<?php echo $basedatos->getDatosInsertar()["telefono"] ?>" id="telefono" maxlength="9">

                            <label for="edad">Edad: </label>
                            <input type="text" name="insertar[edad]" value="<?php echo $basedatos->getDatosInsertar()["edad"] ?>" id="edad">
                        </fieldset>
                        <fieldset>

                            <label for="sexo">sexo</label>
                            <select id="sexo" name="insertar[sexo]">
                                <option value="Mujer" value="<?php if ($basedatos->getDatosInsertar()["sexo"] == "Mujer") echo "selected"; ?>">Mujer</option>
                                <option value="Hombre" value="<?php if ($basedatos->getDatosInsertar()["sexo"] == "Hombre") echo "selected"; ?>">Hombre</option>
                                <option value="no contestar" value="<?php if ($basedatos->getDatosInsertar()["sexo"] == "no contestar") echo "selected"; ?>">Prefiero no contestar</option>
                            </select>

                            <label for="nivelInformatica">Nivel de informatica: </label>
                            <input type="number" name="insertar[nivel]" value="<?php echo $basedatos->getDatosInsertar()["nivel"] ?>" min="0" max="10" id="nivelInformatica">

                            <label for="tiempoRealizacion">Tiempo que ha tardado: </label>
                            <input type="time" min="00:10:00" max="02:00:00" step="1" name="insertar[tiempo]" value="<?php echo $basedatos->getDatosInsertar()["tiempo"] ?>" id="tiempoRealizacion">

                            <label for="seRealizoCorrectamente">Se realizo correctamente: </label>
                            <input type="checkbox" name="insertar[correcto]" value="<?php echo $basedatos->getDatosInsertar()["correcto"] ?>" id="seRealizoCorrectamente">

                        </fieldset>
                        <fieldset>

                            <label for="comentarios">Comentarios: </label>
                            <textarea rows="5" cols="50" name="insertar[comentarios]" id="comentarios"><?php echo $basedatos->getDatosInsertar()["comentarios"] ?></textarea>

                        </fieldset>
                        <fieldset>

                            <label for="propuestas">Propuestas: </label>
                            <textarea rows="5" cols="50" name="insertar[propuestas]" id="propuestas"><?php echo $basedatos->getDatosInsertar()["propuestas"] ?></textarea>

                        </fieldset>
                        <fieldset>

                            <label for="valoracion">Valoracion: </label>
                            <input type="number" name="insertar[valoracion]" value="<?php echo $basedatos->getDatosInsertar()["valoracion"] ?>" min="0" max="10" id="valoracion">

                        </fieldset>
                    </fieldset><!-- FIN INSERTAR_______________________________________________________________________________-->
                </li>


                
                <li>
                    <label for="modificarEnTabla">Modificar datos en una tabla</label>
                    <input type="submit" name="boton" value="Modificar datos" id="modificarEnTabla">
                    <p>Datos a modificar: </p>
                    <fieldset>
                        <!-- MODIFICAR _______________________________________________________________________________-->
                        <legend>Datos que se quieren modificar (el usuario modificado se indica por el dni): </legend>
                        
                        <fieldset>
                            <label for="dniMod">dni: </label>
                            <input type="text" name="modificar[dni]" value="<?php echo $basedatos->getDatosModificar()["dni"] ?>" id="dniMod">

                            <label for="nombreMod">Nombre: </label>
                            <input type="text" name="modificar[nombre]" value="<?php echo $basedatos->getDatosModificar()["nombre"] ?>" id="nombreMod">

                            <label for="apellidosMod">Apellidos: </label>
                            <input type="text" name="modificar[apellidos]" value="<?php echo $basedatos->getDatosModificar()["apellidos"] ?>" id="apellidosMod">

                        </fieldset>
                        <fieldset>
                            <label for="emailMod">Email: </label>
                            <input type="text" name="modificar[email]" value="<?php echo $basedatos->getDatosModificar()["email"] ?>" id="emailMod">

                            <label for="telefonoMod">Telefono: </label>
                            <input type="tel" name="modificar[telefono]" value="<?php echo $basedatos->getDatosModificar()["telefono"] ?>" id="telefonoMod" maxlength="9">

                            <label for="edadMod">Edad: </label>
                            <input type="text" name="modificar[edad]" value="<?php echo $basedatos->getDatosModificar()["edad"] ?>" id="edadMod">
                        </fieldset>
                        <fieldset>

                            <label for="sexoMod">sexo</label>
                            <select id="sexoMod" name="modificar[sexo]">
                                <option value="Mujer" value="<?php if ($basedatos->getDatosModificar()["sexo"] == "Mujer") echo "selected"; ?>">Mujer</option>
                                <option value="Hombre" value="<?php if ($basedatos->getDatosModificar()["sexo"] == "Hombre") echo "selected"; ?>">Hombre</option>
                                <option value="no contestar" value="<?php if ($basedatos->getDatosModificar()["sexo"] == "no contestar") echo "selected"; ?>">Prefiero no contestar</option>
                            </select>

                            <label for="nivelInformaticaMod">Nivel de informatica: </label>
                            <input type="number" name="modificar[nivel]" value="<?php echo $basedatos->getDatosModificar()["nivel"] ?>" min="0" max="10" id="nivelInformaticaMod">

                            <label for="tiempoRealizacionMod">Tiempo que ha tardado: </label>
                            <input type="time" min="00:10:00" max="02:00:00" step="1" name="modificar[tiempo]" value="<?php echo $basedatos->getDatosModificar()["tiempo"] ?>" id="tiempoRealizacionMod">

                            <label for="seRealizoCorrectamenteMod">Se realizo correctamente: </label>
                            <input type="checkbox" name="modificar[correcto]" value="<?php echo $basedatos->getDatosModificar()["correcto"] ?>" id="seRealizoCorrectamenteMod">

                        </fieldset>
                        <fieldset>

                            <label for="comentariosMod">Comentarios: </label>
                            <textarea rows="5" cols="50" name="modificar[comentarios]" id="comentariosMod"><?php echo $basedatos->getDatosModificar()["comentarios"] ?></textarea>

                        </fieldset>
                        <fieldset>

                            <label for="propuestasMod">Propuestas: </label>
                            <textarea rows="5" cols="50" name="modificar[propuestas]" id="propuestasMod"><?php echo $basedatos->getDatosModificar()["propuestas"] ?></textarea>

                        </fieldset>
                        <fieldset>

                            <label for="valoracionMod">Valoracion: </label>
                            <input type="number" name="modificar[valoracion]" value="<?php echo $basedatos->getDatosModificar()["valoracion"] ?>" min="0" max="10" id="valoracionMod">

                        </fieldset>
                    </fieldset><!-- FIN MODIFICAR _______________________________________________________________________________-->
                </li>

                <li>
                    <!-- BUSCAR _______________________________________________________________________________-->
                    <label for="buscarEnTabla">Buscar datos en una tabla</label>
                    <input type="submit" name="boton" value="Buscar datos" id="buscarEnTabla">
                    <fieldset>
                       
                        <label for="buscar">dni: </label><input type="text" name="buscar[dni]" value="<?php echo $basedatos->dniBuscar ?>" id="buscar">
                    </fieldset>
                    <fieldset>
                        <label for="datosMostradosBuscar">Datos Mostrados:</label>
                        <textarea rows="5" cols="50" id="datosMostradosBuscar"><?php echo $basedatos->datosParaMostrar ?></textarea>
                    </fieldset>
                    <!--FIN BUSCAR _______________________________________________________________________________-->
                </li>












                <li>
                    <!-- ELIMINAR _______________________________________________________________________________ -->
                    <label for="eliminarEnTabla">Eliminar datos en tabla</label>
                
                    <input type="submit" name="boton" value="Eliminar datos" id="eliminarEnTabla">
                    <fieldset>
                        <label for="eliminar">dni: </label><input type="text" name="eliminar[dni]" value="<?php echo $basedatos->dniBorrar ?>" id="eliminar">
                    </fieldset>
                </li>
                <!--FIN ELIMINAR _______________________________________________________________________________ -->




                <li>
                    <!-- GENERAR INFORME _______________________________________________________________________________-->
                    <label for="generarInforme">Generar informe</label>
                    <input type="submit" name="boton" value="Generar informe" id="generarInforme">
                    <fieldset>
                        <label for="datosMostradosInforme">Informe:</label>
                        <textarea rows="5" cols="50" id="datosMostradosInforme"><?php echo $basedatos->datosInforme ?></textarea>
                    </fieldset>
                </li><!-- FIN GENERAR INFORME _______________________________________________________________________________-->





                <li><!-- CARGAR CSV _______________________________________________________________________________ -->
                    <label for="elegirfichero" >Elige tu fichero: </label>
                    <input type="file" name="archivoImport" accept=".csv" id="elegirfichero">
                    <label for="cargarDatos">Cargar datos desde un archivo CSV en una tabla de la Base de Datos.</label>
                    <input type="submit" name="boton" value="Cargar datos" id="cargarDatos">
                </li><!-- FIN CARGAR CSV _______________________________________________________________________________ -->





                <li><!-- EXPORTAR CSV _______________________________________________________________________________ -->
                    <label for="exportarDatos">Exportar datos a un archivo en formato CSV los datos desde una tabla de la Base de Datos.</label>
                    <input type="submit" name="boton" value="Exportar datos" id="exportarDatos">
                </li><!-- FIN EXPORTAR CSV _______________________________________________________________________________ -->
            </ul>
        </form>
        <section>
            <h2>Info sobre los datos</h2>
            <ul>
                <li>El dni y el telefono debe tener 9 caracteres</li>
                <li>El formulario de modificacion y de insercion deben estar llenos para poder insertar o modificar</li>
                <li>Todas las casillas deben tener mas de 1 caracter</li>
            </ul>
        </section>
    </main>
</body>

</html>