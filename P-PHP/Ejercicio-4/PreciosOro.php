<?php
session_start();
class preciosOro
{
    protected $apiKey = "access_key=7zgel593714vqgtgds7a4piygl2zyq88od64wg95314w89z0jwb2d8nq3x3b";
    protected $currency = "&base=EUR";
    protected $symbol = "&symbols=XAU";
    protected $from = "&from=EUR";
    protected $to = "&to=XAU";
    protected $type = "convert?";
    protected $linkBase = "https://www.metals-api.com/api/";
    protected $fullUrl;
    protected $datos;

    protected $conversion;
    protected $tuDinero;
    public function __construct()
    {

        $this->fullUrl = $this->linkBase . $this->type . $this->apiKey . $this->from . $this->to;
        $this->datos = file_get_contents($this->fullUrl);
        $this->datos = json_decode($this->datos, JSON_PRETTY_PRINT);
        if ($this->datos == null) {
            echo "<h3>Error en el archivo JSON recibido</h3>";
        } else {
        }

        $this->conversion = (float)$this->datos["result"];
    }
    public function getConversion()
    {
        return $this->conversion * $this->tuDinero;
    }
    public function setTuDinero($tuDinero)
    {
        $this->tuDinero = $tuDinero;
    }
    public function mostrarLingotes()
    {
        echo "<section>";
        echo "<h2>Tu cantidad de oro: </h2>";
        if ($this->conversion * $this->tuDinero < 1) {
            for ($i = 0; $i < (int)($this->conversion * $this->tuDinero * 100); $i++) {
                echo "<img src=\"Gold_nugget.png\" width=\"30\" height=\"30\" alt=\"Imagen de nugget de oro\">";
            }
        }
        for ($i = 0; $i < (int)($this->conversion * $this->tuDinero); $i++) {
            echo "<img src=\"Lingote_de_oro.png\" width=\"30\" height=\"30\" alt=\"Imagen de lingote de oro\">";
        }
        echo "</section>";
    }
}



if (isset($_SESSION['PreciosOro'])) {
    $preciosOro = $_SESSION['PreciosOro'];
} else {
    $preciosOro = new preciosOro();
    $_SESSION['PreciosOro'] = $preciosOro;
}


if (count($_POST) > 0) {
    $tuDinero = $_POST['tuDineroName'];
    $preciosOro->setTuDinero($tuDinero);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Precios ORO">
    <meta name="keywords" content="ORO, precios, servicios web, valentin">
    <meta name="author" content="Valentin">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="PreciosOro.css">
    <title>Precios acciones XAU</title>
</head>

<body>
    <header>
        <h1>
            ¿Cuantas acciones de XAU puedo comprar?
        </h1>
    </header>
    <main>
        <section>
            <h2>Conversor de euros a XAU</h2>
            <p>Las acciones de XAU es el simbolo del <a href="https://en.wikipedia.org/wiki/Philadelphia_Gold_and_Silver_Index">Simbolo de oro y plata de Filadeflia</a>. Es un indice que agrupa 30 diferentes compañias de mineria de metales preciosos (principalmente oro y plata)</p>
        </section>
        <form action="#" method="post" name="preciosOroForm">
            <fieldset>
                <label for="tuDineroId">Dinero que quiero invertir: </label><input type="number" name="tuDineroName" id="tuDineroId" step="0.01"><input type="submit" value="Convertir">
            </fieldset>
            <img src="right_arrow_yellow.png" alt="flecha hacia la derecha">
            <fieldset>
                <label for="conversionAOro">Acciones de oro que puedo comprar: </label><input type="text" name="convertido" id="conversionAOro" readonly="readonly" value="<?php echo $preciosOro->getConversion(); ?>">
            </fieldset>
        </form>  
        <?php $preciosOro->mostrarLingotes(); ?>
    </main>
</body>

</html>