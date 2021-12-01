"use strict"

class CalculadoraCientifica extends Calculadora {

    constructor (){
        super()
        this.openedPars = 0
        this.notacionCientifica = false   
        this.twondClicked = false     
        this.twondClickedTrig = false
        this.hypClicked = false
    }
    openPar(){
        this.screen=this.screen+"("
        this.openedPars++
        escribir( this.screen )
        
    }
    closePar(){
        if (this.openedPars > 0) {
            this.screen=this.screen+")"
            this.openedPars--
            escribir( this.screen )
        }
        
    }
    contar(a){
        var indices = [];
        for(var i = 0; i < this.screen.length; i++) {
	        if (this.screen[i] == a)
                indices.push(i);
        }
        return indices.length;

    }

    igual(){
        var result
        this.mrcOnce = false
        this.screen=this.screen+""
        if (this.screen==""){
            try {
                result= eval(this.formatForEval(document.getElementsByTagName("input")[0].value))
            }catch(err){
                escribir("Error: "+err)
                return "Error: "+err
            }
        } else {
            
            if (this.screen.indexOf('^') > -1){
                var count = this.contar('^');
                console.log("Contador: "+count)
                var base = this.screen.split('^')[0]
                
                var exponente
                for (var i = 1; i<=count; i++){
                    exponente = this.screen.split('^')[i]
                    console.log(base+"^"+exponente+"Contador: "+count)
                    try{
                        result = Math.pow(eval(this.formatForEval(base)),eval(this.formatForEval(exponente)))
                        base = result.toString()
                    }catch(err){
                        escribir( "Error: "+err )
                        return "Error: "+err
                    }
                }
                
                
            } else if (this.screen.indexOf('base') > -1) {
                var count = this.contar('b')
                console.log("Contador: "+count)

                var bases = this.screen.split('base')
                //var base = bases[1]
                result = bases[0].substring(count*3,bases[0].length)
                for (var i = 1; i<bases.length; i++){
                    base = bases[i]
                    var logaritmoDe = result
                    console.log("Logaritmo "+logaritmoDe+"base"+base)
                    result = Math.log(logaritmoDe) / Math.log(base)
                    console.log(result)
                }

                

            
            }else {
                try{
                    result= eval(this.formatForEval(this.screen))
                    console.log("Resultado:"+result)
                }catch(err){
                    escribir( "Error: "+err )
                    return "Error: "+err
                }
            }
            
        }
        
        this.screen=""
        this.num1=""
        this.num2=""
        result=""+result
        if (this.notacionCientifica){
            var parteEntera = result.substring(0,1)
            var parteDecimal = result.substring(1,result.length)
            var a = parteDecimal.split('.')[0]
            var b = parteDecimal.split('.')[1]
            parteDecimal = a+b
            result = parteEntera+"."+parteDecimal+"e+"+(result.length-1)
        } 
        if (result=="undefined")
            result="Error"
        console.log(result)
        escribir( ""+result )
        return result
    }
    pi(){
        this.screen=this.screen+'\u03C0'
        escribir( this.screen )
    }
    e(){
        this.screen+="e"
        escribir( this.screen )
    }

    borrarUnNumero(){
        this.mrcOnce = false
        this.screen=this.screen.substring(0,this.screen.length-1)
        escribir(this.screen)
    }

    negate(){
        if (this.screen == "") {
            return;
        }
        else if (this.screen.charAt(0) == '-'){
            this.screen = this.screen.substring(1, this.screen.length);
        } else {
            this.screen = '-' +this.screen
        }
        escribir(this.screen)
    }

    mod(){
        this.mrcOnce = false
        this.operacion="%"
        this.screen=this.screen.concat(this.operacion)
        this.num1=this.num2
        this.num2=""
        escribir(this.screen)
    }

    fact(){
     
        var result = this.igual()

        var total = 1; 
	    for (var i=1; i<=result; i++) {
		    total = total * i; 
	    }
        escribir( ""+total )
    }

    log(){
        var result = this.igual()

	    var total = Math.log10(result)
        escribir( ""+total )
    }

    logxy(){
        this.mrcOnce = false
        this.operacion="base"
        this.screen = "log"+this.screen.concat(this.operacion)
        this.num1=this.num2
        this.num2=""
        escribir(this.screen)
    }

    ln(){
        var result = this.igual()

	    var total = Math.log(result)
        escribir( ""+total )
    }

    ex(){
        var result = this.igual()

	    var total = Math.exp(result)
        escribir( ""+total )
    }

    exp(){
        //Pone en notación científica
        this.notacionCientifica = true
        this.igual()
        this.notacionCientifica = false        
    }

    abs(){
        var result = this.igual()

	    var total = Math.abs(result)
        escribir( ""+total )
    }
    floor(){//Devuelve entero más grande <= x
        var result = this.igual()

	    var total = Math.floor(result)
        escribir( ""+total )
    }
    ceil(){
        var result = this.igual()

	    var total = Math.ceil(result)
        escribir( ""+total )
    }
    rand(){
        var random = Math.random()
        escribir(random)
    }
    dms(){
        //Pasa de degrees a dms
        var result = this.igual()
        var parteEntera = result.split('.')[0]
        var parteDecimal = result.split('.')[1]
        var m = (('0.'+parteDecimal)*60).toString()

        var degrees = parteEntera
        var minutes = m.split('.')[0].substring(0,2)
        var seconds = m.split('.')[1]*60

        var total = degrees+'.'+minutes+seconds
        escribir( ""+total )
    }
    degrees(){
        //Pasa de dms a degrees
        var result = this.igual()
        var degrees = result.split('.')[0]
        var minutes = result.split('.')[1].substring(0,2)
        var seconds = result.split('.')[1].substring(2,result.length)

        var parteEntera = degrees
        var parteDecimal = (minutes+seconds/60)/60
        parteDecimal = parteDecimal.toString().replace('.','')

        var total = parteEntera+'.'+parteDecimal
        
        escribir( ""+total )
    }

    inverse(){
        var result = this.igual()

	    var total = 1/result
        escribir( ""+total )
    }

    powerTwoOf(){
        var result = this.igual()

	    var total = Math.pow(result,2)
        escribir( ""+total )
    }

    powerThreeOf(){
        var result = this.igual()

	    var total = Math.pow(result,3)
        escribir( ""+total )
    }

    root(n){
        var result = this.igual()

	    var total = Math.sqrt(result,n)
        escribir( ""+total )
    }

    XtoTheY(){
        this.mrcOnce = false
        this.operacion="^"
        this.screen=this.screen.concat(this.operacion)
        this.num1=this.num2
        this.num2=""
        escribir(this.screen)
    }

    YrootX(){
        this.mrcOnce = false
        this.operacion="√"
        this.screen=this.screen.concat(this.operacion)
        this.num1=this.num2
        this.num2=""
        escribir(this.screen)
    }

    tenToThePower(){
        var result = this.igual()

	    var total = Math.pow(10,result)
        escribir( ""+total )
    }

    twoToThePower(){
        var result = this.igual()

	    var total = Math.pow(2,result)
        escribir( ""+total )
    }

    DEG(){
        switch(document.getElementsByName("deg")[0].innerHTML){
            case "DEG":
                document.getElementsByName("deg")[0].innerHTML = "RAD"
                break;
            case "RAD":
                document.getElementsByName("deg")[0].innerHTML = "GRAD"
                break;
            case "GRAD":
                document.getElementsByName("deg")[0].innerHTML = "DEG"
                break;
        }
        
    }

    sin(){
        var result = this.igual()
        var total
        switch(document.getElementsByName("deg")[0].innerHTML){
            case "DEG":
                total = Math.sin(result/180*Math.PI)
                break;
            case "RAD":
                total = Math.sin(result) //en radianes
                break;
            case "GRAD":
                total = Math.sin(result/200*Math.PI)
                break;
	        
        }
        escribir( ""+total )
        return total
    }
    sinh(){
        var result = this.igual()
        var total = Math.sinh(result)
         
        escribir( ""+total )
        return total
    }
    sinInverse(){
        var result = this.igual()
        var total = Math.asin(result)
        
        escribir( ""+total )
        return total
        
    }
    sinhInverse(){
        var result = this.igual()
        var total = Math.asinh(result) 
               
        escribir( ""+total )
        return total
    }
    cos(){
        var result = this.igual()
        var total
        switch(document.getElementsByName("deg")[0].innerHTML){
            case "DEG":
                total = Math.cos(result/180*Math.PI)
                break;
            case "RAD":
                total = Math.cos(result) //en radianes
                break;
            case "GRAD":
                total = Math.cos(result/200*Math.PI)
                break;
	        
        }
        escribir( ""+total )
        return total
    }
    cosh(){
        var result = this.igual()
        var total = Math.cosh(result) //en radianes
              
        escribir( ""+total )
        return total
    }
    cosInverse(){
        var result = this.igual()
        var total = Math.acos(result)
                
        escribir( ""+total )
        return total
    }
    coshInverse(){
        var result = this.igual()
        var total = Math.acosh(result) //en radianes
        console.log(total)
        escribir( ""+total )
        return total
    }
    tan(){
        var result = this.igual()
        var total
        switch(document.getElementsByName("deg")[0].innerHTML){
            case "DEG":
                total = Math.tan(result/180*Math.PI)
                break;
            case "RAD":
                total = Math.tan(result) //en radianes
                break;
            case "GRAD":
                total = Math.tan(result/200*Math.PI)
                break;
	        
        }
        escribir( ""+total )
        return total
    }
    tanh(){
        var result = this.igual()
        var total = Math.tanh(result) 
        
        escribir( ""+total )
        return total
    }
    tanInverse(){
        var result = this.igual()
        var total = Math.atan(result)
        
        escribir( ""+total )
        return total
    }
    tanhInverse(){
        var result = this.igual()
        var total = Math.atanh(result) //en radianes
        
        escribir( ""+total )
        return total
    }
    sec(){
        var total = 1/this.cos()
        escribir( ""+total )
    }
    sech(){
        var total = 1/this.cosh()
        escribir( ""+total )
    }
    secInverse(){ 
        this.screen = 1.0/this.screen
        var total
        switch(document.getElementsByName("deg")[0].innerHTML){
            case "DEG":
                total = this.cosInverse()*180/Math.PI
                break;
            case "RAD":
                total = this.cosInverse() //en radianes
                break;
            case "GRAD":
                total = this.cosInverse()*200/Math.PI
                break;
	        
        }

        escribir( ""+total )
    }
    sechInverse(){
        this.screen = 1.0/this.screen
        var total = this.coshInverse()

        escribir( ""+total )
    }
    csc(){
        var total = 1/this.sin()
        escribir( ""+total )
    }
    csch(){
        var total = 1/this.sinh()
        escribir( ""+total )
    }
    cscInverse(){
        this.screen = 1.0/this.screen

        var total
        switch(document.getElementsByName("deg")[0].innerHTML){
            case "DEG":
                total = this.sinInverse()*180/Math.PI
                break;
            case "RAD":
                total = this.sinInverse()  //en radianes
                break;
            case "GRAD":
                total = this.sinInverse()*200/Math.PI
                break;
	        
        }
        escribir( ""+total )
    }
    cschInverse(){
        this.screen = 1.0/this.screen
        var total = this.sinhInverse()

        escribir( ""+total )
    }
    cot(){
        var total = 1/this.tan()
        escribir( ""+total )
    }
    coth(){
        var total = 1/this.tanh()
        escribir( ""+total )
    }
    cotInverse(){
        this.screen = 1.0/this.screen

        var total
        switch(document.getElementsByName("deg")[0].innerHTML){
            case "DEG":
                total = this.tanInverse()*180/Math.PI
                break;
            case "RAD":
                total = this.tanInverse() //en radianes
                break;
            case "GRAD":
                total = this.tanInverse()*200/Math.PI
                break;
	        
        }
        escribir( ""+total )
    }
    cothInverse(){
        this.screen = 1.0/this.screen
        var total = this.tanhInverse()

        escribir( ""+total )
    }
    

    twoPoweredToThe(){
        if(!this.twondClicked){
            document.getElementsByName("powerTwoOf")[0].innerHTML='x<sup>3</sup>'
            document.getElementsByName("powerTwoOf")[0].setAttribute('onclick',"calculadora.powerThreeOf()")

            document.getElementsByName("root2")[0].innerHTML='<sup>3</sup>&Sqrt;x'
            document.getElementsByName("root2")[0].setAttribute('onclick',"calculadora.root(3)")

            document.getElementsByName("xtothey")[0].innerHTML='<sup>y</sup>&Sqrt;x'
            document.getElementsByName("xtothey")[0].setAttribute('onclick',"calculadora.YrootX()")

            document.getElementsByName("tenToThePower")[0].innerHTML='2<sup>x</sup>'
            document.getElementsByName("tenToThePower")[0].setAttribute('onclick',"calculadora.twoToThePower()")

            document.getElementsByName("log")[0].innerHTML='log<sub>y</sub>x'
            document.getElementsByName("log")[0].setAttribute('onclick',"calculadora.logxy()")

            document.getElementsByName("ln")[0].innerHTML='e<sup>x</sup>'
            document.getElementsByName("ln")[0].setAttribute('onclick',"calculadora.ex()")
            
            this.twondClicked = true
        } else {
            document.getElementsByName("powerTwoOf")[0].innerHTML='x<sup>2</sup>'
            document.getElementsByName("powerTwoOf")[0].setAttribute('onclick',"calculadora.powerTwoOf()")

            document.getElementsByName("root2")[0].innerHTML='<sup>2</sup>&Sqrt;x'
            document.getElementsByName("root2")[0].setAttribute('onclick',"calculadora.root(2)")

            document.getElementsByName("xtothey")[0].innerHTML='x<sup>y</sup>'
            document.getElementsByName("xtothey")[0].setAttribute('onclick',"calculadora.XtoTheY()")

            document.getElementsByName("tenToThePower")[0].innerHTML='10<sup>x</sup>'
            document.getElementsByName("tenToThePower")[0].setAttribute('onclick',"calculadora.tenToThePower()")

            document.getElementsByName("log")[0].innerHTML='log'
            document.getElementsByName("log")[0].setAttribute('onclick',"calculadora.log()")

            document.getElementsByName("ln")[0].innerHTML='ln'
            document.getElementsByName("ln")[0].setAttribute('onclick',"calculadora.ln()")

            this.twondClicked = false
        }

    }
    twoND(){
        if(!this.twondClickedTrig){
            document.getElementsByName("sin")[0].innerHTML+='<sup>-1</sup>'
            document.getElementsByName("cos")[0].innerHTML+='<sup>-1</sup>'
            document.getElementsByName("tan")[0].innerHTML+='<sup>-1</sup>'
            document.getElementsByName("sec")[0].innerHTML+='<sup>-1</sup>'
            document.getElementsByName("csc")[0].innerHTML+='<sup>-1</sup>'
            document.getElementsByName("cot")[0].innerHTML+='<sup>-1</sup>'

            if(this.hypClicked) {
                document.getElementsByName("sin")[0].setAttribute('onclick',"calculadora.sinhInverse()")
                document.getElementsByName("cos")[0].setAttribute('onclick',"calculadora.coshInverse()")
                document.getElementsByName("tan")[0].setAttribute('onclick',"calculadora.tanhInverse()")
                document.getElementsByName("sec")[0].setAttribute('onclick',"calculadora.sechInverse()")
                document.getElementsByName("csc")[0].setAttribute('onclick',"calculadora.cschInverse()")
                document.getElementsByName("cot")[0].setAttribute('onclick',"calculadora.cothInverse()")
            } else {
                document.getElementsByName("sin")[0].setAttribute('onclick',"calculadora.sinInverse()")
                document.getElementsByName("cos")[0].setAttribute('onclick',"calculadora.cosInverse()")
                document.getElementsByName("tan")[0].setAttribute('onclick',"calculadora.tanInverse()")
                document.getElementsByName("sec")[0].setAttribute('onclick',"calculadora.secInverse()")
                document.getElementsByName("csc")[0].setAttribute('onclick',"calculadora.cscInverse()")
                document.getElementsByName("cot")[0].setAttribute('onclick',"calculadora.cotInverse()")
            }
            
            
            this.twondClickedTrig = true

        } else {
            if(this.hypClicked) {
                document.getElementsByName("sin")[0].innerHTML='sinh'
                document.getElementsByName("sin")[0].setAttribute('onclick',"calculadora.sinh()")

                document.getElementsByName("cos")[0].innerHTML='cosh'
                document.getElementsByName("cos")[0].setAttribute('onclick',"calculadora.cosh()")

                document.getElementsByName("tan")[0].innerHTML='tanh'
                document.getElementsByName("tan")[0].setAttribute('onclick',"calculadora.tanh()")

                document.getElementsByName("sec")[0].innerHTML='sech'
                document.getElementsByName("sec")[0].setAttribute('onclick',"calculadora.sech()")

                document.getElementsByName("csc")[0].innerHTML='csch'
                document.getElementsByName("csc")[0].setAttribute('onclick',"calculadora.csch()")

                document.getElementsByName("cot")[0].innerHTML='coth'
                document.getElementsByName("cot")[0].setAttribute('onclick',"calculadora.coth()")
            } else {
                document.getElementsByName("sin")[0].innerHTML='sin'
                document.getElementsByName("sin")[0].setAttribute('onclick',"calculadora.sin()")

                document.getElementsByName("cos")[0].innerHTML='cos'
                document.getElementsByName("cos")[0].setAttribute('onclick',"calculadora.cos()")

                document.getElementsByName("tan")[0].innerHTML='tan'
                document.getElementsByName("tan")[0].setAttribute('onclick',"calculadora.tan()")

                document.getElementsByName("sec")[0].innerHTML='sec'
                document.getElementsByName("sec")[0].setAttribute('onclick',"calculadora.sec()")

                document.getElementsByName("csc")[0].innerHTML='csc'
                document.getElementsByName("csc")[0].setAttribute('onclick',"calculadora.csc()")

                document.getElementsByName("cot")[0].innerHTML='cot'
                document.getElementsByName("cot")[0].setAttribute('onclick',"calculadora.cot()")
            
            }

            this.twondClickedTrig = false
        }
    }
    hyp(){
        if(!this.hypClicked){
            document.getElementsByName("sin")[0].innerHTML=document.getElementsByName("sin")[0].innerHTML.replace('sin','sinh')
            document.getElementsByName("cos")[0].innerHTML=document.getElementsByName("cos")[0].innerHTML.replace('cos','cosh')
            document.getElementsByName("tan")[0].innerHTML=document.getElementsByName("tan")[0].innerHTML.replace('tan','tanh')
            document.getElementsByName("sec")[0].innerHTML=document.getElementsByName("sec")[0].innerHTML.replace('sec','sech')
            document.getElementsByName("csc")[0].innerHTML=document.getElementsByName("csc")[0].innerHTML.replace('csc','csch')
            document.getElementsByName("cot")[0].innerHTML=document.getElementsByName("cot")[0].innerHTML.replace('cot','coth')
           

            if(this.twondClickedTrig) {
                document.getElementsByName("sin")[0].setAttribute('onclick',"calculadora.sinhInverse()")
                document.getElementsByName("cos")[0].setAttribute('onclick',"calculadora.coshInverse()")
                document.getElementsByName("tan")[0].setAttribute('onclick',"calculadora.tanhInverse()")
                document.getElementsByName("sec")[0].setAttribute('onclick',"calculadora.sechInverse()")
                document.getElementsByName("csc")[0].setAttribute('onclick',"calculadora.cschInverse()")
                document.getElementsByName("cot")[0].setAttribute('onclick',"calculadora.cothInverse()")
            } else {
                document.getElementsByName("sin")[0].setAttribute('onclick',"calculadora.sinh()")
                document.getElementsByName("cos")[0].setAttribute('onclick',"calculadora.cosh()")
                document.getElementsByName("tan")[0].setAttribute('onclick',"calculadora.tanh()")
                document.getElementsByName("sec")[0].setAttribute('onclick',"calculadora.sech()")
                document.getElementsByName("csc")[0].setAttribute('onclick',"calculadora.csch()")
                document.getElementsByName("cot")[0].setAttribute('onclick',"calculadora.coth()")
            }
            
            this.hypClicked = true

        } else {
            document.getElementsByName("sin")[0].innerHTML=document.getElementsByName("sin")[0].innerHTML.replace('h','')
            document.getElementsByName("cos")[0].innerHTML=document.getElementsByName("cos")[0].innerHTML.replace('h','')
            document.getElementsByName("tan")[0].innerHTML=document.getElementsByName("tan")[0].innerHTML.replace('h','')
            document.getElementsByName("sec")[0].innerHTML=document.getElementsByName("sec")[0].innerHTML.replace('h','')
            document.getElementsByName("csc")[0].innerHTML=document.getElementsByName("csc")[0].innerHTML.replace('h','')
            document.getElementsByName("cot")[0].innerHTML=document.getElementsByName("cot")[0].innerHTML.replace('h','')
            
            if(this.twondClickedTrig) {
                document.getElementsByName("sin")[0].setAttribute('onclick',"calculadora.sinInverse()")
                document.getElementsByName("cos")[0].setAttribute('onclick',"calculadora.cosInverse()")
                document.getElementsByName("tan")[0].setAttribute('onclick',"calculadora.tanInverse()")
                document.getElementsByName("sec")[0].setAttribute('onclick',"calculadora.secInverse()")
                document.getElementsByName("csc")[0].setAttribute('onclick',"calculadora.cscInverse()")
                document.getElementsByName("cot")[0].setAttribute('onclick',"calculadora.cotInverse()")
            } else {
                document.getElementsByName("sin")[0].setAttribute('onclick',"calculadora.sin()")
                document.getElementsByName("cos")[0].setAttribute('onclick',"calculadora.cos()")
                document.getElementsByName("tan")[0].setAttribute('onclick',"calculadora.tan()")
                document.getElementsByName("sec")[0].setAttribute('onclick',"calculadora.sec()")
                document.getElementsByName("csc")[0].setAttribute('onclick',"calculadora.csc()")
                document.getElementsByName("cot")[0].setAttribute('onclick',"calculadora.cot()")
            }

            this.hypClicked = false
        }
    }



    FE() {
        if (this.notacionCientifica){
            this.notacionCientifica = false
        } else {
            this.notacionCientifica = true
        }
    }
    MC(){
        this.memory=""
    }
    MR(){
        this.screen = this.memory
        escribir(this.screen)
    }
    MS(){
        this.memory = this.screen
        console.log("Memory: "+this.memory)
    }

    formatForEval(operacion){
        console.log("Operacion en pantalla: "+operacion)
        let operacionConNumber=""
        let numeroEncontrado=false
        let parentesisCerrado=true

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
            else if(char=='.'){
                operacionConNumber+='.'

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
        if (parentesisCerrado == false)
            operacionConNumber+=")"
        console.log("Operacion con number: "+operacionConNumber);
        return operacionConNumber 
    }


}

document.addEventListener('keydown', (event) => {
    
    const keyName = event.key;
    switch(keyName){
        case "Backspace":
            calculadora.borrarUnNumero()
            break
        case "e":
            calculadora.e()
            break
        case "c":
            calculadora.borrar()
            break;
        case "%":
            calculadora.mod()
            break
        case "l":
            calculadora.log()
            break;
        case "m":
            calculadora.dms()
            break
        case "q":
            calculadora.powerTwoOf()
            break
        case "r":
            calculadora.inverse()
            break
        case "t":
            calculadora.tan()
            break
        case "y":
            calculadora.XtoTheY()
            break
        
    }

  });

  

  var calculadora = new CalculadoraCientifica()