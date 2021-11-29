import numpy as np
import matplotlib.pyplot as plt
import xml.etree.ElementTree as ET
import re
from mpl_toolkits.mplot3d import Axes3D
"""
Este programa crea una grafica 3D de la trayectoria
de los pozos que se crean para ayudar con la extraccion de recursos, en este caso,
petroleo.


Documentacion de los datos extraidos del WITSML:
DispEw (Displacement east west)
LengthMeasure

East-west offset, positive to the East.
This is relative to wellLocation with a North axis orientation of aziRef.
If a displacement with respect to a different point is desired then
define a localCRS and specify local coordinates in location.

DispNs (Displacement north south)
LengthMeasure

North-south offset, positive to the North.
This is relative to wellLocation with a North axis orientation of aziRef.
If a displacement with respect to a different point is desired then
define a localCRS and specify local coordinates in location.

Tvd (True vertical depth)
WellVerticalDepthCoord
Vertical depth of the measurements
"""

def get_namespace(element):
  m = re.match('\{.*\}', element.tag)
  return m.group(0) if m else ''
def plot(raiz):
    tvds=[]
    dispNss=[]
    dispEws=[]
    dispEws,dispNss,tvds=createPoints(raiz,dispEws,dispNss,tvds)

    Plot3D(dispEws,dispNss,tvds)


def Plot3D(dispEws,dispNss,tvds):
    print('\npuntos dispEws: '+str(dispEws),'\npuntos dispNss: '+str(dispNss),'\npuntos tvds: '+str(tvds))

    fig = plt.figure()
    ax = fig.add_subplot(projection='3d')

    ax.plot(dispNss, dispEws, tvds, '-b', linewidth=2)

    ax.set_xlabel('dispNs', size=20, labelpad=10)
    ax.set_ylabel('dispEw', size=20, labelpad=10)
    ax.set_zlabel('tvd', size=20, labelpad=10)
    ax.tick_params(labelsize=10)
    #Proyectar la grafica
    ax.get_proj = lambda: np.dot(Axes3D.get_proj(ax), np.diag([0.75, 0.75, 1.5, 1]))

    fig.show()
    fig.savefig("figura.jpg")
def createPoints(raiz,dispEws,dispNss,tvds):
    namespaces = get_namespace(raiz)
    for elem in raiz.findall(namespaces+'trajectoryStation'):
        for tvdPoint in elem.findall(namespaces+'tvd'):
            print(tvdPoint.text)
            tvds.append(float(tvdPoint.text))
        for dispNsPoint in elem.findall(namespaces + 'dispNs'):
            print(dispNsPoint.text)
            dispNss.append(float(dispNsPoint.text))
        for dispEwPoint in elem.findall(namespaces + 'dispEw'):
            print(dispEwPoint.text)
            dispEws.append(-1*float(dispEwPoint.text))
    return dispEws,dispNss,tvds


def plotWITSML(archivoXML):
    try:
        arbol = ET.parse(archivoXML)

    except IOError:
        print('No se encuentra el archivo ', archivoXML)
        exit()
    except ET.ParseError:
        print("Error procesando en el archivo XML = ", archivoXML)
        exit()
    raiz = arbol.getroot()
    plot(raiz[0])

def main():
    plotWITSML(input('Nombre del archivo WITSML/XML = '))
if __name__ == "__main__":
    main()


