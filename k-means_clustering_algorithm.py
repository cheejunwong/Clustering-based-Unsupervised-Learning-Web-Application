import pandas as pd
import numpy as np
from sklearn.cluster import KMeans
import sys


data = sys.argv[1]
k = sys.argv[2]

dataset = pd.DataFrame(data)

kmeans = KMeans(n_clusters = k, init = 'k-means++')

kmeans = kmeans.fit(dataset)

labels = kmeans.predict(dataset)

centroids = kmeans.cluster_centers_

print(centroids)

