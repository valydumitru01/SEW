# 02000-XML.py
# # -*- coding: utf-8 -*-
""""
Procesamiento genérico de archivos XML
"""
import xml.etree.ElementTree as ET
import re
def verXML(archivoXML):
    """Función crearHTML(archivoXML)
        Genera un HTML con el arbol genealogico XML
    """
    try:      
        arbol = ET.parse(archivoXML)        
    except IOError:
        print ('No se encuentra el archivo ', archivoXML)
        exit()
    except ET.ParseError:
        print("Error procesando en el archivo XML = ", archivoXML)
        exit()

    raiz = arbol.getroot()
    parseXml2Html("arbol.html", raiz)

def parseXml2Html(archivoHTML, raiz):
    
    f = open(archivoHTML, "w",encoding="utf-8")
    namespaces = get_namespace(raiz)
    title="{}".format(raiz[0].attrib.get('nombre'))+" "+"{}".format(raiz[0].attrib.get('apellidos'))
    headerImg="{}".format(raiz.find('./'+namespaces+'persona/'+namespaces+'datos/'+namespaces+'foto').text)
    
    upper="""
<!DOCTYPE HTML>
<html lang="es">
    
    <head>
        <!-- Datos que describen el documento -->
        <meta charset="UTF-8" />
        <link rel="stylesheet" type="text/css" href="arbol.css" />
        <meta name="author" content="Valentin Dumitru"/>
        <meta name="viewport" content="initial-scale=device-width, maximum-scale=1">
        <meta name="description" content="Representacion de arbol genealogico"/>
        <title>Arbol genealogico</title>
    </head>
    
    <body>
        <!-- Datos con el contenidos que aparece en el navegador -->
        <header>
            <h1>"""+title+"""</h1>
            <img src= \"../../multimedia/"""+headerImg+"""\" alt= \""""+headerImg.replace('.jpg','')+"""\" />
        </header>
        <main>
    """
    body=createHtmlList(raiz)
    bottom="""
</main>
<footer>
    <p>Este arbol fue generado automaticamente mediante un algorimo que transforma un xml en html.</p>
</footer>
</body>
</html>"""
    f.write(upper)
    f.write(body)
    f.write(bottom)
    f.close()
def createHtmlList(raiz):
    return createHtmlListRec(raiz[0])

def get_namespace(element):
  m = re.match('\{.*\}', element.tag)
  return m.group(0) if m else ''

def stripNamespace(string,namespace):
    return string.replace(namespace,'')

def createHtmlListRec(nodePersona):
    namespaces = get_namespace(nodePersona)
    ret='<ul>\n'
    ret+='<li>'
    ret+="{}".format(nodePersona.attrib.get('nombre'))
    ret+=' '
    ret+="{}".format(nodePersona.attrib.get('apellidos'))
    ret+='</li>'
    for dato in nodePersona.find(namespaces+'datos'):
        ret+=stripNamespace("<li>{}".format(dato.tag)+'</li>',namespaces)
        #Si tiene texto
        if(dato.text!=None):
            if(dato.tag==namespaces+'foto'):
                ret+='<li><img src= \"../../multimedia/'+dato.text+'\" alt= \"'+dato.text.replace('.jpg','')+'\"/></li>'
            elif(dato.tag==namespaces+'video'):
                ret+='<li><video controls>'+'<source src=\"../../multimedia/'+dato.text+'\" type=\"video/mp4\">'+'Your browser does not support the video tag.'+'</video></li>'
            else:
                ret+='<li>'
                print(" {}".format(dato.text))
                ret+=" {}".format(dato.text)
                ret+='</li>'
        #Si tiene atributos
        if(len(dato.attrib)>1):
            ret+='<li><ul>\n'
            for atributo in dato.attrib:
                ret+='<li>'+atributo+': '+dato.attrib[atributo]+'</li>'
            ret+='</ul></li>'
    
    if(len(nodePersona)>1):
        ret+='<li>\n'
        print("{}".format(nodePersona[1].attrib.get('nombre')))
        ret+='\n'+createHtmlListRec(nodePersona[1])
        ret+='</li>'

        ret+='<li>\n'
        print("{}".format(nodePersona[2].attrib.get('nombre')))
        ret+='\n'+createHtmlListRec(nodePersona[2])
        ret+='</li>'
    
    ret+='</ul>\n'
    return ret
        

def main():
    """Prueba de la función verXML()"""
    print(verXML.__doc__)
    miArchivoXML = input('Introduzca un archivo XML = ')
    verXML(miArchivoXML)
if __name__ == "__main__":
    main()

