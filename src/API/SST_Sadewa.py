#Mencari data nc dari index
import numpy as np
import pandas as pd
import xarray as xr
import sys, json
import base64
import datetime
import time
import os

#open data
ds = xr.open_dataset('/home/misdan/Documents/Data/data.nc')

#fungsi untuk mencari terdekat
def find_closest(A, target):
    #A must be sorted
    idx = A.searchsorted(target)
    idx = np.clip(idx, 1, len(A)-1)
    left = A[idx-1]
    right = A[idx]
    idx -= target - left < right - target
    return idx

#search latitude
def find_lat(cari_lat):
	lat = ds['XLAT'].data[0][:,0]
	hasil_lat = find_closest(lat,cari_lat)
	return hasil_lat

#search longitude
def find_long(cari_long):
	lg = ds['XLONG'].data[0][0,:]
	hasil_lg = find_closest(lg,cari_long)
	return hasil_lg

#search time
def find_time(cari_waktu):
	#convert waktu ke numpy datetime64
	convert_waktu = np.datetime64(cari_waktu)
	waktu = ds['XTIME'].data
	hasil_waktu = find_closest(waktu,convert_waktu)
	return hasil_waktu

# Load JSON
try:
    data = json.loads(base64.b64decode(sys.argv[1]))
except:
    print "ERROR"
    sys.exit(1)

#Baca data
#lat
a_temp = data[0]
#lon
b_temp = data[1]
#tgl
c_temp = data[2]
#jam
d_temp = data[3]

#rubah format
a = float(a_temp)
b = float(b_temp)
#satukan tgl dan jam
c = " ".join([c_temp, d_temp])

#cari index
index_lat = find_lat(a)
index_lon = find_long(b)
index_waktu = find_time(c)

# south_north -> lat, west_east -> long
#search result
data_nilai = ds['SST'].sel(Time=index_waktu,west_east=index_lon,south_north=index_lat).data.tolist()
data_tgl = ds['SST'].sel(Time=index_waktu,west_east=index_lon,south_north=index_lat).XTIME.data.astype(str)
data_latitude = ds['SST'].sel(Time=index_waktu,west_east=index_lon,south_north=index_lat).XLAT.data.tolist()
data_longitude = ds['SST'].sel(Time=index_waktu,west_east=index_lon,south_north=index_lat).XLONG.data.tolist()
data_nama = ds['SST'].sel(Time=index_waktu,west_east=index_lon,south_north=index_lat).description
data_satuan = ds['SST'].sel(Time=index_waktu,west_east=index_lon,south_north=index_lat).units

# Generate some data to send to PHP
result = {
'nilai': data_nilai,
'tanggal': data_tgl,
'longitude': data_longitude,
'latitude': data_latitude,
'detail_nama': data_nama,
'detail_satuan': data_satuan
}

# Send it to stdout (to PHP)
print json.dumps(result)