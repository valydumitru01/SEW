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
        $this->screen="";
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
        //echo $this->screen;
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
            $this->screen= "Error: "+$err ;
            
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
class CalculadoraCientifica extends CalculadoraBasica
{

    protected $openedPars;
    protected $notacionCientifica;
    protected $twondClicked;
    protected $twondClickedTrig;
    protected $hypClicked;

    public function __construct()
    {
        parent::__construct();
        $this->openedPars = 0;
        $this->notacionCientifica = false;
        $this->twondClicked = false;
        $this->twondClickedTrig = false;
        $this->hypClicked = false;
    }
    public function openPar()
    {
        $this->screen = $this->screen . "(";
        $this->openedPars++;
    }
    public function closePar()
    {
        if ($this->openedPars > 0) {
            $this->screen = $this->screen . ")";
            $this->openedPars--;
        }
    }
    public function contar($a)
    {
        $indices = [];

        for ($i = 0; $i < strlen($this->screen); $i++) {
            if ($this->screen[$i] == $a)
                array_push($indices,$i);
        }
        return sizeof($indices);
    }

    public function igual()
    {
        $result=0;
        $toEval="\$result=";
        $this->mrcOnce = false;

        $toEval.=$this->screen;
        $toEval.=";";
        $toEval=str_replace("^","**",$toEval);
        //echo "Evaluated: " . $toEval;

        try{
            if($this->screen!="")
                eval($toEval);
                
        }catch(Exception $err){
            $this->screen= "ERROR: ". $err;
            return 0;
        }finally{
            $this->screen = "";
            $this->num1 = "";
            $this->num2 = "";
            if ($this->notacionCientifica) {
                $parteEntera = substr($result ,0, 1);
                $parteDecimal = substr($result ,1, strlen($result));
                $a = explode('.',$parteDecimal)[0];
                $b = explode('.',$parteDecimal)[1];
                $parteDecimal = $a . $b;
                $result =$parteEntera . "." . $parteDecimal . "e+" . (strlen($result) - 1);
            }
            if ($result == "undefined")
                $this->screen = "Error";
            
            $this->screen=$result;
            //echo "\nResult= ".$result;
            return (float)$result;
        }

    }
    public function pi()
    {
        $this->screen .= "π";
    }
    public function e()
    {
        $this->screen .= "e";
    }

    public function borrarUnNumero()
    {
        $this->mrcOnce = false;
        $this->screen = substr($this->screen , 0, strlen($this->screen)- 1);
    }

    public function negate()
    {
        if ($this->screen == "") {
            return;
        } else if ($this->screen[0] == '-') {
            $this->screen = substr($this->screen , 1, strlen($this->screen));
        } else {
            $this->screen = '-' . $this->screen;
        }
    }

    public function mod()
    {
        $this->mrcOnce = false;
        $this->operacion = "%";
        $this->screen = $this->screen . $this->operacion;
        $this->num1 = $this->num2;
        $this->num2 = "";
    }

    public function fact()
    {

        $result = $this->igual();
        //echo "\nFACTORIAL, se multiplica " . $result . " VECES\n";
        $total = 1;
        $this->screen=$total;
       
        for ($i=1; $i <= $result; $i++) { 
            $total = $total * $i;
        }
        $this->screen=$total;
    }

    public function log()
    {
        $result = $this->igual();

        $total = log10($result);
        $this->screen=$total;
        $this->screen=$total;
    }

    public function logxy()
    {
        $this->mrcOnce = false;
        $this->operacion = "base";
        $this->screen = "log" . $this->screen . $this->operacion;
        $this->num1 = $this->num2;
        $this->num2 = "";
    }

    public function ln()
    {
        $result = $this->igual();

        $total = log($result);
        $this->screen=$total;
        $this->screen=$total;
    }

    public function ex()
    {
        $result = $this->igual();

        $total = exp($result);
        $this->screen=$total;
        $this->screen=$total;
    }

    public function exp()
    {
        //Pone en notación científica
        $this->notacionCientifica = true;
        $this->igual();
        $this->notacionCientifica = false;
    }

    public function abs()
    {
        $result = $this->igual();

        $this->screen= abs($result);
    }
    public function floor()
    { //Devuelve entero más grande <= x
        $result = $this->igual();

        $this->screen= floor($result);
        
    }
    public function ceil()
    {
        $result = $this->igual();

        $this->screen= ceil($result);
    }
    public function rand()
    {
        $random = rand();
        $this->screen=$random;
        //$this->escribir(random);
    }
    public function dms()
    {
        //SI HAY $ERROR COMPROBAR ESTO---------------------------------------------------------------------------------
        //Pasa de degrees a dms
        $result = $this->igual();
        $parteEntera = explode(".",$result)[0];
        $parteDecimal = explode(".",$result)[1];
        $m = (('0.' . $parteDecimal) . 60);

        $degrees =$parteEntera;
        $minutes =substr(explode(".",$m)[0], 0, 2);
        $seconds =explode(".",$m)[1] * 60;

        $this->screen= $degrees . '.' . $minutes . $seconds;
    }
    public function degrees()
    {
        //Pasa de dms a degrees
        $result = $this->igual();
        $degrees = explode('.',$result)[0];
        $minutes = explode('.',$result)[1];
        $seconds = substr(explode('.',$result)[1], 2, strlen($result));

        $parteEntera = $degrees;
        $parteDecimal = ($minutes .$seconds / 60) / 60;
        $parteDecimal = str_replace($parteDecimal,'.', '');

        $this->screen=$parteEntera . '.' . $parteDecimal;
    }

    public function inverse()
    {
        $result = $this->igual();
        if($result!=0)
            $this->screen= 1 / $result;
    }

    public function powerTwoOf()
    {
        $this->igual();

        $this->screen=$this->screen . "^(2)";
    }

    public function powerThreeOf()
    {
        $this->igual();

        $this->screen=$this->screen . "^(3)";
    }

    public function root($n)
    {
        $this->igual();
        $this->screen=$this->screen . "^(1/".$n.")";
    }

    public function XtoTheY()
    {
        $this->mrcOnce = false;
        $this->operacion = "^";
        $this->screen = $this->screen . $this->operacion;
        $this->num1 = $this->num2;
        $this->num2 = "";
    }

    public function YrootX()
    {
        $this->mrcOnce = false;
        $this->operacion = "√";
        $this->screen = $this->screen . $this->operacion;
        $this->num1 = $this->num2;
        $this->num2 = "";
    }

    public function tenToThePower()
    {
        $result = $this->igual();

        $total = pow(10, $result);
        $this->screen=$total;
    }

    public function twoToThePower()
    {
        $result = $this->igual();

        $total = pow(2, $result);
        $this->screen=$total;
    }



    public function sin()
    {
        $result = $this->igual();
        $total = sin($result / 180 * pi());
        $this->screen=$total;
        return $total;
    }
    public function sinh()
    {
        $result = $this->igual();
        $total = sinh($result);
        $this->screen=$total;
        return $total;
    }
    public function sinInverse()
    {
        $result = $this->igual();
        $total = asin($result);
        $this->screen=$total;
        return $total;
    }
    public function sinhInverse()
    {
        $result = $this->igual();
        $total = asinh($result);
        $this->screen=$total;
        return $total;
    }
    public function cos()
    {
        $result = $this->igual();
        $total = cos($result / 180 * pi());
        $this->screen=$total;
        return $total;
    }
    public function cosh()
    {
        $result = $this->igual();
        $total = cosh($result); //en radianes
        $this->screen=$total;
        return $total;
    }
    public function cosInverse()
    {
        $result = $this->igual();
        $total = acos($result);
        $this->screen=$total;
        return $total;
    }
    public function coshInverse()
    {
        $result = $this->igual();
        $total = acosh($result); //en radianes
        $this->screen=$total;
        ////$console . log(total);
        return $total;
    }
    public function tan()
    {
        $result = $this->igual();
        $total = tan($result / 180 * pi());
        $this->screen=$total;
        return $total;
    }
    public function tanh()
    {
        $result = $this->igual();
        $total = tanh($result);
        $this->screen=$total;
        return $total;
    }
    public function tanInverse()
    {
        $result = $this->igual();
        $total = atan($result);
        $this->screen=$total;
        return $total;
    }
    public function tanhInverse()
    {
        $result = $this->igual();
        $total = atanh($result); //en radianes
        $this->screen=$total;
        return $total;
    }
    public function sec()
    {
        $total = 1 / $this->cos();
        $this->screen=$total;
    }
    public function sech()
    {
        $total = 1 / $this->cosh();
        $this->screen=$total;
    }
    public function secInverse()
    {
        $this->screen = 1.0 / $this->screen;
        $total = $this->cosInverse() * 180 / pi();
        $this->screen=$total;
    }
    public function sechInverse()
    {
        $this->screen = 1.0 / $this->screen;
        $total = $this->coshInverse();
        $this->screen=$total;
    }
    public function csc()
    {
        $total = 1 / $this->sin();
        $this->screen=$total;
    }
    public function csch()
    {
        $total = 1 / $this->sinh();
        $this->screen=$total;
    }
    public function cscInverse()
    {
        $this->screen = 1.0 / $this->screen;
    }
    public function cschInverse()
    {
        $this->screen = 1.0 / $this->screen;
        $total = $this->sinhInverse();
        $this->screen=$total;
    }
    public function cot()
    {
        $total = 1 / $this->tan();
        $this->screen=$total;
    }
    public function coth()
    {
        $total = 1 / $this->tanh();
        $this->screen=$total;
    }
    public function cotInverse()
    {
        $this->screen = 1.0 / $this->screen;
    }
    public function cothInverse()
    {
        $this->screen = 1.0 / $this->screen;
        $total = $this->tanhInverse();
        $this->screen=$total;
    }







    public function FE()
    {
        if ($this->notacionCientifica) {
            $this->notacionCientifica = false;
        } else {
            $this->notacionCientifica = true;
        }
    }
    public function MC()
    {
        $this->memory = "";
    }
    public function MR()
    {
        $this->screen = $this->memory;
    }
    public function MS()
    {
        $this->memory = $this->screen;
    }


    public function pulsadoCientifico($boton)
    {

        if($this->equalPressed==true){
            $this->screen="";
        }
        $this->equalPressed=false;
        if(is_numeric($boton)){
            $this->digitos($boton);
        }
        //echo $boton;
        switch($boton){
            case "DEG":

                break;
            case "F-E":
            $this->FE();
                break;
            case"MC":
            $this->MC();
                break;
            case"MR":
            $this->MR();
                break;
            case"M+":
            $this->mMas();
                break;
            case"M-":
            $this->mMenos();
                break;
            case"MS":
            $this->MS();
                break;
            case"2ⁿᵈ":

                break;
            case"sin":
            $this->sin();
                break;
            case"cos":
            $this->cos();
                break;
            case"tan":
            $this->tan();
                break;
            case"hyp":

                break;
            case"sec":
            $this->sec();
                break;
            case"csc":
            $this->csc();
                break;
            case"cot":
            $this->cot();
                break;
            case"|x|":
            $this->abs();
                break;
            case"⌊x⌋":
            $this->floor();
                break;
            case"⌈x⌉;":
            $this->ceil();
                break;
            case"rand":
            $this->rand();
                break;
            case"dms":
            $this->dms();
                break;
            case"deg":
            $this->degrees();
                break;
            case"2ⁿᵈ":

                break;
            case"π":
            $this->pi();
                break;
            case"e":
            $this->e();
                break;
            case"C":
            $this->borrar();
                break;
            case"⌫":
            $this->borrarUnNumero();
                break;
            case"x²":
            $this->powerTwoOf();
                break;
            case"1/x":
            $this->inverse();
                break;
            case"|x|":
            $this->abs();
                break;
            case"exp":
            $this->exp();
                break;
            case"mod":
            $this->mod();
                break;
            case"²√x":
            $this->root(2);
                break;
            case"(":
            $this->openPar();
                break;
            case")":
            $this->closePar();
                break;
            case"n!":
            $this->fact();
                break;
            case"÷":
            $this->division();
                break;
            case"xʸ":
            $this->XtoTheY();
                break;
            case"×":// x
            $this->multiplicacion();
                break;
            case"10ˣ":
            $this->tenToThePower();
                break;
            case "−":
            $this->resta();
                break;
            case "log":
            $this->log();
                break;
            case "+":
            $this->suma();
                break;
            case "ln":
            $this->ln();
                break;
            case "⁺/₋":
            $this->negate();
                break;
            case ",":
            $this->punto();
                break;
            case "=":
                $this->igual();
                break;
            default:
                break;
        }
        
    }
}

if (isset($_SESSION['CalculadoraCientifica'])) {
    $calculadora = $_SESSION['CalculadoraCientifica'];
} else {
    $calculadora = new CalculadoraCientifica();
    $_SESSION['CalculadoraCientifica'] = $calculadora;
}


if (count($_POST) > 0) {
    $boton = $_POST['boton'];
    $calculadora->pulsadoCientifico($boton);
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
    <link rel="stylesheet" type="text/css" href="CalculadoraCientifica.css">
    <title>Calculadora Cientifica</title>
</head>

<body>
    <header>
        <h1>
            Calculadora Cientifica
        </h1>
    </header>
    <main>
        <form action="#" method="post" name="calculadoraForm">
            <fieldset>
                <input type="text" title="screen" readonly="readonly" value="<?php echo $calculadora->getScreen(); ?>"/>
            </fieldset>
            <fieldset>
                <input type="submit" name="deg" value="DEG" />
                <input type="submit" name="fe" value="F-E" />

                <input type="submit" value="MC" />
                <input type="submit" value="MR" />
                <input type="submit" value="M+" />
                <input type="submit" value="M-" />
                <input type="submit" value="MS" />


            </fieldset>
            <fieldset>
                <ul>
                    <li>Trigonometria <sup>⌄</sup>
                        <ul>
                            <li><input type="submit" name="boton" value="2ⁿᵈ" /></li>
                            <li><input type="submit" name="boton" value="sin" /></li>
                            <li><input type="submit" name="boton" value="cos" /></li>
                            <li><input type="submit" name="boton" value="tan" /></li>
                            <li><input type="submit" name="boton" value="hyp" /></li>
                            <li><input type="submit" name="boton" value="sec" /></li>
                            <li><input type="submit" name="boton" value="csc" /></li>
                            <li><input type="submit" name="boton" value="cot" /></li>
                        </ul>
                    </li>
                    <li>Funcion <sup>⌄</sup>
                        <ul>
                            <li><input type="submit" name="boton" value="|x|" /></li>
                            <li><input type="submit" name="boton" value="⌊x⌋" /></li>
                            <li><input type="submit" name="boton" value="⌈x⌉" /></li>
                            <li><input type="submit" name="boton" value="rand" /></li>
                            <li><input type="submit" name="boton" value="dms" /></li>
                            <li><input type="submit" value="deg" /></li>
                        </ul>
                    </li>
                </ul>
            </fieldset>
            <fieldset>
                <input type="submit" name="boton" name="twond" value="2ⁿᵈ" />
                <input type="submit" name="boton" value="π" />
                <input type="submit" name="boton" value="e" />
                <input type="submit" name="boton" value="C" value="C" />
                <input type="submit" name="boton" value="⌫" />

                <input type="submit" name="boton" name="powerTwoOf" value="x²" />
                <input type="submit" name="boton" value="1/x" />
                <input type="submit" name="boton" value="|x|" />
                <input type="submit" name="boton" value="exp" />
                <input type="submit" name="boton" value="mod" />

                <input type="submit" name="boton" name="root2" value="²√x" />
                <input type="submit" name="boton" value="(" />
                <input type="submit" name="boton" value=")" />
                <input type="submit" name="boton" value="n!" />
                <input type="submit" name="boton" value="÷" />

                <input type="submit" name="boton" name="xtothey" value="xʸ" />

                <input type="submit" name="boton" value="7" />
                <input type="submit" name="boton" value="8" />
                <input type="submit" name="boton" value="9" />
                <input type="submit" name="boton" value="×" />

                <input type="submit" name="boton" name="tenToThePower" value="10ˣ" />
                <input type="submit" name="boton" value="4" />
                <input type="submit" name="boton" value="5" />
                <input type="submit" name="boton" value="6" />
                <input type="submit" name="boton" value="−" />

                <input type="submit" name="boton" name="log" value="log" />
                <input type="submit" name="boton" value="1" />
                <input type="submit" name="boton" value="2" />
                <input type="submit" name="boton" value="3" />
                <input type="submit" name="boton" value="+" />

                <input type="submit" name="boton" name="ln" value="ln" />
                <input type="submit" name="boton" value="⁺/₋" />
                <input type="submit" name="boton" value="0" />
                <input type="submit" name="boton" value="," />
                <input type="submit" name="boton" value="=" />


            </fieldset>

        </form>
        <aside>

        </aside>
    </main>
</body>

</html>