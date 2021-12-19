<?php
session_start();
class Veterinaria
{
    private $servername = "localhost";
    private $username = "DBUSER2021";
    private $password = "DBPSWD2021";


    public $admin=array("email"=>"admin@uniovi.es",
    "contraseña"=>"adminuniovies",
    "nombre"=>"admin",
    "apellidos"=>"uniovi",
    "telefono"=>"000000000",
    "edad"=>"20");    
    private $conn = null;
    private $pst = null;

    public $datosParaIniciarSesion = [];
    public $datosParaRegistrarse = [];
    public $datosParaRegistrarMascota = [];
    public $datosCuidados = [];
    public $mascotasUsuarioActual=[];

    public $datosUsuarioActual;
    public $isContraseñaCorrecta;

    public $seHaApretadoCuidados;
    public $seHaApretadoIniciarSesion;
    public $seHaApretadoRegistrarse;
    public $seHaApretadoRegistrarMascota;

    public $seHaIniciadoSesion;

    public $contraseñaIncorrectaIniciarSesion;
    public $datosRegistrarseIncorrectos;
    public $datosRegistrarMascotaIncorrectos;
    public $datosCuidadosMascotaIncorrectos;

    public $noExisteUsuarioIniciarSesion;
    public $existeUsuarioRegistrarse;

    public $isClienteIniciadoSesion;
    public function __construct()
    {
    }
    public function connect()
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password);
        $this->pst = null;
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        if (!mysqli_select_db($this->conn, "veterinariaDatabase")) {
            die("Cant select database");
        }
    }
    public function disconnect()
    {
        try {
            if (isset($this->pst)) {
                if ($this->pst->close() === FALSE) {
                    echo "<p>Error closing pst: " . $this->conn->error . "</p>";
                }
            }

            if ($this->conn->close() === FALSE) {
                echo "<p>Error closing conn: " . $this->conn->error . "</p>";
            }
        } catch (Exception $err) {
            echo $err;
        }
    }
    public function crearBaseDatos()
    {
        $this->connect();
        $createDb = "CREATE DATABASE veterinariaDatabase";
        if ($this->conn->query($createDb) === FALSE) {
            if(str_contains($this->conn->error,"database exists"))
                return;
            echo "<p>Error creating database: " . $this->conn->error . "</p>";
        }
        $this->disconnect();
    }
    public function crearTablas()
    {
        $this->connect();
        $createTableCliente = "CREATE TABLE Cliente (
            email VARCHAR(50) PRIMARY KEY,
            contraseña VARCHAR(50) NOT NULL,
            nombre VARCHAR(30) NOT NULL,
            apellidos VARCHAR(30) NOT NULL,
            telefono int(9),
            edad int(3) NOT NULL
            )";
        $createTableMascotas = "CREATE TABLE Mascota (
            ID MEDIUMINT NOT NULL AUTO_INCREMENT,
            nombre VARCHAR(50) NOT NULL UNIQUE,
            especie VARCHAR(30) NOT NULL,
            email_propietario VARCHAR(50),
            años int(3) NOT NULL,
            PRIMARY KEY(ID)
            )";
        $createTableInstalacion = "CREATE TABLE Instalacion (
            ID MEDIUMINT NOT NULL AUTO_INCREMENT,
            nombre VARCHAR(50) NOT NULL,
            descripcion VARCHAR(1000) NOT NULL UNIQUE,
            PRIMARY KEY(ID)
            )";
        $createTableCuidados = "CREATE TABLE Cuidados (
            ID_Instalacion MEDIUMINT NOT NULL,
            ID_Mascota MEDIUMINT NOT NULL,
            hora time NOT NULL,
            comentarios VARCHAR(1000),
            PRIMARY KEY(ID_Instalacion,ID_Mascota)
            )";
        if ($this->conn->query($createTableCliente) === FALSE) {
            
            //echo "<p>Error creating table cliente: " . $this->conn->error . "</p>";
        }
        if ($this->conn->query($createTableMascotas) === FALSE) {
            
            //echo "<p>Error creating table mascotas: " . $this->conn->error . "</p>";
        }
        if ($this->conn->query($createTableInstalacion) === FALSE) {
            
            //echo "<p>Error creating table instalacion: " . $this->conn->error . "</p>";
        }
        if ($this->conn->query($createTableCuidados) === FALSE) {
            
            //echo "<p>Error creating table cuidados: " . $this->conn->error . "</p>";
        }
        $this->disconnect();
    }
    private function isContraseñaCorrecta($correo, $pass)
    {
        $this->connect();
        $selectContraseña = "SELECT contraseña
        FROM Cliente
        WHERE email=?";
        $this->pst = $this->conn->prepare($selectContraseña);
        if ($this->pst === FALSE) {
            echo "<p>ERROR: " . $selectContraseña . " </p><p>" . $this->conn->error . "</p>";
        }
        if ($this->pst->bind_param("s", $correo) === FALSE) {
            echo "<p>Error: " . $selectContraseña . "</p><p>" . $this->conn->error . "</p>";
        }
        if ($this->pst->execute() === FALSE) {
            echo "<p>Error: " . $selectContraseña . "</p><p>" . $this->conn->error . "</p>";
        } else {
            $contraseña = "";
            $this->pst->bind_result($contraseña);
            while ($this->pst->fetch()) {
                if ($contraseña === $pass)
                    return  true;
                else
                    return  false;
            }
        }
        $this->disconnect();
    }
    public function registrarse()
    {
        $this->connect();
        $insertCliente = "INSERT INTO Cliente 
        (email, contraseña, nombre,apellidos,telefono,edad)
        VALUES (?,?,?,?,?,?)";
        $this->pst = $this->conn->prepare($insertCliente);
        if ($this->pst === FALSE) {
            echo "<p>ERROR: " . $insertCliente . " </p><p>" . $this->conn->error . "</p>";
        }

        if (!$this->pst->bind_param(
            "ssssii",
            $this->datosParaRegistrarse["email"],
            $this->datosParaRegistrarse["contraseña"],
            $this->datosParaRegistrarse["nombre"],
            $this->datosParaRegistrarse["apellidos"],
            $this->datosParaRegistrarse["telefono"],
            $this->datosParaRegistrarse["edad"]
        )) {
            echo "<p>ERROR: " . $insertCliente . " </p><p>" . $this->conn->error . "</p>";
        }

        $this->datosClienteYaExisten = false;
        if ($this->pst->execute() === FALSE) {
            if (explode(" ", $this->conn->error)[0] == "Duplicate" ) {
                if($this->datosParaRegistrarse["nombre"]!="admin")
                    $this->datosClienteYaExisten = true;
                return;
            }
            echo "<p>Error: " . $insertCliente . "<p>" . $this->conn->error . "</p>" . "</p>";
            
        }
        if($this->datosClienteYaExisten===false){
            $this->isClienteIniciadoSesion=true;
            $this->datosUsuarioActual=$this->datosParaRegistrarse;
        }
        $this->disconnect();
    }
    public function insertarInstalaciones(){
        $intalaciones=array("baños"=>   "se debe bañar con agua y jabon de mascotas",
            "sala de radiografias"=>    "hay que tranquilizar la mascota o anestesiarla antes",
            "sala de operaciones"=>     "solo en caso extremo",
            "comedero"=>                "para las mascotas que pasan mucho tiempo aqui");
        foreach ($intalaciones as $instalacion => $descripcion) {
            $this->connect();
            
            $insertInstalacion = "INSERT INTO Instalacion 
            (nombre, descripcion)
            VALUES (?,?)";
            $this->pst = $this->conn->prepare($insertInstalacion);
            if ($this->pst === FALSE) {
                echo "<p>ERROR: " . $insertInstalacion . " </p><p>" . $this->conn->error . "</p>";
            }

            if (!$this->pst->bind_param(
                "ss",
                $instalacion,
                $descripcion
            )) {
                echo "<p>ERROR: " . $insertInstalacion . " </p><p>" . $this->conn->error . "</p>";
            }

            if ($this->pst->execute() === FALSE) {
                if(explode(" ",$this->conn->error)[0]==="Duplicate"){
                    return;
                }
                echo "<p>Error: " . $insertInstalacion . "<p>" . $this->conn->error . "</p>" . "</p>";
            }
            $this->disconnect();
        }
    }
    public function iniciarSesion()
    {
        $this->connect();
        $this->datosUsuarioActual = [];
        $identificadorParam = (string)$this->datosParaIniciarSesion["email"];
        $selectCliente = "SELECT nombre, apellidos, contraseña
        FROM Cliente
        WHERE email=?";
        $this->datosBuscarNoExisten = false;
        $this->pst = $this->conn->prepare($selectCliente);

        if ($this->pst === FALSE) {
            echo "<p>Error: " . $selectCliente . "<p>" . $this->conn->error . "</p>" . "</p>";
        }
        if (!$this->pst->bind_param("s", $identificadorParam)) {
            echo "<p>Error: " . $selectCliente . "<p>" . $this->conn->error . "</p>" . "</p>";
        }

        if ($this->pst->execute() === FALSE) {
            if(str_contains($this->conn->error,"Duplicate entry")){
                return;
            }
            echo "<p>Error: " . $selectCliente . "<p>" . $this->conn->error . "</p>" . "</p>";
        } else {
            $nombre = "";
            $apellidos = "";
            $contraseña = "";
            $this->pst->bind_result(
                $nombre,
                $apellidos,
                $contraseña
            );
            while ($this->pst->fetch()) {
                $this->datosUsuarioActual["nombre"] = $nombre;
                $this->datosUsuarioActual["apellidos"] = $apellidos;
                $this->datosUsuarioActual["email"] = $this->datosParaIniciarSesion["email"];
            }


            if (!isset($this->datosUsuarioActual["nombre"]))
                $this->noExisteUsuarioIniciarSesion = true;
            else $this->noExisteUsuarioIniciarSesion = false;
        }
        $this->seHaIniciadoSesion=false;
        if($this->noExisteUsuarioIniciarSesion===false){
            $this->seHaIniciadoSesion=true;
        }
        $this->disconnect();
    }
    public function cerrarSesion()
    {
        $this->seHaIniciadoSesion=false;
        $this->datosUsuarioActual = [];
    }

    public function registrarMascota()
    {
        $this->connect();

        $insertMascota = "INSERT INTO Mascota
        (nombre, especie,email_propietario,años)
        VALUES (?,?,?,?)";

        $this->pst = $this->conn->prepare($insertMascota);
        
        if($this->pst===FALSE){
            echo "<p>Error: " . $insertMascota . "<p>" . $this->conn->error . "</p>" . "</p>";
        }

        if($this->pst->bind_param("sssi", $this->datosParaRegistrarMascota["nombre"],
         $this->datosParaRegistrarMascota["especie"],$this->datosUsuarioActual["email"], $this->datosParaRegistrarMascota["años"])===FALSE){
            
            echo "<p>Error: " . $insertMascota . "<p>" . $this->conn->error . "</p>" . "</p>";
        }

        if ($this->pst->execute() === FALSE) {
            if(!str_contains($this->conn->error,"Duplicate entry"))
                echo "<p>Error: " . $insertMascota . "<p>" . $this->conn->error . "</p>" . "</p>";
        }
        $this->disconnect();
    }
    
    public function getMascotasUsuarioActual()
    {
        $this->connect();

        $selectMascotas = "SELECT ID,nombre,especie,años FROM Mascota
            WHERE email_propietario=?";
        $this->pst = $this->conn->prepare($selectMascotas);
        if ($this->pst === FALSE) {
            echo "<p>Error: " . $selectMascotas . "<p>" . $this->conn->error . "</p>" . "</p>";
        }

        if ($this->pst->bind_param("s", $this->datosUsuarioActual["email"]) === FALSE) {
            echo "<p>Error: " . $selectMascotas . "<p>" . $this->conn->error . "</p>" . "</p>";
        } 
        
        if ($this->pst->execute() === FALSE) {
            echo "<p>Error: " . $selectMascotas . "<p>" . $this->conn->error . "</p>" . "</p>";
        }else {
            $ID = "";
            $nombre = "";
            $especie = "";
            $años = "";
            $this->pst->bind_result(
                $ID,
                $nombre,
                $especie,
                $años
            );
            $this->mascotasUsuarioActual=[];
            $nrMascota=0;
            while ($this->pst->fetch()) {
                
                $this->mascotasUsuarioActual[$nrMascota]["ID"] = $ID;
                $this->mascotasUsuarioActual[$nrMascota]["nombre"] = $nombre;
                $this->mascotasUsuarioActual[$nrMascota]["especie"] = $especie;
                $this->mascotasUsuarioActual[$nrMascota]["años"] = $años;
                $nrMascota++;
            }
            $this->disconnect();
        }
    }
    public function getTodasLasMascotas()
    {
        $this->connect();

        $selectMascotas = "SELECT ID,nombre,especie,años FROM Mascota";
        $this->pst = $this->conn->prepare($selectMascotas);
        if ($this->pst === FALSE) {
            echo "<p>Error: " . $selectMascotas . "<p>" . $this->conn->error . "</p>" . "</p>";
        }
        if ($this->pst->execute() === FALSE) {
            echo "<p>Error: " . $selectMascotas . "<p>" . $this->conn->error . "</p>" . "</p>";
        }else {
            $ID = "";
            $nombre = "";
            $especie = "";
            $años = "";
            $this->pst->bind_result(
                $ID,
                $nombre,
                $especie,
                $años
            );
            $this->mascotasUsuarioActual=[];
            $nrMascota=0;
            while ($this->pst->fetch()) {
                
                $this->mascotasUsuarioActual[$nrMascota]["ID"] = $ID;
                $this->mascotasUsuarioActual[$nrMascota]["nombre"] = $nombre;
                $this->mascotasUsuarioActual[$nrMascota]["especie"] = $especie;
                $this->mascotasUsuarioActual[$nrMascota]["años"] = $años;
                $nrMascota++;
            }
            $this->disconnect();
        }
    }
    public function mostrarTodasLasMascotas(){
        $this->getTodasLasMascotas();

        echo "<table>";
        echo "<tr>";
        echo "  <th> ID </th>"; 
        echo "  <th> Nombre     </th>";
        echo "  <th> Especie    </th>";
        echo "  <th> Años    </th>";
        echo "  <th> Cuidados   </th>";
        echo "</tr>";
        foreach ($this->mascotasUsuarioActual as $nrMascota => $datos) {
            echo "<tr>";

            foreach ($datos as $tipoDato => $dato) {
                echo "<td>";

                echo $dato;

                echo "</td>";
                
            }
            
            $cuidados = $this->getCuidadosMascota($datos["ID"]);
            echo "<td>";
            $pendiente="pendiente";
            foreach ($cuidados as $cuidado => $datosCuidado) {
                $pendiente="";
                echo "<ul>";
                foreach ($datosCuidado as $tipoDatoC => $datoC) {
                    echo "<li>" . $datoC . "</li>";
                }
                echo "</ul>";
            }
            echo $pendiente;
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    public function mostrarMascotasUsuarioActual()
    {

        $this->getMascotasUsuarioActual();

        echo "<table>";
        echo "<tr>";
        echo "  <th> Numero </th>";
        echo "  <th> Nombre     </th>";
        echo "  <th> Especie    </th>";
        echo "  <th> Años    </th>";
        echo "  <th> Cuidados   </th>";
        echo "</tr>";
        foreach ($this->mascotasUsuarioActual as $nrMascota => $datos) {
            echo "<tr>";
            echo "<td>";

            echo $nrMascota;

            echo "</td>";
            foreach ($datos as $tipoDato => $dato) {
                if($tipoDato!="ID"){
                    echo "<td>";

                    echo $dato;

                    echo "</td>";
                }
            }
            
            $cuidados = $this->getCuidadosMascota($datos["ID"]);
            echo "<td>";
            $pendiente="pendiente";
            foreach ($cuidados as $cuidado => $datosCuidado) {
                $pendiente="";
                echo "<ul>";
                foreach ($datosCuidado as $tipoDatoC => $datoC) {
                    echo "<li>" . $datoC . "</li>";
                }
                echo "</ul>";
            }
            echo $pendiente;
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    public function getCuidadosMascota($id)
    {
        $this->connect();

        $selectMascotas = "SELECT Instalacion.nombre,Instalacion.descripcion,Cuidados.hora,Cuidados.comentarios
        FROM Cuidados,Instalacion
        WHERE Cuidados.id_instalacion=Instalacion.ID
        AND Cuidados.ID_mascota=?";

        $listaDeCuidados = [];

        $this->pst = $this->conn->prepare($selectMascotas);
        if ($this->pst === FALSE) {
            echo "<p>Error: " . $selectMascotas . "<p>" . $this->conn->error . "</p>" . "</p>";
        }
        if ($this->pst->bind_param("s", $id) === FALSE) {
            echo "<p>Error: " . $selectMascotas . "<p>" . $this->conn->error . "</p>" . "</p>";
        } else {
            $nombreInstalacion = "";
            $descripcionInstalacion = "";
            $horasDeCuidado = "";
            $comentariosDeCuidado = "";
            $this->pst->bind_result(
                $nombreInstalacion,
                $descripcionInstalacion,
                $horasDeCuidado,
                $comentariosDeCuidado
            );
            $nrCuidado = 0;
            while ($this->pst->fetch()) {
                $listaDeCuidados[$nrCuidado] = [];
                $listaDeCuidados[$nrCuidado]["nombre"] = $nombreInstalacion;
                $listaDeCuidados[$nrCuidado]["descripcion"] = $descripcionInstalacion;
                $listaDeCuidados[$nrCuidado]["horas"] = $horasDeCuidado;
                $listaDeCuidados[$nrCuidado]["comentarios"] = $comentariosDeCuidado;
                $nrCuidado++;
            }
            $this->disconnect();
            return  $listaDeCuidados;
            
        }

        
    }

    public function getInstalaciones()
    {
        $this->connect();

        $selectMascotas = "SELECT ID ,nombre, descripcion
        FROM Instalacion";

        $listaDeInstalaciones = [];

        $this->pst = $this->conn->prepare($selectMascotas);
        if ($this->pst === FALSE) {
            echo "<p>Error: " . $selectMascotas . "<p>" . $this->conn->error . "</p>" . "</p>";
        }

        $IDInstalacion = "";
        $nombreInstalacion = "";
        $descripcionInstalacion = "";

        $this->pst->bind_result(
            $IDInstalacion,
            $nombreInstalacion,
            $descripcionInstalacion

        );
        $nrIntalacion = 0;
        while ($this->pst->fetch()) {
            echo "INSTALACION: ". $nombreInstalacion;
            $listaDeInstalaciones[$nrIntalacion] = [];
            $listaDeInstalaciones[$nrIntalacion]["ID"] = $IDInstalacion;
            $listaDeInstalaciones[$nrIntalacion]["nombre"] = $nombreInstalacion;
            $listaDeInstalaciones[$nrIntalacion]["descripcion"] = $descripcionInstalacion;

            $nrIntalacion++;
        }
        return  $listaDeInstalaciones;


        $this->disconnect();
    }
    public function mostrarOptionsInstalaciones()
    {

        $instalaciones = $this->getInstalaciones();
        foreach ($instalaciones as $instalacion => $datos) {
            echo "<option value=\"" . $datos["nombre"] . "\" ?>" . $datos["nombre"] . "</option>";
        }
    }



    public function botonPulsado($boton)
    {

        $this->seHaApretadoCuidados = false;
        $this->seHaApretadoIniciarSesion = false;
        $this->seHaApretadoRegistrarse = false;
        $this->seHaApretadoRegistrarMascota = false;
        $this->crearBaseDatos();
        $this->crearTablas();
        $this->insertarInstalaciones();
        switch ($boton) {
            case 'Registrarse':
                $this->seHaApretadoRegistrarse = true;
                $this->comprobarDatosRegistrarse();
                if (!$this->datosRegistrarseIncorrectos)
                    $this->registrarse();
                break;
            case 'Iniciar Sesion':
                $this->seHaApretadoIniciarSesion = true;
                $this->comprobarDatosIniciarSesion();
                if (!$this->contraseñaIncorrectaIniciarSesion)
                    $this->iniciarSesion();
                break;
            case 'Registrar Mascota':
                $this->seHaApretadoRegistrarMascota = true;
                $this->comprobarDatosRegistrarMascota();
                if (!$this->datosRegistrarMascotaIncorrectos)
                    $this->registrarMascota();
                break;
            case 'Planificar Cuidado':
                $this->seHaApretadoCuidados = true;
                $this->comprobarDatosCuidados();
                if (!$this->datosCuidadosMascotaIncorrectos)
                    $this->planificarCuidadoMascota();
                break;
            case 'Cerrar Sesion':
                $this->cerrarSesion();
            default:
                break;
        }
    }
    public function planificarCuidadoMascota(){

    }
    public function comprobarDatosIniciarSesion()
    {
        $this->contraseñaIncorrectaIniciarSesion = false;
        $this->noExisteUsuarioIniciarSesion = false;
        $this->seHaIniciadoSesion=true;
        $puedoComprobarContraseña = true;
        if (
            !isset($this->datosParaRegistrarse["email"]) ||
            !isset($this->datosParaIniciarSesion["contraseña"])
        ) {
            $this->puedoComprobarContraseña = false;
        }
        foreach ($this->datosParaIniciarSesion as $dato) {
            if (strlen($dato) == 0) {
                $puedoComprobarContraseña = false;
            }
        }
        if ($puedoComprobarContraseña == true) {
            if (!$this->isContraseñaCorrecta($this->datosParaIniciarSesion["email"], $this->datosParaIniciarSesion["contraseña"])) {
                $this->contraseñaIncorrectaIniciarSesion = true;
                $this->seHaIniciadoSesion=false;
            }
        }
        $this->isClienteIniciadoSesion=false;
        foreach ($this->datosParaIniciarSesion as $key => $value) {
            if($this->admin[$key]!=$value){
                $this->isClienteIniciadoSesion=true;
            }
        }
        
    }
    public function comprobarDatosRegistrarse()
    {
        $this->datosRegistrarseIncorrectos = false;

        if (
            !isset($this->datosParaRegistrarse["email"]) ||
            !isset($this->datosParaRegistrarse["contraseña"]) ||
            !isset($this->datosParaRegistrarse["nombre"]) ||
            !isset($this->datosParaRegistrarse["apellidos"]) ||
            !isset($this->datosParaRegistrarse["telefono"]) ||
            !isset($this->datosParaRegistrarse["edad"])
        ) {
            $this->datosRegistrarseIncorrectos = true;
        }
        foreach ($this->datosParaRegistrarse as  $dato) {
            if (strlen($dato) == 0) {
                $this->datosRegistrarseIncorrectos = true;
            }
        }
        if (strlen($this->datosParaRegistrarse["telefono"]) != 9 or $this->datosParaRegistrarse["edad"] < 18) {
            $this->datosRegistrarseIncorrectos = true;
        }
    }
    public function comprobarDatosRegistrarMascota()
    {
        $this->datosRegistrarMascotaIncorrectos = false;
        foreach ($this->datosParaRegistrarMascota as  $dato) {
            if (strlen($dato) == 0) {
                $this->datosRegistrarseIncorrectos = true;
            }
        }
        if ($this->datosParaRegistrarMascota["años"] < 0) {
            $this->datosRegistrarMascotaIncorrectos = true;
        }
    }
    public function comprobarDatosCuidados()
    {
        $this->datosCuidadosMascotaIncorrectos = false;
        foreach ($this->datosCuidados as  $dato) {
            if (strlen($dato) == 0) {
                $this->datosRegistrarseIncorrectos = true;
            }
        }
    }
}


if (isset($_SESSION['Veterinaria'])) {
    $veterinaria = $_SESSION['Veterinaria'];
} else {
    $veterinaria = new Veterinaria();
    $_SESSION['Veterinaria'] = $veterinaria;
}




if (count($_POST) > 0) {
    $boton = $_POST['boton'];
    $datosParaIniciarSesion;
    $datosParaRegistrarse;
    $datosParaRegistrarMascota;
    $datosCuidados;
    
    if(isset($_SESSION['once'])){
        $veterinaria->datosParaRegistrarse=$veterinaria->admin;
        $veterinaria->botonPulsado("Registrarse");
    }

    $_SESSION['once']=true;

    
    if (isset($_POST['iniciarSesion'])) {
        $datosParaIniciarSesion = $_POST['iniciarSesion'];
        //var_dump($datosParaIniciarSesion);
        $veterinaria->datosParaIniciarSesion = $datosParaIniciarSesion;
        
    }
    if (isset($_POST['registrarse'])) {
        $datosParaRegistrarse = $_POST['registrarse'];
        //var_dump($datosParaRegistrarse);
        $veterinaria->datosParaRegistrarse = $datosParaRegistrarse;
    }
    if (isset($_POST['registrarMascota'])) {
        $datosParaRegistrarMascota = $_POST['registrarMascota'];
        //var_dump($datosParaRegistrarMascota);
        $veterinaria->datosParaRegistrarMascota = $datosParaRegistrarMascota;
    }
    if (isset($_POST['cuidados'])) {
        $datosCuidados = $_POST['cuidados'];
        //var_dump($datosCuidados);
        $veterinaria->datosCuidados = $datosCuidados;
    }

    $veterinaria->botonPulsado($boton);
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Veterinaria">
    <meta name="keywords" content="Base de datos, veterinaria, valentin">
    <meta name="author" content="Valentin">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="Veterinaria.css">
    <title>Ejercicio6: Base de datos</title>
</head>

<body>
    <header>
        
        <h1>
            <img src="logo.jpg" alt="Logo de perro amarillo">VetVal
        </h1>
        <p>
            Cuidamos y queremos a tus mascotas como si fueran nuestras.
        </p>
        <table title="tu" <?php if($veterinaria->seHaIniciadoSesion==false) echo "hidden"?>>
            <tr>
                <th>Correo</th>
                <th>Nombre</th>
                <th>Apellidos</th>
            </tr>
            <tr>
                <td><?php if(isset($veterinaria->datosUsuarioActual["email"])) echo $veterinaria->datosUsuarioActual["email"] ?></td>
                <td><?php if(isset($veterinaria->datosUsuarioActual["nombre"])) echo $veterinaria->datosUsuarioActual["nombre"] ?></td>
                <td><?php if(isset($veterinaria->datosUsuarioActual["apellidos"])) echo $veterinaria->datosUsuarioActual["apellidos"] ?></td>
            </tr>
        </table>
        <form title="Cerrar sesion" action="#" method="post" <?php if(!$veterinaria->seHaIniciadoSesion==true) echo "hidden"?>>
            <!-- CERRAR SESION ____________________________________________________________________________________-->
            <label for="cerrarsesion">Click para Cerrar Sesion: </label>
            <input type="submit" name="boton" value="Cerrar Sesion" id="cerrarsesion">
        </form><!-- FIN CERRAR SESION ____________________________________________________________________________________-->
    </header>
    <main>

        <form action="#" method="post" <?php if($veterinaria->seHaIniciadoSesion) echo "hidden"?>>
            <!-- INICIAR SESION ____________________________________________________________________________________-->
            <?php if ($veterinaria->noExisteUsuarioIniciarSesion && $veterinaria->seHaApretadoIniciarSesion) echo "<p title=\"ERROR\">NO EXISTE UNA CUENTA ASOCIADA A ESTE CORREO</p>" ?>
            <?php if ($veterinaria->contraseñaIncorrectaIniciarSesion && $veterinaria->seHaApretadoIniciarSesion) echo "<p title=\"ERROR\">CONTRASEÑA INCORRECTA</p>" ?>
            <fieldset>
                <legend>Iniciar Sesion </legend>


                <label for="emailIS">Email: </label>
                <input type="email" name="iniciarSesion[email]" value="<?php if (($veterinaria->noExisteUsuarioIniciarSesion
                                                                            || $veterinaria->contraseñaIncorrectaIniciarSesion) && $veterinaria->seHaApretadoIniciarSesion)
                                                                            echo $veterinaria->datosParaIniciarSesion["email"] ?>" id="emailIS">

                <label for="contraseñaIS">Contraseña: </label>
                <input type="password" name="iniciarSesion[contraseña]" id="contraseñaIS">

            </fieldset>
            <label for="iniciarsesion">Click para Iniciar Sesion: </label>
            <input type="submit" name="boton" value="Iniciar Sesion" id="iniciarsesion">
        </form><!-- FIN INICIAR SESION ____________________________________________________________________________________-->



        <form action="#" method="post" <?php if($veterinaria->seHaIniciadoSesion==true) echo "hidden"?> >
            <!-- REGISTRARSE ____________________________________________________________________________________-->
            <fieldset>
                <legend>Registrarse </legend>
                <?php if ($veterinaria->existeUsuarioRegistrarse && $veterinaria->seHaApretadoRegistrarse) echo "<p title=\"ERROR\">YA EXISTE UNA CUENTA CON ESTE CORREO</p>" ?>
                <?php if ($veterinaria->datosRegistrarseIncorrectos && $veterinaria->seHaApretadoRegistrarse) echo "<p title=\"ERROR\">DATOS INCORRECTOS</p>" ?>
                <label for="emailR">Email: </label>
                <input type="email" name="registrarse[email]" value="<?php if (
                                                                            $veterinaria->existeUsuarioRegistrarse
                                                                            || $veterinaria->seHaApretadoRegistrarse
                                                                            || $veterinaria->datosRegistrarseIncorrectos
                                                                        )
                                                                            echo $veterinaria->datosParaRegistrarse["email"] ?>" id="emailR">

                <label for="contraseñaR">Contraseña(minimo 8 caracteres): </label>
                <input type="password" name="registrarse[contraseña]" id="contraseñaR">

                <label for="nombreR">Nombre: </label>
                <input type="text" name="registrarse[nombre]" value="<?php if (
                                                                            $veterinaria->existeUsuarioRegistrarse
                                                                            || $veterinaria->seHaApretadoRegistrarse
                                                                            || $veterinaria->datosRegistrarseIncorrectos
                                                                        )
                                                                            echo $veterinaria->datosParaRegistrarse["nombre"] ?>" id="nombreR">

                <label for="apellidosR">Apellidos: </label>
                <input type="text" name="registrarse[apellidos]" value="<?php if (
                                                                            $veterinaria->existeUsuarioRegistrarse
                                                                            || $veterinaria->seHaApretadoRegistrarse
                                                                            || $veterinaria->datosRegistrarseIncorrectos
                                                                        )
                                                                            echo $veterinaria->datosParaRegistrarse["apellidos"] ?>" id="apellidosR">


                <label for="telefonoR">Telefono: </label>
                <input type="tel" name="registrarse[telefono]" value="<?php if (
                                                                                $veterinaria->existeUsuarioRegistrarse
                                                                                || $veterinaria->seHaApretadoRegistrarse
                                                                                || $veterinaria->datosRegistrarseIncorrectos
                                                                            )
                                                                                echo $veterinaria->datosParaRegistrarse["telefono"] ?>" id="telefonoR" maxlength="9">

                <label for="edadR">Edad: </label>
                <input type="text" name="registrarse[edad]" value="<?php if (
                                                                        $veterinaria->existeUsuarioRegistrarse
                                                                        || $veterinaria->seHaApretadoRegistrarse
                                                                        || $veterinaria->datosRegistrarseIncorrectos
                                                                    )
                                                                        echo $veterinaria->datosParaRegistrarse["edad"] ?>" id="edadR">
            </fieldset>
            <label for="registrarse">Click para registrarse: </label>
            <input type="submit" name="boton" value="Registrarse" id="registrarse">
        </form><!-- FIN REGISTRARSE ____________________________________________________________________________________-->




        <form action="#" method="post" <?php if($veterinaria->isClienteIniciadoSesion==false || $veterinaria->seHaIniciadoSesion==false) echo "hidden"?> >
            <!-- REGISTRAR MASCOTA ____________________________________________________________________________________-->
            <fieldset>
                <legend>Registrar Mascota</legend>
                <?php if ($veterinaria->datosRegistrarMascotaIncorrectos && $veterinaria->seHaApretadoRegistrarse) echo "<p title=\"ERROR\">DATOS INCORRECTOS</p>" ?>
                <label for="nombreRM">Nombre: </label>
                <input type="text" name="registrarMascota[nombre]"  id="nombreRM">

                <label for="especieRM">Especie: </label>
                <input type="text" name="registrarMascota[especie]"  id="especieRM">

                <label for="edadRM">Edad (En años humanos): </label>
                <input type="text" name="registrarMascota[años]"  id="edadRM">

            </fieldset>
            <label for="registrarmascota">Click para registrar tu mascota: </label>
            <input type="submit" name="boton" value="Registrar Mascota" id="registrarmascota">
        </form><!-- FIN REGISTRARSE ____________________________________________________________________________________-->




        <form action="#" method="post" <?php if($veterinaria->isClienteIniciadoSesion==true || $veterinaria->seHaIniciadoSesion==false) echo "hidden"?> >
            <!-- CUIDADOS ____________________________________________________________________________________-->
            <fieldset>
                <legend>¿Que cuidados se necesitan? </legend>
                <?php if ($veterinaria->datosCuidadosMascotaIncorrectos && $veterinaria->seHaApretadoCuidados) echo "<p title=\"ERROR\">DATOS INCORRECTOS</p>" ?>
                <label for="IDMascotaCuidados">ID de la mascota: </label>
                <input type="text" name="cuidados[IDMascota]" value="<?php if ($veterinaria->datosRegistrarMascotaIncorrectos && $veterinaria->seHaApretadoRegistrarse)
                                                                            echo $veterinaria->datosCuidados["ID"] ?>" id="IDMascotaCuidados">

                <label for="horaCuidados">Hora del cuidado: </label>
                <input type="time" name="cuidados[hora]" value="<?php if ($veterinaria->datosRegistrarMascotaIncorrectos && $veterinaria->seHaApretadoRegistrarse)
                                                                    echo $veterinaria->datosCuidados["hora"] ?>" id="horaCuidados">

                <label for="Instalacion">Instalacion: </label>
                <select name="cuidados[instalacion]" id="Instalacion">
                    <?php $veterinaria->mostrarOptionsInstalaciones() ?>
                </select>

                <label for="comentariosCuidados">Comentarios: </label>
                <textarea name="cuidados[comentarios]" id="comentariosCuidados" cols="30" rows="10">
                <?php if ($veterinaria->datosRegistrarMascotaIncorrectos && $veterinaria->seHaApretadoRegistrarse)
                    echo $veterinaria->datosCuidados["comentarios"] ?>
                </textarea>

            </fieldset>
            <label for="planificarcuidado">Click para planificar el cuidado: </label>
            <input type="submit" name="boton" value="Planificar Cuidado" id="planificarcuidado">
        </form><!-- FIN CUIDADOS ____________________________________________________________________________________-->




        <article <?php if($veterinaria->seHaIniciadoSesion==false) echo "hidden"?>> <!-- MASCOTAS ____________________________________________________________________________________-->
            <head>
                <?php if ($veterinaria->isClienteIniciadoSesion==true && $veterinaria->seHaIniciadoSesion){
                    echo "<h2>Tus Mascotas </h2>";
                }else echo "<h2>Todas Las Mascotas</h2>"; ?>
            </head>
            <section>
                <h3>Tabla de mascotas: </h3>
                <?php if ($veterinaria->isClienteIniciadoSesion==true && $veterinaria->seHaIniciadoSesion)
                    $veterinaria->mostrarMascotasUsuarioActual();
                    else $veterinaria->mostrarTodasLasMascotas() ?>
            </section>
        </article> <!-- FIN MASCOTAS ____________________________________________________________________________________-->

    </main>
</body>
<?php //echo "se ha iniciado sesion: ". $veterinaria->seHaIniciadoSesion; echo "es cliente? ". $veterinaria->isClienteIniciadoSesion;?>
</html>
