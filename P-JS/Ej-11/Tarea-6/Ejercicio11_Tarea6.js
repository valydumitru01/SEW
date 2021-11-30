
"use strict";
class Geolocalizacion {
    initMap() {
        var centro = { lat: 43.3672702, lng: -5.8502461 };
        var mapaGeoposicionado = new google.maps.Map(document.getElementById("mapa"), {
            zoom: 8,
            center: centro,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        var marker = new google.maps.Marker({
            position: centro,
            map: mapaGeoposicionado,
            title: 'Mi Posicion'
        }); 
        var self=this
        google.maps.event.addListener(mapaGeoposicionado, "rightclick", function(event) {
            var lat = event.latLng.lat(); 
            var lng = event.latLng.lng();
            var pos = {
                lat: lat,
                lng: lng
            };
            self.mostrarTiempo(lng, lat)
            marker.setPosition(pos);
            // populate yor box/field with lat, lng
            console.log("Lat=" + lat + "; Lng=" + lng);
        })
        this.mostrarTiempo(centro.lng, centro.lat)
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                self.mostrarTiempo(pos.lng, pos.lat)
                marker.setPosition(pos)
                mapaGeoposicionado.setCenter(pos);
            }, function () { 
                self.handleLocationError(true, infoWindow, mapaGeoposicionado.getCenter());
            });
        } else {
            // Browser doesn't support Geolocation
            handleLocationError(false, infoWindow, mapaGeoposicionado.getCenter());
        }
    }
    mostrarTiempo(long, lat){
        this.tiempo=new Tiempo(long, lat)
    }
    handleLocationError(browserHasGeolocation, infoWindow, pos) {
        infoWindow.setPosition(pos);
        infoWindow.setContent(browserHasGeolocation ?
            'Error: Ha fallado la geolocalizacion' :
            'Error: Su navegador no soporta geolocalizacion');
        infoWindow.open(this.mapaGeoposicionado);
    }
}
var miPosicion = new Geolocalizacion();

