<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

//satu data
$app->get("/sadewa/sst/", function (Request $request, Response $response, $args){
    $cari_lat = $request->getQueryParam("lat");
    $cari_lon = $request->getQueryParam("lon");
    $cari_tgl = $request->getQueryParam("tgl");
    $cari_jam = $request->getQueryParam("jam");

    // Data yang akan dipindah ke python
    $data = array($cari_lat,$cari_lon,$cari_tgl,$cari_jam);

    // Execute the python script with the JSON data
    $result = shell_exec('python "/var/www/html/lapan-api/src/API/SST_Sadewa.py" ' . base64_encode(json_encode($data)));

    // Decode the result
    $resultData = json_decode($result, true);

    return $response->withJson(["status" => "success", "data_SST" => $resultData], 200);
});

//fungsi cari data pada file nc
function cari_data($lat,$lon,$tgl,$jam,$i){
    // Data yang akan dipindah ke python
    $data = array($lat,$lon,$tgl,$jam);

    // Execute the python script with the JSON data
    $result = shell_exec('python "/var/www/html/lapan-api/src/API/SST_Sadewa.py" ' . base64_encode(json_encode($data)));

    // Decode the result
    $resultData = json_decode($result, true);

    return $resultData;
}

//dua titik dengan lon tetap
$app->get("/sadewa/sst/range/lat/", function (Request $request, Response $response, $args){
    /*
    lat awal harus lebih kecil nilainya dari lat akhir
    range lat dari -10 sampai 10
    iterasi ditambah 0.1
    */
    $cari_lat_awal = $request->getQueryParam("lat_awal");
    $cari_lat_akhir = $request->getQueryParam("lat_akhir");

    $cari_lon = $request->getQueryParam("lon");
    $cari_tgl = $request->getQueryParam("tgl");
    $cari_jam = $request->getQueryParam("jam");

    //conversi data ke float
    $lat_1 = floatval($cari_lat_awal);
    $lat_2 = floatval($cari_lat_akhir);

    //loop
    $i=1;
    while ($lat_1<=$lat_2) {
        //cari data dengan fungsi
        $data = cari_data($lat_1,$cari_lon,$cari_tgl,$cari_jam,$i);
       
        //var_dump ($data);
        $myJSON = json_encode(["status" => "success", "data_SST_Ke_".$i => $data],200);
        echo $myJSON;

         //iterasi
        $i=$i+1;
        $lat_1 = $lat_1+0.1;
    }
});

//dua titik dengan lat tetap
$app->get("/sadewa/sst/range/lon/", function (Request $request, Response $response, $args){
   /*
    lon awal harus lebih kecil nilainya dari lon akhir
    range lon dari 95.0 sampai 145.0
    iterasi ditambah 0.1
    */

    $cari_lat = $request->getQueryParam("lat");
    $cari_lon_awal = $request->getQueryParam("lon_awal");
    $cari_lon_akhir = $request->getQueryParam("lon_akhir");

    $cari_tgl = $request->getQueryParam("tgl");
    $cari_jam = $request->getQueryParam("jam");

    //conversi data ke float
    $lon_1 = floatval($cari_lon_awal);
    $lon_2 = floatval($cari_lon_akhir);

    //loop
    $i=1;
    while ($lon_1<=$lon_2) {
        //cari data dengan fungsi
        $data = cari_data($cari_lat,$lon_1,$cari_tgl,$cari_jam,$i);
       
        //var_dump ($data);
        $myJSON = json_encode(["status" => "success", "data_SST_Ke_".$i => $data],200);
        echo $myJSON;

         //iterasi
        $i=$i+1;
        $lon_1 = $lon_1+0.1;
    }
});


