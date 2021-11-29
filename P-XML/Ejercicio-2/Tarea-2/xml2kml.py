# 02000-XML.py
# # -*- coding: utf-8 -*-
import re
import xml.etree.ElementTree as ET
def verXML(archivoXML):
    """Función crearKML(archivoXML)
        Genera un KML con el arbol genealogico XML
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
    parseXml2Kml("arbol.kml", raiz)
    
    
def get_namespace(element):
  m = re.match('\{.*\}', element.tag)
  return m.group(0) if m else ''



def parseXml2Kml(archivoKml, raiz):
    f = open(archivoKml, "w",encoding="utf-8")
    
    upper="""<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2"> <Document>
"""
    body=createXMLList(raiz)
    bottom="""</Document></kml>"""
    f.write(upper)
    f.write(body)
    f.write(bottom)
    f.close()
def createXMLList(raiz):
    return createXMLListRec(raiz[0])

def createXMLListRec(nodePersona):
    namespaces = get_namespace(nodePersona)
    head='<Placemark>'+'<name>'+"{}".format(nodePersona.attrib.get('nombre'))+"{}".format(nodePersona.attrib.get('apellidos'))+'</name>'+'<description>'
    
    mid='</description>'+'<Point>'+'<coordinates>'
    
    bottom='</coordinates>'+'</Point>'+'</Placemark>'
    
    ret=head
    ret+="Coordenada de nacimiento de: "+"{}".format(nodePersona.attrib.get('nombre'))
    ret+=mid
    for atrib in nodePersona.find(namespaces+'datos/'+namespaces+'coor_nacimiento').attrib:
        ret+=nodePersona.find(namespaces+'datos/'+namespaces+'coor_nacimiento').attrib[atrib]
        ret+=','
    ret=ret.rstrip(',')
    ret+=bottom
    if(nodePersona.find(namespaces+'datos/'+namespaces+'coor_fallecimiento')!=None):
        ret+=head
        ret+="Coordenada de fallecimiento de: "+"{}".format(nodePersona.attrib.get(namespaces+'nombre'))
        ret+=mid
        for atrib in nodePersona.find(namespaces+'datos/'+namespaces+'coor_fallecimiento').attrib:
            ret+=nodePersona.find(namespaces+'datos/'+namespaces+'coor_fallecimiento').attrib[atrib]
            ret+=','
        ret=ret.rstrip(',')
        ret+=bottom
        
    if(len(nodePersona)>1):
        print("{}".format(nodePersona[1].attrib.get('nombre')))
        ret+='\n'+createXMLListRec(nodePersona[1])
        print("{}".format(nodePersona[2].attrib.get('nombre')))
        ret+='\n'+createXMLListRec(nodePersona[2])
    
    return ret
        

def main():
    print(verXML.__doc__)
    miArchivoXML = input('Introduzca un archivo XML = ')
    verXML(miArchivoXML)
if __name__ == "__main__":
    main()

