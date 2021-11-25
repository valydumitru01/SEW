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
        
        return this.screen
    }
    punto() {
        this.mrcOnce = false
        
        if (this.screen == ""){
            this.num1="0."
            this.screen=this.screen.concat(this.num1)
        }
        else
            this.num1="."
            this.screen=this.screen.concat(this.num1)
        return this.screen
    }
    suma() {
        this.mrcOnce = false
        this.operacion="+"
        this.screen=this.screen.concat(this.operacion)
        this.num1=this.num2
        this.num2=""
        return this.screen
    }
    resta() {
        this.mrcOnce = false
        this.operacion="-"
        this.screen=this.screen.concat(this.operacion)
        this.num1=this.num2
        this.num2=""
        return this.screen
    }
    multiplicacion() {
        this.mrcOnce = false
        this.operacion="*"
        this.screen=this.screen.concat(this.operacion)
        this.num1=this.num2
        this.num2=""
        return this.screen
    }
    division() {
        this.mrcOnce = false
        this.operacion="/"
        this.screen=this.screen.concat(this.operacion)
        this.num1=this.num2
        this.num2=""
        return this.screen
    }
    mrc() {
        if (this.mrcOnce == true){
            borrar()
            return this.screen
        }
        else {
            this.mrcOnce = true
            this.num2=this.memory
            this.screen=this.num2
            this.memory=""
            return this.screen
        }
    }
    mMenos() {
        this.mrcOnce = false

        this.num1=this.memory
        this.operacion="-"
        this.memory=""
        
        return this.igual()
    }
    mMas() {
        this.mrcOnce = false

        this.num1=this.memory
        this.operacion="+"
        this.memory=""

        return this.igual()
    }
    borrar() {
        this.mrcOnce = false
        this.memory=""
        this.num1=""
        this.num2=""
        this.operacion=""
        this.screen=""
        return this.screen
    }
    igual() {
        this.mrcOnce = false
        var result
        try{
            result= eval(Number(this.num1)+""+this.operacion+""+Number(this.num2))
        }catch(err){
            return "Error: "+err
        }
        
        this.memory=""+result
        this.screen=""
        this.num1=""
        this.num2=""
        return ""+result

    }


}
var calculadora = new Calculadora();

function digitos(digit) {
    escribir(calculadora.digitos(digit))
}

function punto() {
    escribir(calculadora.punto())
}

function suma() {

    escribir(calculadora.suma())
}

function resta() {
    escribir(calculadora.resta())
}

function multiplicacion() {
    escribir(calculadora.multiplicacion())
}

function division() {
    escribir(calculadora.division())
}

function mrc() {
    escribir(calculadora.mrc())
}

function mMenos() {
    escribir(calculadora.mMenos())
}

function mMas() {
    escribir(calculadora.mMas())
}

function borrar() {
    escribir(calculadora.borrar())
}

function igual() {
    escribir( calculadora.igual())
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
        escribir(calculadora.digitos(keyName))
        break;
        case ".":
            escribir(calculadora.punto())
        break
        case "/":
            escribir(calculadora.division())
            break
        case "*":
            escribir(calculadora.multiplicacion())
        break
        case "-":
            escribir(calculadora.resta())
        break
        case "+":
            escribir(calculadora.suma())
        break
        case "=":
            escribir(calculadora.igual())
        break
        case "m":
            escribir(calculadora.mrc())
        break
        case "n":
            escribir(calculadora.mMenos())
        break
        case "b":
            escribir(calculadora.mMas())
        break
        
    }
  });