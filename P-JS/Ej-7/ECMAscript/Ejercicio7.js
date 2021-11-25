
function Hide(elem) {
    $(elem).hide()
}

function DesplegarEjercicios(num) {

    $("h3[name="+num+"] ~ ul").toggle(300)
}
function CambiarBorde(num) {
    $("main section:nth-child("+num+")").click(function(){
        $(this).css("border-color", "steelblue");
      });
}
function introducirCampo(num) {
    var content=$("main section:nth-child(3) ol li:nth-child("+num+") form input[type=text]").val()
    console.log("Contenido: "+content)

    concatened=$("main section:nth-child(3) ol li:nth-child("+num+") ul").html()
    if(!concatened.includes(content))
        $("main section:nth-child(3) ol li:nth-child("+num+") ul").html(concatened+"<li onclick=\"eliminable(this)\">"+content+"</li>")
}
function eliminable(elem) {
    $(elem).remove()
}
function iterarSobreHtml() {
    let textarea=""
    $("*").each(function name(index) {
        console.log("Element "+index+" Tipo: "+$(this).prop('nodeName')+" Elemento Padre: " +$(this).parent().prop('nodeName'))
        textarea+="Element "+index+" Tipo: "+$(this).prop('nodeName')+" Elemento Padre: " +$(this).parent().prop('nodeName')+"\n"
    })
    $("textarea").val(textarea)
}
function calcularFilasColumnasTabla() {
    numFilas=0
    numCols=0
    
    $("section table tr").each(function sumarFilas(j) {
        numFilas=j+1
        let hijosTh=$(this).children("th").length

        console.log(hijosTh)
        if(hijosTh==0)
            $(this).html($(this).html()+"<td> Fila:"+j+"</td>")
    })

    let initial=$("section table tbody").html()
    initial+="<tr>"
    
    
    $("section table tbody tr th").each(function sumarColumnas(i) {
        numCols=i+1
        initial+="<td>"+i+"</td>"
    })

    initial+="</tr>"
    
    $("section table tbody").html(initial)
    console.log("Numero de filas de la tabla: "+numFilas)
    console.log("Numero de columnas de la tabla: "+numCols)
    
}
