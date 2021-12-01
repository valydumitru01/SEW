
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
    introducirCampo(num) {
        var content=$("main section:nth-child(3) ol li:nth-child("+num+") form input[type=text]").val()
        console.log("Contenido: "+content)
    
        var concatened=$("main section:nth-child(3) ol li:nth-child("+num+") ul").html()
        if(!concatened.includes(content))
            $("main section:nth-child(3) ol li:nth-child("+num+") ul").html(concatened+"<li onclick=\"ej7.eliminable(this)\">"+content+"</li>")
    }
    eliminable(elem) {
        $(elem).remove()
    }
    iterarSobreHtml() {
        let textarea=""
        $("*").each(function name(index) {
            console.log("Element "+index+" Tipo: "+$(this).prop('nodeName')+" Elemento Padre: " +$(this).parent().prop('nodeName'))
            textarea+="Element "+index+" Tipo: "+$(this).prop('nodeName')+" Elemento Padre: " +$(this).parent().prop('nodeName')+"\n"
        })
        $("textarea").val(textarea)
    }
    
}

var ej7=new Ejercicio7()