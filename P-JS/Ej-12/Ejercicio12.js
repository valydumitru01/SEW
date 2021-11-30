
if (window.File && window.FileReader && window.FileList && window.Blob) {
    //El navegador soporta el API File
    document.write("<p>Este navegador soporta el API File </p>");
}
else document.write("<p>¡¡¡ Este navegador NO soporta el API File y este programa puede no funcionar correctamente !!!</p>");

function calcularTamañoArchivos() {
    var nBytes = 0,
        archivos = document.getElementsByName("Archivos")[0].files,
        nArchivos = archivos.length;
    console.log(archivos)
    for (var i = 0; i < nArchivos; i++) {
        nBytes += archivos[i].size;
    }
    var nombresTiposTamaños = "<h2> Nombre de archivos </h2>";
    for (var i = 0; i < nArchivos; i++) {
        nombresTiposTamaños += "<p>Archivo[" + i + "] = " + archivos[i].name + " Tamaño: " + archivos[i].size + " bytes " + " Tipo: " + archivos[i].type + "</p>";
    }

    document.getElementsByTagName("p")[3].innerHTML = nArchivos;
    document.getElementsByTagName("p")[5].innerHTML = nBytes + " bytes";
    document.getElementsByTagName("section")[0].innerHTML = nombresTiposTamaños;
    console.log(document.getElementsByTagName("p"))
}