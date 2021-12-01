"use strict"

class CalculadoraEstadistica extends CalculadoraRPN {

    constructor(){
        super()
        
        this.mapX = new Map()
        this.mapY = new Map()

        this.stackY = []
        this.introducirY = false
        this.map = this.mapX
        this.variable = 'x'
        this.screenY = ""
    }
    x(){
        this.introducirY = false
        this.map = this.mapX
        this.variable = 'x'
    }
    y(){
        this.introducirY = true
        this.map = this.mapY
        this.variable = 'y'
    }
    digitos(n){
        if (!this.introducirY){
            super.digitos(n)
        } else {
            this.screenY=this.screenY.concat(n)
            this.escribirY(this.screenY)
        }
    }
    
    borrarUnNumero(){
        if (!this.introducirY){
            this.screen=this.screen.substring(0,this.screen.length-1)
            this.escribir(this.screen)
        } else {
            this.screenY=this.screenY.substring(0,this.screenY.length-1)
            this.escribirY(this.screenY)
        }

    }

    getStack(v){
        if (v=='x'){
            return this.stack
        } else {
            return this.stackY
        }
    }
    getMap(v){
        if (v=='x'){
            return this.mapX
        } else {
            return this.mapY
        }
    }

    media(v,mostrar){
        var stack = this.getStack(v)
        
        var suma = 0
        for (var i in stack) {
            suma+=parseFloat(stack[i])
        }
        var media = suma/stack.length
        if (mostrar)
            this.mostrar(v,"Media",media)
        
        return media
    }
    
    moda(v){
        var map = this.getMap(v)
        
        var maxReps=0
        var moda = []
        for (var [key, value] of map){
            if (value > maxReps){
                maxReps = value
            }
        }
        for (var [key, value] of map){
            if (value == maxReps){
                moda.push(key)
            }
        }

        this.mostrar(v,"Moda",moda)
        
    }
    
    mediana(v){
        var orderedList = this.ordenar(v)
        var n = orderedList.length
        var mediana
        if (n % 2 == 0){
            mediana = (orderedList.at(n/2)+orderedList.at(n/2-1))/2
        } else {
            mediana = orderedList.at(n/2)
        }

        this.mostrar(v,"Mediana",mediana)
    }
    min(v){
        var orderedList = this.ordenar(v)
        this.mostrar(v,"Mínimo",orderedList.at(0))
    }
    max(v){
        var orderedList = this.ordenar(v)
        this.mostrar(v,"Máximo",orderedList.at(this.stack.length-1))
    }
    primerCuartil(v){
        var orderedList = this.ordenar(v)
        var n = this.stack.length
        var pos = (n+3.0)/4.0-1

        var a = pos % 1
        var q1 = orderedList.at(pos) + a * (orderedList.at(pos+1)-orderedList.at(pos))

        this.mostrar(v,"Q1",q1)
    }
    tercerCuartil(v){
        var orderedList = this.ordenar(v)
        var n = this.stack.length
        var pos = (3.0*n+1)/4.0-1

        var a = pos % 1
        var q3 = orderedList.at(pos) + a * (orderedList.at(pos+1)-orderedList.at(pos))
        
        this.mostrar(v,"Q3",q3)
    }
    ordenar(v){
        var stack = this.getStack(v)
        var orderedList = stack.slice()
        
        const l = orderedList.length;
        for (let i = 0; i < l; i++ ) {
            for (let j = 0; j < l - 1 - i; j++ ) {
                if ( orderedList[j] > orderedList[j + 1] ) {
                    [ orderedList[j], orderedList[j + 1] ] = [ orderedList[j + 1], orderedList[j] ];
                }
            }
        }
        
        return orderedList
    }
    varianzaPoblacional(v){
        
        var suma=0
        var stack
        var texto
        if (v == 'x'){
            stack = this.stack
        } else {
            stack = this.stackY
        }
        var n = stack.length
        for (var i in stack) {
            suma+=Math.pow(parseFloat(stack[i]),2)
        }
        var result = suma/n
        result=result-Math.pow(this.media(v,false),2)
        this.mostrar(v,"σ2",result)
        return result
    }
    varianzaMuestral(v){
        var suma=0
        var stack
        var texto
        if (v == 'x'){
            stack = this.stack
        } else {
            stack = this.stackY
        }
        var n = stack.length
        for (var i in stack) {
            suma+=Math.pow(parseFloat(stack[i]),2)
        }
        var result = suma/(n-1)
        result=result-(n*Math.pow(this.media(v,false),2))/(n-1)
        this.mostrar(v,"s2",result)
        return result
    }
    desviacionTipicaPoblacional(v){
        var result=Math.sqrt(this.varianzaPoblacional(v))
        this.mostrar(v,"σ",result)
    }
    desviacionTipicaMuestral(v){
        var result=Math.sqrt(this.varianzaMuestral(v))
        if (v == 'x')
            this.mostrar(v,"s",result)
        else
            this.mostrar(v,"s",result)
    }
    coeficienteCorrelacion(){
        var r
        if (this.stack.length != this.stackY.length){
            this.mostrar('xy',"Coeficiente de correlacion","No coincide la cantidad de 'x' y de 'y'")
            return
        } else if(this.stack.length == 0){
            this.mostrar('xy',"Coeficiente de correlacion",r)
            return
        }
        
        var mediaX = this.media('x',false);
        var mediaY = this.media('y',false);
            
        var numerador = 0
        var denominadorX = 0
        var denominadorY = 0
        for (var i in this.stack) {
            numerador+=(parseFloat(this.stack[i])-mediaX)*(parseFloat(this.stackY[i])-mediaY)
            denominadorX+=Math.pow((parseFloat(this.stack[i])-mediaX),2)
            denominadorY+=Math.pow((parseFloat(this.stackY[i])-mediaY),2)
        }
        var mult = denominadorX*denominadorY
        var denominador = Math.sqrt(mult,2)
        r = numerador/denominador

        this.mostrar('xy',"Coeficiente de correlacion",r)
        
    }
    covarianzaPoblacional(){
        var c
        if (this.stack.length != this.stackY.length){
            this.mostrar('xy',"Covarianza poblacional","No coincide la cantidad de 'x' y de 'y'")
            return
        } else if(this.stack.length == 0){
            this.mostrar('xy',"Covarianza poblacional",c)
            return
        }
        var mediaX = this.media('x',false);
        var mediaY = this.media('y',false);
        var mult = mediaX * mediaY
        var numerador = 0
        for (var i in this.stack) {
            numerador+=parseFloat(this.stack[i])*parseFloat(this.stackY[i])
        }
        numerador = numerador/this.stack.length

        c = numerador - mult
        this.mostrar('xy',"Covarianza poblacional",c)

    }
    covarianzaMuestral(){
        var c
        if (this.stack.length != this.stackY.length){
            this.mostrar('xy',"Covarianza muestral","No coincide la cantidad de 'x' y de 'y'")
            return
        } else if(this.stack.length == 0){
            this.mostrar('xy',"Covarianza muestral",c)
            return
        }
        var mediaX = this.media('x',false);
        var mediaY = this.media('y',false);
        var numerador = 0
        for (var i in this.stack) {
            numerador+=(parseFloat(this.stack[i])-mediaX)*(parseFloat(this.stackY[i])-mediaY)
        }
        c = numerador/(this.stack.length-1)
        
        this.mostrar('xy',"Covarianza muestral",c)
    }

    mostrar(variable,operacion,content){
        
        var enoughNumbers = true
        if (isNaN(content) || content.length == 0)
            enoughNumbers = false
        if (isNaN(content) && content.length > 0)
            enoughNumbers = true
       
        if (variable == 'y') {
            document.document.getElementsByTagName("ul")[1].innerHTML = "<li>"+operacion+": "+content+"</li>"
            this.drawStackY(this.stack.length+2)
        } else {
            document.document.getElementsByTagName("ul")[0].innerHTML = "<li>"+operacion+": "+content+"</li>"
            this.drawStack(this.stack.length+2)
        }
        if (content == "No coincide la cantidad de 'x' y de 'y'"){
            document.gdocument.getElementsByTagName("ul")[0].innerHTML = "<li>"+operacion+": "+content+"</li>"
            this.drawStack(this.stack.length+2)
            return
        }
        if (!enoughNumbers) {
            document.document.getElementsByTagName("ul")[0].innerHTML = "<li>"+operacion+": "+"No hay datos suficientes"+"</li>"
            return
        }
        
        
    }
    enter(){
        console.log("pantalla: "+this.screen)
        if (!this.introducirY) {
            var num = parseFloat(this.screen)
            if (this.map.get(num)==undefined){
                this.map.set(num,1)
            } else {
                this.map.set(num,this.map.get(num)+1)
            }
            super.enter()
        } else {
            var num = parseFloat(this.screenY)
            if (this.map.get(num)==undefined){
                this.map.set(num,1)
            } else {
                this.map.set(num,this.mapY.get(num)+1)
            }
            this.enterY()
        }
    }
    escribirY(content){
        document.getElementsByTagName("ul")[1].innerHTML = "<li>1: "+content+"</li>"

        this.drawStackY(this.stackY.length+2)
    }
    enterY(){
        this.stackY.push(parseFloat(this.screenY))
        console.log(this.stackY)
        document.getElementsByTagName("ul")[1].innerHTML = "<li>1: </li>"
        this.drawStackY(this.stackY.length+2)
        this.screenY=""
    }
    drawStackY(i){
        var s = ""
        this.stackY.forEach(function(elemento,indice,array) {
            i=i-1
            s+="<li>"+i+": "+elemento+"</li>"
        })
        document.getElementsByTagName("ul")[1].innerHTML =s+document.getElementsByTagName("ul")[1].innerHTML
    }

}

document.addEventListener('keydown', (event) => {
    const keyName = event.key;
    switch(keyName){
        case "x":
            calculadora.x()
            break
        case "y":
            calculadora.y()
            break
    }
  });


var calculadora = new CalculadoraEstadistica()