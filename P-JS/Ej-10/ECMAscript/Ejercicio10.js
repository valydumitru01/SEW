"use strict"
class PreciosGas {
    constructor() {
        this.oil = new Object()
        this.oil.indicator = "ng1:com"
        this.oil.url = "https://api.tradingeconomics.com/markets/historical/" + this.oil.indicator+"?c=guest:guest&format=json"
        this.oil.error = "<h2>¡problemas! No puedo obtener información de <a href=\"https://tradingeconomics.com\">tradingeconomics.com</a></h2>"
        this.arrayDeValores = []
        this.arrayDeFechas=[] 
        this.cargarDatos()   
    }
    dibujarGrafico() {
        var i, max, min, h, html = '', css = '', data = this.arrayDeValores

        max = min = data[0];

        for (i = 0; i < data.length; i++) {
            if (max < data[i]) max = data[i];
            if (min > data[i]) min = data[i];
        }
        html+="<h2> Precios en dolares estadounidenses (USD) </h2>"
        html +="<ul>"
        for (i = 0; i < data.length; i++) {
            html += '<li name=' + data[i] + '><p name=\"dato\">' + data[i] + '</p><p name=\"fecha\">'+this.arrayDeFechas[i]+'</p></li>\n';
        }
        html +="</ul>"
        $("section[name=grafica]").html(html)
        for (i = 1; i < data.length+1; i++) {
            console.log(data[i-1])
            h = Math.round(20+100 * ((Math.sqrt(data[i-1]**2)-min) / (max / 2)));
            $('li:nth-child(' + i + ')').css({ "height": h + "%", "left": (8.5 * i)  + '%' })
        }

    }
    cargarDatos() {
        var self = this
        $.ajax({
            dataType: "json",
            url: self.oil.url,
            method: 'GET',
            success: function (data) {
                self.seleccionarDatos(data)
                self.dibujarGrafico()
            },
            error: function () {
                document.write(self.oil.error);
            }
        });
    }
    seleccionarDatos(datos) {
        console.log(JSON.stringify(datos, null, 1))
        Object.values(datos).forEach(value => {

            this.arrayDeValores.unshift(value["High"])
            this.arrayDeFechas.unshift(value["Date"])
        });
        
    }
}
var oil = new PreciosGas()
