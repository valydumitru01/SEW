class Geolocalizacion {
    initMap() {
        var centro = { lat: 43.3672702, lng: -5.8502461 } 
        this.mapaGeoposicionado = new google.maps.Map(document.getElementById('mapa'), {
            zoom: 8,
            center: centro,
            mapTypeId: "terrain"
        }) 

    }
    loadGeoJsonString(geoFile) {
        try {

            var lector = new FileReader() 
            var self = this
            lector.onload = function (evento) {
                //El evento "onload" se lleva a cabo cada vez que se completa con éxito una operación de lectura
                //La propiedad "result" es donde se almacena el contenido del archivo
                //Esta propiedad solamente es válida cuando se termina la operación de lectura
                var geojsonString = lector.result
                console.log(geojsonString)
                const geojson = JSON.parse(geojsonString) 
                console.log(geojson)
                self.mapaGeoposicionado.data.addGeoJson(geojson) 
                self.zoom(self.mapaGeoposicionado) 
            }
            lector.readAsText(geoFile[0])

        } catch (e) {
            alert("Not a GeoJSON file!") 
        }
    }
    processPoints(geometry, callback, thisArg) {
        if (geometry instanceof google.maps.LatLng) {
            callback.call(thisArg, geometry) 
        } else if (geometry instanceof google.maps.Data.Point) {
            callback.call(thisArg, geometry.get()) 
        } else {
            geometry.getArray().forEach((g) => {
                processPoints(g, callback, thisArg) 
            }) 
        }
    }
    zoom(map) {
        const bounds = new google.maps.LatLngBounds() 
        map.data.forEach((feature) => {
            const geometry = feature.getGeometry() 

            if (geometry) {
                this.processPoints(geometry, bounds.extend, bounds) 
            }
        }) 
        map.fitBounds(bounds) 
    }
}


var miPosicion = new Geolocalizacion() 

