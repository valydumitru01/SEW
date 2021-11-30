"use strict"
class PreciosGas {
    constructor() {
        this.gas = new Object()
        this.gas.indicator = "ng1:com"
        this.gas.url = "https://api.tradingeconomics.com/markets/historical/" + this.gas.indicator+"?c=guest:guest&format=json"
        this.gas.error = "<h2>¡problemas! No puedo obtener información de <a href=\"https://tradingeconomics.com\">tradingeconomics.com</a></h2>"
        this.arrayDeValores = []
        this.arrayDeFechas=[] 
        this.canvas = document.querySelector('canvas');
        this.ctx = this.canvas.getContext('2d');
        this.cargarDatos()   
    }
    dibujarGrafico() {
        var i, max, min, h, html = '', data = this.arrayDeValores

        max = min = data[0];

        for (i = 0; i < data.length; i++) {
            if (max < data[i]) max = data[i];
            if (min > data[i]) min = data[i];
        }
        for (i = 1; i < data.length+1; i++) {
            h = Math.round(20+100 * ((Math.sqrt(data[i-1]**2)-min) / (max / 2)));
            this.canvasBar(i, h,this.arrayDeFechas[i-1])
        }
        this.canvasText()

    }
    
    cargarDatos() {
        var self = this
        $.ajax({
            dataType: "json",
            url: self.gas.url,
            method: 'GET',
            success: function (data) {
                self.seleccionarDatos(data)
                self.dibujarGrafico()
            },
            error: function () {
                document.write(self.gas.error);
            }
        });
    }
    seleccionarDatos(datos) {
        Object.values(datos).forEach(value => {

            this.arrayDeValores.unshift(value["High"])
            this.arrayDeFechas.unshift(value["Date"])
        });
        
    }
    canvasBar(x, h, fecha) {
        h=h*10
        var numBarras=this.arrayDeValores.length
        this.ctx.fillStyle = "rgb(33, 141, 48)"
        console.log(this.canvas)
        this.ctx.fillRect(x*this.canvas.width*0.94/(numBarras)-this.canvas.width*0.04 , this.canvas.height*0.9-h , this.canvas.width/(numBarras*2) , h );
        this.ctx.lineJoin = 'bevel';
        this.ctx.lineWidth = 5;
        this.ctx.strokeStyle = 'rgb(171, 255, 46)';
        this.ctx.strokeRect(x*this.canvas.width*0.94/(numBarras) -this.canvas.width*0.04, this.canvas.height*0.9-h , this.canvas.width/(numBarras*2) , h );
        
        this.ctx.fillStyle = "rgb(171, 255, 46)";
        this.ctx.font = 'bold 2em Courier New';
        this.ctx.fillText(""+this.arrayDeValores[x-1]+"$", x*this.canvas.width*0.93/(numBarras) -this.canvas.width*0.05, this.canvas.height*0.8-h**1.01);
        this.ctx.lineWidth = 1;
        this.ctx.strokeStyle = 'rgb(33, 141, 48)';
        this.ctx.strokeText(""+this.arrayDeValores[x-1]+"$", x*this.canvas.width*0.93/(numBarras) -this.canvas.width*0.05, this.canvas.height*0.8-h**1.01)
    
        this.ctx.fillStyle = "rgb(171, 255, 46)";
        this.ctx.font = 'bold 1em Courier New';
        this.ctx.fillText(fecha, x*this.canvas.width*0.93/(numBarras) -this.canvas.width*0.05, this.canvas.height*0.95);
        this.ctx.lineWidth = 1;
        this.ctx.strokeStyle = 'rgb(33, 141, 48)';
        this.ctx.strokeText(fecha, x*this.canvas.width*0.93/(numBarras) -this.canvas.width*0.05, this.canvas.height*0.95)
    
    }
    canvasText(){
        this.ctx.lineWidth = 40;
        this.ctx.strokeStyle = 'green';
        this.ctx.strokeRect(0, 0 ,this.canvas.width , this.canvas.height);


        this.ctx.fillStyle = "rgb(171, 255, 46)";
        this.ctx.font = 'bold 3.5em Courier New';
        this.ctx.fillText("Precio en USD ($)", this.canvas.width/20, this.canvas.height/10);
        this.ctx.lineWidth = 2;
        this.ctx.strokeStyle = 'rgb(33, 141, 48)';
        this.ctx.strokeText("Precio en USD ($)",this.canvas.width/20, this.canvas.height/10)
    
    }
}
var oil = new PreciosGas()
