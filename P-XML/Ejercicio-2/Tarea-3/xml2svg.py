# 02000-XML.py
# # -*- coding: utf-8 -*-
import re
import xml.etree.ElementTree as ET



def verXML(archivoXML):
    """Función crearSVG(archivoXML)
        Genera un SVG con el arbol genealogico XML
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
    parseXml2Svg("arbol.svg", raiz)

def parseXml2Svg(archivoSvg, raiz):
    f = open(archivoSvg, "w",encoding="utf-8")
    altura=10000
    anchura=10000
    upper="""
<html>
    <body>
    <svg height=\""""+toString(altura)+"""\" width=\""""+toString(anchura)+"""\" xmlns="http://www.w3.org/2000/svg">
"""
    body=createSvg(raiz)
    bottom="""
    </svg>
    </body>
</html>"""
    f.write(upper)
    f.write(body)
    f.write(bottom)
    f.close()



    
def get_namespace(element):
  m = re.match('\{.*\}', element.tag)
  return m.group(0) if m else ''




def casilla(colorFondo,colorLinea,altura,anchura,posX,posY):
    return "<rect x=\""+toString(posX)+"\" y=\""+toString(posY)+"\" height=\""+toString(altura)+"\" width=\""+toString(anchura)+"""\" stroke=\""""+colorLinea+"""\" stroke-width="3" fill=\""""+colorFondo+"""\"/>"""


def line(desdeX,desdeY,hastaX,hastaY,ancho,alto):
    desdeX+=ancho/2
    hastaX+=ancho/2
    desdeY+=alto/2
    hastaY+=alto/2
    return """
        <polyline points=\""""+toString(desdeX)+','+toString(desdeY) +' '+toString(hastaX)+','+toString(desdeY) +' '+toString(hastaX)+','+toString(hastaY)+"""\" style="fill:none;stroke:red;stroke-width:3" />
    """
    
def foto(src,altura,anchura,x,y):
    x+=anchura/4
    return '<image x=\"'+toString(x)+'\" y=\"'+toString(y)+'\" href=\"../../multimedia/'+toString(src)+'\" height=\"'+toString(altura)+'\"/>'

def toString(obj):
    return '{}'.format(obj)

def text(texto,altura,anchura,x,y):
    return """<text fill="#ffffff" font-size="10" font-family="Verdana" x=\""""+toString(x+10)+"\" y=\""+toString(y+10)+"\">\n"+texto+"\n</text>\n"""


def createSvg(raiz):
    return createSvgRec(raiz[0],4000,0)[0]

def createSvgRec(nodePersona, posXNodo, posYNodo):
    offsetX=0
    offsetY=0
    altura=100
    anchura=200
    alturaAtt=50
    namespaces = get_namespace(nodePersona)
    texto=nodePersona.attrib.get('nombre') +" "
    texto+=nodePersona.attrib.get('apellidos')
    
    #casilla de las personas
    ret=casilla("red","green",altura,anchura,posXNodo,posYNodo)
    ret+=text(texto,altura,anchura,posXNodo,posYNodo)
    
    offsetY+=alturaAtt*1.5
    for dato in nodePersona.find(namespaces+'datos'):
        offsetY+=alturaAtt*1.5
        ret += casilla("green", "red", alturaAtt, anchura, posXNodo + offsetX, posYNodo + offsetY)
        if(dato.tag==namespaces+'foto'):
            ret+=foto(dato.text,alturaAtt,anchura,posXNodo+offsetX,posYNodo+offsetY)
        else:
            texto=dato.tag+': '
            texto+='<tspan dy="1em" x=\"'+toString(posXNodo+10)+'\">'
            if(dato.text!=None): #Solo mostrar texto si hay texto
                texto+=dato.text
            if(len(dato.attrib)>1): #Solo seguir si exiten antecesores
                for att in dato.attrib:
                    texto+='<tspan dy="1em" x=\"'+toString(posXNodo+10)+'\">'
                    texto+=att
                    texto+= ': '+dato.attrib[att]
                    texto+='</tspan>'
                    texto+='\n'
            texto+='</tspan>\n'
            #Casillas de los atributos
            ret+=text(texto,alturaAtt,anchura,posXNodo+offsetX,posYNodo+offsetY)
        
    if(len(nodePersona)>1):
        
        #Matematica de offset...
        offsetY+=altura*2
        offsetX+=anchura
        offsetX*=2
        
        
        #Padre 1
        print(" {} {}".format(nodePersona[1].attrib.get('nombre'),nodePersona[1].attrib.get('apellidos')))
        ret+=line(posXNodo,posYNodo,posXNodo+offsetX*2,posYNodo+offsetY,anchura,altura)
        stri,offX,offY=createSvgRec(nodePersona[1],posXNodo+offsetX*2,posYNodo+offsetY)
        ret+=stri
        
        
        #Matematica de offset...
        offsetX+=offX*2
        offsetX+=anchura
        
        
        #Padre 2
        print(" {} {}".format(nodePersona[2].attrib.get('nombre'),nodePersona[1].attrib.get('apellidos')))
        ret+=line(posXNodo,posYNodo,posXNodo-offsetX/2,posYNodo+offsetY,anchura,altura)
        stri,offX,offY=createSvgRec(nodePersona[2],posXNodo-offsetX/2,posYNodo+offsetY)
        ret+=stri
        
        
        #Matematica de offset...
        offsetX+=offX
        offsetY+=offY
        
    return ret,offsetX,offsetY
        








def main():
    print(verXML.__doc__)
    miArchivoXML = input('Introduzca un archivo XML = ')
    verXML(miArchivoXML)
if __name__ == "__main__":
    main()

