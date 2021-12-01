"use strict"
class Calculadora {

    constructor() {
        this.screen = ""
        this.operacion=""
        this.num1=""
        this.num2=""
        this.memory = ""
        this.mrcOnce = false
    }

    digitos(digit) {
        this.mrcOnce = false
        this.num2=this.num2.concat(digit)
        this.screen=this.screen.concat(digit)
        
        escribir(this.screen)
    }
    punto() {
        this.mrcOnce = false
        
        if (this.num2 == ""){
            this.num1="0."
            this.screen=this.screen.concat(this.num1)
            
        } else if (this.num1.indexOf('.') > -1) {
            return
        }
        else {
            this.num1="."
            this.screen=this.screen.concat(this.num1)
        }
        escribir(this.screen)
    }
    suma() {
        this.mrcOnce = false
        this.operacion="+"
        this.screen=this.screen.concat(this.operacion)
        this.num1=this.num2
        this.num2=""
        escribir(this.screen)
    }
    resta() {
        this.mrcOnce = false
        this.operacion="-"
        this.screen=this.screen.concat(this.operacion)
        this.num1=this.num2
        this.num2=""
        escribir(this.screen)
    }
    multiplicacion() {
        this.mrcOnce = false
        this.operacion="*"
        this.screen=this.screen.concat(this.operacion)
        this.num1=this.num2
        this.num2=""
        escribir(this.screen)
    }
    division() {
        this.mrcOnce = false
        this.operacion="/"
        this.screen=this.screen.concat(this.operacion)
        this.num1=this.num2
        this.num2=""
        escribir(this.screen)
    }
    mrc() {
        if (this.mrcOnce == true){
            borrar()
            escribir(this.screen)
        }
        else {
            this.mrcOnce = true
            this.num2=this.memory
            this.screen=this.num2
            this.memory=""
            escribir(this.screen)
        }
    }
    mMenos() {
        this.mrcOnce = false

        this.num1=this.memory
        this.operacion="-"
        this.memory=""
        
        escribir(this.igual())
    }
    mMas() {
        this.mrcOnce = false

        this.num1=this.memory
        this.operacion="+"
        this.memory=""

        escribir(this.igual())
    }
    borrar() {
        this.mrcOnce = false
        this.num1=""
        this.num2=""
        this.operacion=""
        this.screen=""
        escribir(this.screen)
    }
    igual() {
        this.mrcOnce = false
        var result
        try{
            result= eval(Number(this.num1)+""+this.operacion+""+Number(this.num2))
        }catch(err){
            escribir( "Error: "+err )
        }
        
        this.memory=""+result
        this.screen=""
        this.num1=""
        this.num2=""
        escribir(""+result)

    }


}

function escribir(content){
    document.getElementsByTagName("input")[0].value =content
}
document.addEventListener('keydown', (event) => {
    const keyName = event.key;
    switch(keyName){
        case "0":
        case "1":
        case "2":
        case "3":
        case "4":
        case "5":
        case "6":
        case "7":
        case "8":
        case "9":
            calculadora.digitos(keyName)
            break;
        case ".":
            calculadora.punto()
            break
        case "/":
            calculadora.division()
            break
        case "*":
            calculadora.multiplicacion()
            break
        case "-":
            calculadora.resta()
            break
        case "+":
            calculadora.suma()
            break
        case "Enter":
            calculadora.igual()
            break
        case "m":
            calculadora.mrc()
            break
        case "n":
            calculadora.mMenos()
            break
        case "b":
            calculadora.mMas()
            break
        
    }
  });

  var calculadora = new Calculadora();