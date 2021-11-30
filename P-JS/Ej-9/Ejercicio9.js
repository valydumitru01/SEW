"use strict";
class Meteo {
    constructor() {
        this.cargarDatos(0)
    }

    cargarDatos(index) {
        this.where = ["Bucarest",
            "Gura Sutii",
            "Bacau",
            "Comanesti",
            "Oviedo"]
        this.apikey = "52f3a6700c325fd0fd964e14704ee5c4";
        this.ciudad = this.where[index];
        this.tipo = "&mode=xml";
        this.unidades = "&units=metric";
        this.idioma = "&lang=es";
        this.url = "https://api.openweathermap.org/data/2.5/weather?q=" + this.ciudad + this.tipo + this.unidades + this.idioma + "&APPID=" + this.apikey;
        this.correcto = "¡Todo correcto! XML recibido de <a href='https://openweathermap.org/'>OpenWeatherMap</a>"
        var self = this
        $.ajax({
            dataType: "xml",
            url: self.url,
            method: 'GET',
            success: function (datos) {
                //Extracción de los datos contenidos en el XML
                var ciudad = $('city', datos).attr("name");
                var longitud = $('coord', datos).attr("lon");
                var latitud = $('coord', datos).attr("lat");
                var pais = $('country', datos).text();
                var amanecer = $('sun', datos).attr("rise");
                var minutosZonaHoraria = new Date().getTimezoneOffset();
                var amanecerMiliSeg1970 = Date.parse(amanecer);
                amanecerMiliSeg1970 -= minutosZonaHoraria * 60 * 1000;
                var amanecerLocal = (new Date(amanecerMiliSeg1970)).toLocaleTimeString("es-ES");
                var oscurecer = $('sun', datos).attr("set");
                var oscurecerMiliSeg1970 = Date.parse(oscurecer);
                oscurecerMiliSeg1970 -= minutosZonaHoraria * 60 * 1000;
                var oscurecerLocal = (new Date(oscurecerMiliSeg1970)).toLocaleTimeString("es-ES");
                var temperatura = $('temperature', datos).attr("value");
                var temperaturaMin = $('temperature', datos).attr("min");
                var temperaturaMax = $('temperature', datos).attr("max");
                var temperaturaUnit = $('temperature', datos).attr("unit");
                var humedad = $('humidity', datos).attr("value");
                var humedadUnit = $('humidity', datos).attr("unit");
                var presion = $('pressure', datos).attr("value");
                var presionUnit = $('pressure', datos).attr("unit");
                var velocidadViento = $('speed', datos).attr("value");
                var nombreViento = $('speed', datos).attr("name");
                var direccionViento = $('direction', datos).attr("value");
                var codigoViento = $('direction', datos).attr("code");
                var nombreDireccionViento = $('direction', datos).attr("name");
                var nubosidad = $('clouds', datos).attr("value");
                var nombreNubosidad = $('clouds', datos).attr("name");
                var visibilidad = $('visibility', datos).attr("value");
                var precipitacionValue = $('precipitation', datos).attr("value");
                var precipitacionMode = $('precipitation', datos).attr("mode");
                var descripcion = $('weather', datos).attr("value");
                var horaMedida = $('lastupdate', datos).attr("value");
                var horaMedidaMiliSeg1970 = Date.parse(horaMedida);
                horaMedidaMiliSeg1970 -= minutosZonaHoraria * 60 * 1000;
                var horaMedidaLocal = (new Date(horaMedidaMiliSeg1970)).toLocaleTimeString("es-ES");
                var fechaMedidaLocal = (new Date(horaMedidaMiliSeg1970)).toLocaleDateString("es-ES");
                var icono = $('weather', datos).attr("icon");

                var stringDatos = ""
                stringDatos += "<h2>Ciudad: " + ciudad + ", " + pais + "<img src=https://openweathermap.org/img/w/" + icono + ".png alt=\"Icono " + descripcion + "\"> </h2>";
                stringDatos += "<ul>"
                stringDatos += "<li>Longitud: " + longitud + " grados</li>";
                stringDatos += "<li>Latitud: " + latitud + " grados</li>";
                stringDatos += "<li>Amanece a las: " + amanecerLocal + "</li>";
                stringDatos += "<li>Oscurece a las: " + oscurecerLocal + "</li>";
                stringDatos += "<li>Temperatura: " + temperatura + " grados Celsius</li>";
                stringDatos += "<li>Temperatura mínima: " + temperaturaMin + " grados Celsius</li>";
                stringDatos += "<li>Temperatura máxima: " + temperaturaMax + " grados Celsius</li>";
                stringDatos += "<li>Temperatura (unidades): " + temperaturaUnit + "</li>";
                stringDatos += "<li>Humedad: " + humedad + " " + humedadUnit + "</li>";
                stringDatos += "<li>Presión: " + presion + " " + presionUnit + "</li>";
                stringDatos += "<li>Velocidad del viento: " + velocidadViento + " metros/segundo</li>";
                stringDatos += "<li>Nombre del viento: " + nombreViento + "</li>";
                stringDatos += "<li>Dirección del viento: " + direccionViento + " grados</li>";
                stringDatos += "<li>Código del viento: " + codigoViento + "</li>";
                stringDatos += "<li>Nombre del viento: " + nombreDireccionViento + "</li>";
                stringDatos += "<li>Nubosidad: " + nubosidad + "</li>";
                stringDatos += "<li>Nombre nubosidad: " + nombreNubosidad + "</li>";
                stringDatos += "<li>Visibilidad: " + visibilidad + " metros</li>";
                stringDatos += "<li>Precipitación valor: " + precipitacionValue + "</li>";
                stringDatos += "<li>Precipitación modo: " + precipitacionMode + "</li>";
                stringDatos += "<li>Descripción: " + descripcion + "</li>";
                stringDatos += "<li>Hora de la medida: " + horaMedidaLocal + "</li>";
                stringDatos += "<li>Fecha de la medida: " + fechaMedidaLocal + "</li>";
                stringDatos += "</ul>"

                index++
                var whereStr = "body main article section:nth-of-type("+index+")"
                $(whereStr).html($(whereStr).html() + stringDatos)
                if (index < 5)
                    self.cargarDatos(index)
            },
            error: function () {
                $("h3").html("¡Tenemos problemas! No puedo obtener XML de <a href='https://openweathermap.org'>OpenWeatherMap</a>");
                $("img").remove();
                $("h4").remove();
                $("h5").remove();
                $("p").remove();
            }
        });
    }
}
var meteo = new Meteo();

/* Ejemplo de XML recibido de http://openweathermap.org
<?xml version="1.0" encoding="UTF-8"?>
<current>
    <city id="3114711" name="Oviedo">
        <coord lon="-5.84" lat="43.36"/>
        <country>ES</country>
        <sun rise="2017-11-19T07:23:01" set="2017-11-19T16:54:35"/>
    </city>
    <temperature value="10" min="10" max="10" unit="metric"/>
    <humidity value="81" unit="%"/>
    <pressure value="1023" unit="hPa"/>
    <wind>
        <speed value="2.6" name="Light breeze"/>
        <gusts/>
        <direction value="160" code="SSE" name="South-southeast"/>
    </wind>
    <clouds value="0" name="clear sky"/>
    <visibility value="10000"/>
    <precipitation mode="no"/>
    <weather number="800" value="cielo claro" icon="01n"/>
    <lastupdate value="2017-11-19T22:30:00"/>
</current>
 */