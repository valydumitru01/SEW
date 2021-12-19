<?php
session_start();
class CalculadoraBasica
{

    protected $screen;
    protected $operacion;
    protected $num1;
    protected $num2;
    protected $memory;
    protected $mrcOnce;
    protected $equalPressed;

    public function __construct()
    {
        $this->equalPressed=false;
    }

    public function pulsado($boton)
    {
        
        if($this->equalPressed==true){
            $this->screen="";
        }
        $this->equalPressed=false;
        if(is_numeric($boton)){
            $this->digitos($boton);
        }
        switch($boton){
            case "mrc":
                $this->mrc();
                break;
            case "m+":
                $this->mMas();
                break;
            case "m-":
                $this->mMenos();
                break;
            case "+":
                $this->suma();
                break;
            case "-":
                $this->resta();
                break;
            case "/":
                $this->division();
                break;
            case "*":
                $this->multiplicacion();
                break;
            case ".":
                $this->punto();
                break;
            case "=":
                $this->igual();
                $this->equalPressed=true;
                break;
            default:
                break;
        }
    }

    public function digitos($digit)
    {
        $this->mrcOnce = false;
        $this->num2 = $this->num2 . $digit;
        $this->screen = $this->screen . $digit;
    }

    public function punto()
    {
        $this->mrcOnce = false;
        if ($this->num2 == "") {
            $this->num1 = "0.";
            $this->screen = $this->screen . $this->num1;
        } elseif (strpos($this->num1, ".") > -1) {

        } else {
            $this->num1 .= ".";
            $this->screen = $this->screen . $this->num1;
        }
    }
    public function suma()
    {
        $this->mrcOnce = false;
        $this->operacion = "+";
        $this->screen = $this->screen . $this->operacion;
        $this->num1 = $this->num2;
        $this->num2 = "";
    }
    public function resta()
    {
        $this->mrcOnce = false;
        $this->operacion = "-";
        $this->screen = $this->screen . $this->operacion;
        $this->num1 = $this->num2;
        $this->num2 = "";
    }
    public function multiplicacion()
    {
        $this->mrcOnce = false;
        $this->operacion = "*";
        $this->screen = $this->screen . $this->operacion;
        $this->num1 = $this->num2;
        $this->num2 = "";
    }
    public function division()
    {
        $this->mrcOnce = false;
        $this->operacion = "/";
        $this->screen = $this->screen . $this->operacion;
        $this->num1 = $this->num2;
        $this->num2 = "";
    }
    public function mrc()
    {
        if ($this->mrcOnce == true) {
            $this->borrar();
        } else {
            $this->mrcOnce = true;
            $this->num2 = $this->memory;
            $this->screen = $this->num2;
            $this->memory = "";
        }
    }
    public function mMenos()
    {
        $this->mrcOnce = false;

        $this->num1 = $this->memory;
        $this->operacion = "-";
        $this->memory = "";
    }
    public function mMas()
    {
        $this->mrcOnce = false;

        $this->num1 = $this->memory;
        $this->operacion = "+";
        $this->memory = "";
    }
    public function borrar()
    {
        $this->mrcOnce = false;
        $this->num1 = "";
        $this->num2 = "";
        $this->operacion = "";
        $this->screen = "";
    }
    public function igual() {
        $this->mrcOnce = false;
        $result=0;
        try{
            eval("\$result" . "=" .$this->getScreen().";");
        }catch(Exception $err){
            echo "Error: "+$err ;
            
        }
        
        $this->memory="".$result;
        $this->screen=$result;
        $this->num1="";
        $this->num2="";

    }
    
    public function getScreen()
    {
        return $this->screen;
    }
}
if (isset($_SESSION['calculadora'])) {
    $calculadora = $_SESSION['calculadora'];
} else {
    $calculadora = new CalculadoraBasica();
    $_SESSION['calculadora'] = $calculadora;
}


if (count($_POST) > 0) {
    $boton = $_POST['boton'];
    $calculadora->pulsado($boton);
} 
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Calculadora Cientifica">
    <meta name="keywords" content="ECMAscript,calculadora,Valentin">
    <meta name="author" content="Valentin">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="CalculadoraBasica.css">
    <title>Calculadora Basica</title>


</head>

<body>
    <header>
        <h1>
            Calculadora Basica
        </h1>
    </header>
    <main>
        <form action="#" method="post" name="calculadoraForm">
            <fieldset>
                <input type="text" title="screen" readonly="readonly" value="<?php echo $calculadora->getScreen() ?>" />
            </fieldset>
            <fieldset>
                <input type="submit" name="boton" value="mrc"/>
                <input type="submit" name="boton" value="m-"/>
                <input type="submit" name="boton" value="m+"/>
                <input type="submit" name="boton" value="/" />
                <input type="submit" name="boton" value="7" />
                <input type="submit" name="boton" value="8" />
                <input type="submit" name="boton" value="9" />
                <input type="submit" name="boton" value="*" />
                <input type="submit" name="boton" value="4" />
                <input type="submit" name="boton" value="5" />
                <input type="submit" name="boton" value="6" />
                <input type="submit" name="boton" value="-" />
                <input type="submit" name="boton" value="1" />
                <input type="submit" name="boton" value="2" />
                <input type="submit" name="boton" value="3" />
                <input type="submit" name="boton" value="+" />
                <input type="submit" name="boton" value="0" />
                <input type="submit" name="boton" value="." />
                <input type="submit" name="boton" value="C" />
                <input type="submit" name="boton" value="=" />

            </fieldset>
        </form>
    </main>
</body>

</html>