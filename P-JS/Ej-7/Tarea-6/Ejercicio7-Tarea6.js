
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
    calcularFilasColumnasTabla() {
        var numFilas=0
        var numCols=0
        
        $("section table tr").each(function sumarFilas(j) {
            numFilas=j+1
            let hijosTh=$(this).children("th").length
    
            console.log(hijosTh)
            if(hijosTh==0)
                $(this).html($(this).html()+"<td>"+j+"</td>")
            else
                $(this).html($(this).html()+"<th>num</th>")
        })
    
        let initial=$("section table tbody").html()
        
        initial+="<tr>"
        $("section table tbody tr th").each(function sumarColumnas(i) {

            if(i==0)
                initial+="<td>num</td>"
            else
                initial+="<td>"+i+"</td>"
        })
    
        initial+="</tr>"
        
        $("section table tbody").html(initial)

        
    }
}

var ej7=new Ejercicio7()