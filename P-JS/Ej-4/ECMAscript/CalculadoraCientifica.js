

"use strict"
class CalculadoraCientifica extends Calculadora {

    constructor (){
        super()
    }
    abrirParentesis(){
        this.screen=this.screen+"("
        return this.screen
    }
    cerrarParentesis(){
        this.screen=this.screen+")"
        return this.screen
    }
    igual(){
        this.mrcOnce = false
        var result
        try{
            result= eval(this.formatForEval(this.screen))
        }catch(err){
            return "Error: "+err
        }
        
        this.memory=""+result
        this.screen=""
        this.num1=""
        this.num2=""
        return ""+result
    }
    pi(){
        this.screen=this.screen+'\u03C0'
        return this.screen
    }
    e(){
        this.screen+="e"
        return this.screen
    }

    formatForEval(operacion){
        console.log("Operacion en pantalla: "+operacion)
        let operacionConNumber=""
        let numeroEncontrado=false
        let parentesisCerrado=false
        for (let i in operacion) {
            let char=operacion[i]
            console.log("Char: "+char)
            if(char=='\u03C0'){
                operacionConNumber+="Math.PI"
            }else if(char=='e'){
                operacionConNumber+="Math.E"
            }
            else if(!isNaN(char) && numeroEncontrado==false){
                parentesisCerrado=false
                operacionConNumber+="Number("+char
                numeroEncontrado=true
            }
            else if(!isNaN(char) && numeroEncontrado==true){
                operacionConNumber+=char
            }
            else if(parentesisCerrado==false){
                parentesisCerrado=true
                numeroEncontrado=false
                operacionConNumber+=")"+char;
            }
            else{
                operacionConNumber+=char;
            }
            
        }
        console.log("Operacion con number: "+operacionConNumber);
        return operacionConNumber
    }

}
var calculadora = new CalculadoraCientifica();

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

function openPar(){
    escribir( calculadora.abrirParentesis())
}
function closePar(){
    escribir( calculadora.cerrarParentesis())
}
function pi(){
    escribir( calculadora.pi())
}
function e() {
    escribir(calculadora.e())
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