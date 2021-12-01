class Geolocalizacion {
    initMap() {
        var centro = { lat: 43.3672702, lng: -5.8502461 };
        this.mapaGeoposicionado = new google.maps.Map(document.getElementById('mapa'), {
            zoom: 8,
            center: centro,
            mapTypeId: "terrain"
        });
        


    }
    cargarKml(files) {
        //Solamente toma un archivo
        //var archivo = document.getElementById("archivoTexto").files[0];
        var archivo = files[0];

        var lector = new FileReader();
        var self=this
        lector.onload = function (evento) {
            //El evento "onload" se lleva a cabo cada vez que se completa con éxito una operación de lectura
            //La propiedad "result" es donde se almacena el contenido del archivo
            //Esta propiedad solamente es válida cuando se termina la operación de lectura
            var kml=lector.result;
            var kmlDoc = $.parseXML( kml ),
            $kml = $( kmlDoc ),
            coordenadas = $kml.find( "coordinates" );
            console.log(coordenadas)
            self.colocarMarcadores(coordenadas)
        }
        console.log(lector.readAsText(archivo));
        
    }

    colocarMarcadores(coordenadas){
        var self = this
        for (let coorIndex = 0; coorIndex < coordenadas.length; coorIndex++) {
            let coordenada = coordenadas[coorIndex];
            console.log(coordenada["innerHTML"])
            var coordsStr=coordenada["innerHTML"].split(",")
            var coords={
                lng:parseFloat(coordsStr[0]),
                lat:parseFloat(coordsStr[1])
            }
            var marker = new google.maps.Marker({
                position: coords,
                map: self.mapaGeoposicionado,
                title: 'Mi Posicion'
            });
            self.mapaGeoposicionado.setCenter(coords)
            self.mapaGeoposicionado.setZoom(6)
        }

    }
}


var miPosicion = new Geolocalizacion();

