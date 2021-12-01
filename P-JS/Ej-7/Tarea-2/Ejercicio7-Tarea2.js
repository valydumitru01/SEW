
"use strict"
class Ejercicio7{
    constructor(){

    }
    Hide(elem) {
        $(elem).hide()
    }
    Modificar(elem) {
        $(elem).html($(elem).html()+" MODIFICACION")
    }
}

var ej7=new Ejercicio7()