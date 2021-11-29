# # -*- coding: utf-8 -*-

## Import Libraries
import numpy as np
import matplotlib.pyplot as plt 
from bs4 import BeautifulSoup
import pandas as pd
from mpl_toolkits.mplot3d import Axes3D
import xml.etree.ElementTree as ET


## Opening the F-15 well Trajectory file
WITSML_file = "C:/Users/valyd/OneDrive - Universidad de Oviedo/Software Projects Classes/3º/Software y" \
              " Estandares para la Web/Lab/P-XML/Practica1/Ejercicio2/Tarea4/NA-NA-EnergisticsWell2016-A" \
              "/1/trajectory/1.xml"
 
# Reading the WITSML file
with open(WITSML_file) as f:
    data = f.read()
## Parse the WITSML file using the Beautiful library
data_xml = BeautifulSoup(data, 'xml')
# Print the tags in the file
temp = set([str(tag.name) for tag in data_xml.find_all()])
print ("\n".join(temp))

columns = ['azi', 'md', 'tvd', 'incl', 'dispNs', 'dispEw']
df = pd.DataFrame()
for col in columns:
    df[col] = [float(x.text) for x in data_xml.find_all(col)]
print(df)

## Plot the trajectory
fig = plt.figure()
ax = fig.add_subplot(projection='3d')

print('\npuntos dispEws:\n ' + str(df['dispEw']), '\npuntos dispNss: \n' + str(df['dispNs']), '\npuntos tvds: \n' + str(df['tvd']))

# define the axis parameters
ax.plot(df['dispNs'], df['dispEw'], df['tvd']*-1, '-r', linewidth = 2)

# format the plot
ax.set_xlabel('dispNs', size=20, labelpad=30)
ax.set_ylabel('dispEw', size=20, labelpad=30)
ax.set_zlabel('tvd', size=20, labelpad=30)
ax.tick_params(labelsize=15)

#set plot aspect ratio
ax.get_proj = lambda: np.dot(Axes3D.get_proj(ax), np.diag([0.5, 0.5,1.5, 1]))

fig.show()
