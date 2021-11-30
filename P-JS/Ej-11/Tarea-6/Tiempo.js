
"use strict"
class Tiempo {
    constructor(long, lat) {
        this.meteo = new Object()
        this.meteo.apikey = "52f3a6700c325fd0fd964e14704ee5c4"
        this.meteo.long = long
        this.meteo.lat = lat
        this.meteo.exclue = "minutely,hourly"
        this.meteo.unidades = "&units=metric"
        this.meteo.idioma = "&lang=es"
        this.meteo.url = "https://api.openweathermap.org/data/2.5/onecall?lat=" + this.meteo.lat + "&lon=" + this.meteo.long + "&exclude=" + this.meteo.exclue +this.meteo.idioma+ this.meteo.unidades+"&appid=" + this.meteo.apikey
        this.meteo.error = "<h2>¡problemas! No puedo obtener información de <a href=\"http://openweathermap.org\">OpenWeatherMap</a></h2>"
        this.infoRelevanteMeteo = []
        this.cargarDatos()

    }

    cargarDatos() {
        var self = this
        this.meteo.datos = $.ajax({
            dataType: "json",
            url: self.meteo.url,
            method: 'GET',
            success: function (data) {
                self.seleccionarDatos(data)
                self.mostrarInfo()
            },
            error: function () {
                document.write(self.meteo.error);
            }
        });
    }


    seleccionarDatos(datos) {
        //console.log(JSON.stringify(datos,null,1))
        this.actual = new Object()

        this.actual.icono=datos.current.weather[0].icon
        this.actual.descripcion=datos.current.weather[0].description

        this.actual.nubosidad=datos.current.clouds
        this.actual.humedad=datos.current.humidity

        this.actual.gradosDireccionViento=datos.current.wind_deg
        this.actual.velocidadVinto=datos.current.wind_speed

        this.actual.sensacionTermica=datos.current.feels_like
        this.actual.temperaturaReal=datos.current.temp

        this.actual.horaAmanecer = new Date(datos.current.sunrise * 1000).toLocaleTimeString()
        this.actual.horaOscurecer = new Date(datos.current.sunset * 1000).toLocaleTimeString()
        
        this.actual.horaTomaDatos = new Date(datos.current.dt * 1000).toLocaleTimeString()
        this.actual.fechaTomaDatos = new Date(datos.current.dt * 1000).toLocaleDateString()

        
    
        this.semana=[]
        
        var pronostico=["Mañana","Pasado Mañana","Dentro De Tres Dias", "Dentro De Cuatro Dias", "Dentro De Cinco Dias", "Dentro De Seis Dias", "Dentro De Siete Dias","Dentro De Ocho Dias"]
        var i=0
        var self=this
        datos.daily.forEach(diaIndex => {
            var dia = new Object()
            dia.paraCuando=pronostico[i]
            i++
            dia.icono=diaIndex.weather[0].icon
            dia.descripcion=diaIndex.weather[0].description

            dia.humedad=diaIndex.humidity
            dia.nubosidad = diaIndex.clouds
            dia.presion = diaIndex.pressure
            
            dia.probLluvia = diaIndex.rain
            dia.probNieve = diaIndex.snow

            dia.gradosDireccionViento = diaIndex.wind_deg
            dia.velocidadVinto = diaIndex.wind_speed

            dia.sensacionTermica = new Object()
            dia.sensacionTermica.dia = diaIndex.feels_like.day
            dia.sensacionTermica.noche = diaIndex.feels_like.night
            dia.sensacionTermica.tarde = diaIndex.feels_like.eve
            dia.sensacionTermica.mañana = diaIndex.feels_like.morn

            dia.temperaturaReal = new Object()
            dia.temperaturaReal.dia = diaIndex.temp.day
            dia.temperaturaReal.noche = diaIndex.temp.night
            dia.temperaturaReal.tarde = diaIndex.temp.eve
            dia.temperaturaReal.mañana = diaIndex.temp.morn


            dia.horaAmanecer = new Date(diaIndex.sunrise * 1000).toLocaleTimeString()
            dia.horaOscurecer = new Date(diaIndex.sunset * 1000).toLocaleTimeString()
            
            dia.horaTomaDatos = new Date(diaIndex.dt * 1000).toLocaleTimeString()
            dia.fechaTomaDatos = new Date(diaIndex.dt * 1000).toLocaleDateString()
            self.semana.push(dia)
        });

    }
    mostrarInfo() {
        this.resetHtml()
        this.mostrarInfoCabecera()
        this.mostrarInfoGeneral()
        this.footer()

    }

    resetHtml(){
        $("section:nth-of-type(2)").html("")
        $("section:nth-of-type(3) section").html("")
    }
    mostrarInfoCabecera(){
        var cabeceraInfo=""
        cabeceraInfo+="<header>"
        cabeceraInfo+="<h2>El tiempo en "+this.meteo.lat+", "+this.meteo.long+"</h2>"
        cabeceraInfo+="</header>"
        $("main section:nth-of-type(2)").html(cabeceraInfo)
    }
    mostrarInfoGeneral() {
        var actual=""
        actual+="<header>"
        actual+="<h2> Tiempo Actual: </h2>"
        actual+="<h3>Tiempo general: "+this.actual.descripcion+"</h3"
        actual+="<img src=https://openweathermap.org/img/w/"+this.actual.icono+".png alt=\"foto de "+this.actual.descripcionTiempo+"\">"
        actual+="</header>"

        actual+="<ul>"
        actual+="<li> <p> Sensacion Termica: "+this.actual.sensacionTermica+"º </p></li>"
        actual+="<li><p>Temperatura real: "+this.actual.temperaturaReal+"º</p> </li>"
        actual+="<li> <p> Nubosidad: "+this.actual.nubosidad+"% </p> </li>"
        actual+="<li> <p> Humedad: "+this.actual.humedad+"% </p> </li>"
        
        actual+="<li> <p> Velocidad Del Viento: "+this.actual.velocidadVinto+"m/s </p> </li>"
        actual+="<li> <p> Direccion Del Viento: "+this.actual.gradosDireccionViento+"º </p> </li>"
        actual+="<li> <p> Hora Amanecer: "+this.actual.horaAmanecer+"</p> </li>"
        actual+="<li> <p> Hora Oscurecer: "+this.actual.horaOscurecer+"</p> </li>"
        actual+="</ul>"


        actual+="<footer>"
        actual+="<p>Hora De Toma De Datos: "+this.actual.horaTomaDatos+"<p>"
        actual+="<p>Fecha De Toma De Datos: "+this.actual.fechaTomaDatos+"<p>"
        actual+="</footer>"
        $("main>section:nth-of-type(2)").html(actual)
        $("main section:nth-of-type(3) section").html("<h3> Los dias: </h3>")
        var i=0
        this.semana.forEach(dia => {
            this.mostrarDia(dia.descripcion,dia.icono,dia.sensacionTermica,dia.temperaturaReal,dia.descripcionTiempo,dia.probNieve,dia.probLluvia,dia.nubosidad,dia.humedad,dia.velocidadVinto,dia.gradosDireccionViento,dia.horaAmanecer,dia.horaOscurecer,dia.horaTomaDatos,dia.fechaTomaDatos,dia.paraCuando,i)
            i++
        });

        
    }
    mostrarDia(descripcion,icono,sensacionTermica,temperaturaReal,descripcionTiempo,probNieve,probLluvia,nubosidad,humedad,velocidadVinto,gradosDireccionViento,horaAmanecer,horaOscurecer,horaTomaDatos,fechaTomaDatos,paraCuando,index){
        var semanal=""
        semanal+="<section>"
        semanal+="<header>"
            semanal+="<h3> Tiempo Para "+paraCuando+"</h3>"
            semanal+="<h3>Tiempo general: </h3>"
            semanal+="<p>"+descripcion+"</p>"
            semanal+="<img src=https://openweathermap.org/img/w/"+icono+".png alt=\"foto de "+descripcionTiempo+"\">"
        semanal+="</header>"

        semanal+="<ul>"
            semanal+="<li> <h3>Temperatura</h3>"
                semanal+="<h4> Sensacion Termica: </h4>"
                semanal+="<ul>"
                    semanal+= "<li>Sensacion Termica de Mañana: "+sensacionTermica.mañana+"º</li>"
                    semanal+= "<li>Sensacion Termica de Dia: "+sensacionTermica.dia+"º</li>"
                    semanal+= "<li>Sensacion Termica de Tarde: "+sensacionTermica.tarde+"º</li>"
                    semanal+= "<li>Sensacion Termica de Noche: "+sensacionTermica.noche+"º</li>"
                semanal+="</ul>"
            semanal+="<h4> Temperatura Real: </h4>"
                semanal+="<ul>"
                    semanal+= "<li>Sensacion Termica de Mañana: "+temperaturaReal.mañana+"º</li>"
                    semanal+= "<li>Sensacion Termica de Dia: "+temperaturaReal.dia+"º</li>"
                    semanal+= "<li>Sensacion Termica de Tarde: "+temperaturaReal.tarde+"º</li>"
                    semanal+= "<li>Sensacion Termica de Noche: "+temperaturaReal.noche+"º</li>"
                semanal+="</ul>"
            semanal+="</li>"
            semanal+="<li> <p> Nubosidad: "+nubosidad+"% </p> </li>"
            semanal+="<li> <p> Humedad: "+humedad+"% </p> </li>"
            if(typeof(probLluvia)!="undefined")
                semanal+="<li> <p> Probabilidad de lluvia: "+probLluvia+"%</p></li>"
            else
                semanal+="<li> <p> Probabilidad de lluvia: 0%</p></li>"
            if(typeof(probNieve)!="undefined")
                semanal+="<li> <p> Probabilidad de nieve: "+probNieve+"%</p></li>"
            else
                semanal+="<li> <p> Probabilidad de nieve: 0%</p></li>"

            semanal+="<li> <p> Velocidad Del Viento: "+velocidadVinto+"m/s </p> </li>"
            semanal+="<li> <p> Direccion Del Viento: "+gradosDireccionViento+"º </p> </li>"
            semanal+="<li> <p> Hora Amanecer: "+horaAmanecer+"</p> </li>"
            semanal+="<li> <p> Hora Oscurecer: "+horaOscurecer+"</p> </li>"

            semanal+="<li> <p> Hora De Toma De Datos: "+horaTomaDatos+"</p> </li>"
            semanal+="<li> <p> Fecha De Toma DeDatos: "+fechaTomaDatos+"</p> </li>"
        semanal+="</ul>"
        semanal+="</section>"
        $("main section:nth-of-type(3) section").html($("main section:nth-of-type(3) section").html()+semanal)
    }

    footer() {
        var footer = ""
        footer += "<footer><p>"
        footer += "<p>Datos extraidos de <a href=\"http://openweathermap.org\">http://openweathermap.org</a></p></footer>"
        $("main>section:nth-of-type(2)").html($("main>section:nth-of-type(2)").html()+footer)
    }
    


}