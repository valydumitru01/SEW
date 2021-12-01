
"use strict";
class Geolocalizacion {
    initMap() {
        var centro = { lat: 43.3672702, lng: -5.8502461 };
        var mapaGeoposicionado = new google.maps.Map(document.getElementById('mapa'), {
            zoom: 8,
            center: centro,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        var marker = new google.maps.Marker({
            position: centro,
            map: mapaGeoposicionado,
            title: 'Mi Posicion'
        }); 

        var infoWindow = new google.maps.InfoWindow;
        var self=this
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                console.log(pos)
                infoWindow.setPosition(pos);
                infoWindow.setContent('Localizacion encontrada');
                marker.setPosition(pos)
                infoWindow.open(mapaGeoposicionado);
                mapaGeoposicionado.setCenter(pos);
            }, function () {
                
                handleLocationError(true, infoWindow, mapaGeoposicionado.getCenter());
            });
        } else {
            // Browser doesn't support Geolocation
            handleLocationError(false, infoWindow, mapaGeoposicionado.getCenter());
        }
    }
    handleLocationError(browserHasGeolocation, infoWindow, pos) {
        infoWindow.setPosition(pos);
        infoWindow.setContent(browserHasGeolocation ?
            'Error: Ha fallado la geolocalizacion' :
            'Error: Su navegador no soporta geolocalizacion');
        infoWindow.open(mapaGeoposicionado);
    }
}



var miPosicion = new Geolocalizacion();

