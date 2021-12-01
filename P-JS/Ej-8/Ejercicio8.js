
"use strict"
class Tiempo {
    constructor() {
        this.cargarDatos(0)
    }

    cargarDatos(index) {
        this.where = ["Bucarest",
            "Gura Sutii",
            "Bacau",
            "Comanesti",
            "Oviedo"]
        this.meteo = new Object()
        this.meteo.ciudad = this.where[index]
        this.meteo.apikey = "52f3a6700c325fd0fd964e14704ee5c4"
        this.meteo.unidades = "&units=metric"
        this.meteo.idioma = "&lang=es"
        this.meteo.url = "https://api.openweathermap.org/data/2.5/weather?q=" + this.meteo.ciudad + this.meteo.unidades + this.meteo.idioma + "&APPID=" + this.meteo.apikey
        this.meteo.error = "<h2>¡problemas!<h2><p> No puedo obtener información de <a href=\"http://openweathermap.org\">OpenWeatherMap</a></p>"
        this.infoRelevanteMeteo = new Object()
        var self = this
        this.meteo.datos = $.ajax({
            dataType: "json",
            url: self.meteo.url,
            method: 'GET',
            success: function (data) {
                index++
                self.seleccionarDatos(data)
                self.mostrarInfo(index)
                
                if (index < 5)
                    self.cargarDatos(index)

            },
            error: function () {
                var whereStr = "body article section:nth-of-type("+index+")"
                $(whereStr).html(this.meteo.error)
            }
        });

    }

    seleccionarDatos(datos) {
        this.infoRelevanteMeteo.ciudad = datos.name
        this.infoRelevanteMeteo.pais = datos.sys.country
        this.infoRelevanteMeteo.latitud = datos.coord.lat
        this.infoRelevanteMeteo.longitud = datos.coord.lon
        this.infoRelevanteMeteo.tempActual = datos.main.temp
        this.infoRelevanteMeteo.tempSensacion = datos.main.feels_like
        this.infoRelevanteMeteo.tempMax = datos.main.temp_max
        this.infoRelevanteMeteo.tempMin = datos.main.temp_min
        this.infoRelevanteMeteo.presion = datos.main.pressure
        this.infoRelevanteMeteo.humedad = datos.main.humidity
        this.infoRelevanteMeteo.horaAmanecer = new Date(datos.sys.sunrise * 1000).toLocaleTimeString()
        this.infoRelevanteMeteo.horaOscurecer = new Date(datos.sys.sunset * 1000).toLocaleTimeString()
        this.infoRelevanteMeteo.direccionViento = datos.wind.deg
        this.infoRelevanteMeteo.velocidadVinto = datos.wind.speed

        this.infoRelevanteMeteo.visibilidad = datos.visibility
        this.infoRelevanteMeteo.descripcionTiempo = datos.weather[0].description
        this.infoRelevanteMeteo.nubosidad = datos.clouds.all
        this.infoRelevanteMeteo.icono = datos.weather[0].icon
        this.horaDatos = new Date(datos.dt * 1000).toLocaleTimeString()
        this.fechaDatos = new Date(datos.dt * 1000).toLocaleDateString()
    }
    mostrarInfo(i) {
        this.mostrarInfoGeneral(i)
        this.footer(i)
    }
    mostrarInfoGeneral(i) {
        var header = "<h2>El tiempo en " + this.infoRelevanteMeteo.ciudad + ", " + this.infoRelevanteMeteo.pais + "</h2><h3> Tiempo general: </h3> <p>" + this.infoRelevanteMeteo.descripcionTiempo + "</p><img src=https://openweathermap.org/img/w/" + this.infoRelevanteMeteo.icono + ".png alt=\"foto de " + this.infoRelevanteMeteo.descripcionTiempo + "\">"
        var inicial = header
        inicial += "<ul>"
        inicial += "<li><h3> Coordenadas: </h3><p> Longitud: " + this.infoRelevanteMeteo.longitud + "</p><p> Latitud: " + this.infoRelevanteMeteo.latitud + "</p></li>"
        inicial += "<li><h3> Temperatura:</h3> <p>Actual: " + this.infoRelevanteMeteo.tempActual + "º</p> <p>Sensacion Termica: " + this.infoRelevanteMeteo.tempSensacion + "º </p> <p>Maxima: " + this.infoRelevanteMeteo.tempMax + "º </p><p>Minima: " + this.infoRelevanteMeteo.tempMin + "º</p></li>"
        inicial += "<li><h3> Presion: </h3><p>" + this.infoRelevanteMeteo.presion + "hPa</p></li>"
        inicial += "<li><h3> Humedad: </h3><p>" + this.infoRelevanteMeteo.humedad + "%</p></li>"
        inicial += "<li><h3> Viento: </h3><p> Velocidad: " + this.infoRelevanteMeteo.velocidadVinto + "m/s </p> <p>Direccion: " + this.infoRelevanteMeteo.direccionViento + "º</p></li>"
        inicial += "<li><h3> Visibilidad </h3><p>Metros de visibilidad: " + this.infoRelevanteMeteo.visibilidad + "</p></li>"
        inicial += "<li><h3> Horario: </h3><p> Hora de Amanecer: " + this.infoRelevanteMeteo.horaAmanecer + "</p><p> Hora Anochecer: " + this.infoRelevanteMeteo.horaOscurecer + "</p></li>"
        inicial += "<li><h3> Nubes: </h3><p> Porcentaje de Nubosidad: " + this.infoRelevanteMeteo.nubosidad + "%</p></li>"
        inicial += "</ul>"
        var whereStr = "body article section:nth-of-type("+i+")"
        $(whereStr).html($(whereStr).html() + inicial)
    }
    footer(i) {
        var footer = ""
        footer += "<footer><p>"
        footer += "<h3> Informacion extra: </h3>"
        footer += "Hora y fecha de la toma de datos: " + this.horaDatos + ", " + this.fechaDatos
        footer += "</p>"
        footer += "<p>Datos extraidos de <a href=\"http://openweathermap.org\">http://openweathermap.org</a></p></footer>"
        var whereStr = "body article section:nth-of-type("+i+")"
        $(whereStr).html($(whereStr).html() + footer)
    }

}
var tiempo = new Tiempo();