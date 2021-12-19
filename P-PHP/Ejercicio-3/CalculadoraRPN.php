<?php
session_start();
class PilaLIFO
{

    protected $pila;
    protected $tamagnoMax; //número de elementos máximo que se pueden almacenar en la pila
    public function __construct($tamagnoMax = 5)
    {
        // Crea la pila con un array con 5 elementos como máximo (valor por defecto)
        $this->pila = array();
        // El tamaño es el número máximo de elementos en la pila
        $this->tamagnoMax = $tamagnoMax;
    }

    public function apilar($elemento)
    {
        // precondición: Comprueba que la pila no supere el tamaño máximo
        if ($this->longitud() < $this->tamagnoMax) {
            // inserta un elemento en la cabeza del array
            array_unshift($this->pila, $elemento);
        } else {
            return $this->pila;
            throw new RunTimeException('¡Pila llena: no hay espacio para apilar más elementos!');
        }
    }

    public function desapilar()
    {
        //precondición: comprueba que la pila no esté vacía    
        if ($this->vacia()) {
            return [];
            
        } else {
            // desapila un elemento del inicio del array
            return array_shift($this->pila);
        }
    }

    public function getTamagnoMax()
    {
        //devuelve el número de elementos máximo que se pueden almacenar en la pila
        return $this->tamagnoMax;
    }

    public function longitud()
    {
        //devuelve el número de elementos de la pila
        return count($this->pila);
    }

    public function ultimo()
    {
        return current($this->pila);
    }

    public function vacia()
    {
        return empty($this->pila);
    }
    public function getFromIndex($i){
        return $this->pila[$i];
    }
    public function ver()
    {
        for ($i=0; $i < sizeof($this->pila); $i++) { 
            "<li>".
            "".$i.": ".$this->pila[$i].
            "</li>";
        }
        
    }
}

class CalculadoraRPN
{
    protected $screen;
    protected $currentNum;
    protected PilaLIFO $stack;
    protected $activatedShift;
    public function __construct()
    {
        $this->screen = "";
        $this->stack = new PilaLIFO(999);
        $this->activatedShift = false;
    }
    public function digitos($digit)
    {
        $this->currentNum = $this->currentNum . $digit;
        $this->screen = $this->screen . $digit;
    }
    public function borrarUnNumero()
    {
        $this->screen = substr($this->screen, 0, strlen($this->screen) - 1);
    }
    public function saveResult($result)
    {
        $this->stack->apilar($result . "");
        $this->drawStack();
    }
    public function suma()
    {
        $result = ((float)$this->stack->desapilar()) + ((float)$this->stack->desapilar());
        $this->saveResult($result);
    }
    public function resta()
    {
        $num = (float)($this->stack->desapilar());
        $result = ((float)($this->stack->desapilar()) - $num);
        $this->saveResult($result);
    }
    public function multiplicacion()
    {
        $result = ((float)$this->stack->desapilar()) * ((float)$this->stack->desapilar());
        $this->saveResult($result);
    }
    public function division()
    {
        $num = (float)($this->stack->desapilar());
        if($num!=0)
            $result = ((float)$this->stack->desapilar()) / $num;
        else{
            return 0;
        }
        $this->saveResult($result);
    }
    public function punto()
    {
        $this->screen = $this->screen . '.';
    }
    public function sin()
    {
        $result = sin($this->stack->desapilar()); //en radianes
        $this->saveResult($result);
    }
    public function cos()
    {
        $result = cos($this->stack->desapilar()); //en radianes
        $this->saveResult($result);
    }
    public function tan()
    {
        $result = tan($this->stack->desapilar()); //en radianes
        $this->saveResult($result);
    }
    public function asin()
    {
        $result = asin($this->stack->desapilar()); //en radianes
        $this->saveResult($result);
    }
    public function acos()
    {
        $result = acos($this->stack->desapilar()); //en radianes
        $this->saveResult($result);
    }
    public function atan()
    {
        $result = atan($this->stack->desapilar()); //en radianes
        $this->saveResult($result);
    }
    public function root()
    {
        $result = pow($this->stack->desapilar(), 1 / 2); //en radianes
        $this->saveResult($result);
    }
    public function powerTwoOf()
    {
        $result = pow($this->stack->desapilar(), 2); //en radianes
        $this->saveResult($result);
    }
    public function borrar(){
        $this->screen="";
        $this->stack=new PilaLIFO(999);
    }

    public function enter()
    {
        $this->stack->apilar((float)($this->currentNum));
        $this->currentNum="";

        $this->drawStack($this->stack->longitud() + 2);
    }


    public function drawStack()
    {   
        $s="";
        for ($i=$this->stack->longitud()-1; $i >= 0 ; $i--) { 
            $s .= "<li>" . $i . ": " . $this->stack->getFromIndex($i). "</li>";
        }
        $this->screen = $s;
    }

    public function getScreen()
    {
        return $this->screen;
    }
    public function getStack()
    {
        return $this->stack;
    }
    public function pulsadoRPN($boton)
    {
        if (is_numeric($boton)) {
            $this->digitos($boton);
        }
        switch ($boton) {
            case ".":
                $this->punto();
                break;
            case ",":
                $this->punto();
                break;
            case "/":
                $this->division();
                break;
            case "*":
                $this->multiplicacion();
                break;
            case "-":
                $this->resta();
                break;
            case "+":
                $this->suma();
                break;
            case "Enter":
                $this->enter();
                break;
            case "⌫":
                $this->borrarUnNumero();
                break;
            case "SIN":
                $this->sin();
                break;
            case "COS":
                $this->cos();
                break;
            case "TAN":
                $this->tan();
                break;
            case "x²":
                $this->powerTwoOf();
                break;
            case "²√x":
                $this->root();
                break;
            case "C":
                $this->borrar();
            default:
                break;
        }
    }
}

if (isset($_SESSION['CalculadoraRPN'])) {
    $calculadoraRPN = $_SESSION['CalculadoraRPN'];
} else {
    $calculadoraRPN = new CalculadoraRPN();
    $_SESSION['CalculadoraRPN'] = $calculadoraRPN;
}


if (count($_POST) > 0) {
    $boton = $_POST['boton'];
    $calculadoraRPN->pulsadoRPN($boton);


}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Calculadora RPN">
    <meta name="keywords" content="ECMAscript,calculadora,RPN,Valentin">
    <meta name="author" content="Valentin">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="CalculadoraRPN.css">
    <title>Calculadora RPN</title>
</head>

<body>
    <header>
        <h1>
            Calculadora RPN
        </h1>
    </header>
    <main>
        <form action="#" method="post" name="calculadoraForm">
            <fieldset>
                <ul>
                    <?php echo $calculadoraRPN->getScreen(); ?>
                </ul>
            </fieldset>

            <fieldset>
                <input type="submit" name="boton" value="7" />
                <input type="submit" name="boton" value="8" />
                <input type="submit" name="boton" value="9" />
                <input type="submit" name="boton" value="⌫" />

                <input type="submit" name="boton" value="4" />
                <input type="submit" name="boton" value="5" />
                <input type="submit" name="boton" value="6" />
                <input type="submit" name="boton" value="+" />

                <input type="submit" name="boton" value="1" />
                <input type="submit" name="boton" value="2" />
                <input type="submit" name="boton" value="3" />
                <input type="submit" name="boton" value="-" />

                <input type="submit" name="boton" value="0" />
                <input type="submit" name="boton" value="." />
                <input type="submit" name="boton" value="*" />
                <input type="submit" name="boton" value="/" />


                <input type="submit" name="boton" value="SIN">
                <input type="submit" name="boton" value="COS">
                <input type="submit" name="boton" value="TAN">

                <input type="submit" name="boton" value="²√x" />
                <input type="submit" name="boton" value="x²" />
                <input type="submit" name="boton" value="C" />
                <input type="submit" name="boton" value="Enter" />




            </fieldset>

        </form>
        <aside>

        </aside>
    </main>
</body>

</html>