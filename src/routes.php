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

/*
PENCARIAN DATA SST pada NC Sadewa
*/

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


//satu titik data
$app->get("/sadewa/sst/", function (Request $request, Response $response, $args){
    $cari_lat = $request->getQueryParam("lat");
    $cari_lon = $request->getQueryParam("lon");
    $cari_tgl = $request->getQueryParam("tgl");
    $cari_jam = $request->getQueryParam("jam");

    // pemasukan data yang dicari
    $data = cari_data($cari_lat,$cari_lon,$cari_tgl,$cari_jam);

    //rubah ke json
    $myJSON = json_encode(["status" => "success", "data_SST_Ke_1" => $data],200);
    echo $myJSON;
});


//dua titik dengan lon tetap dan lat berubah
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

    //perbandingan untuk lat awal dan akhir
    if ($lat_1 < $lat_2) {
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
    }elseif ($lat_1 > $lat_2) {
         //loop
        $i=1;
        while ($lat_1>=$lat_2) {
            //cari data dengan fungsi
            $data = cari_data($lat_1,$cari_lon,$cari_tgl,$cari_jam,$i);
           
            //var_dump ($data);
            $myJSON = json_encode(["status" => "success", "data_SST_Ke_".$i => $data],200);
            echo $myJSON;

             //iterasi
            $i=$i+1;
            $lat_1 = $lat_1-0.1;
        }
    }
    
});

//dua titik dengan lat tetap dan lon berubah
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

    //perbandingan untuk lon awal dan akhir
    if ($lon_1 < $lon_2) {
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
    }elseif ($lon_1 > $lon_2) {
        //loop
        $i=1;
        while ($lon_1>=$lon_2) {
            //cari data dengan fungsi
            $data = cari_data($cari_lat,$lon_1,$cari_tgl,$cari_jam,$i);
           
            //var_dump ($data);
            $myJSON = json_encode(["status" => "success", "data_SST_Ke_".$i => $data],200);
            echo $myJSON;

             //iterasi
            $i=$i+1;
            $lon_1 = $lon_1-0.1;
        }
    }
    
});

//2 titik atau lebih dengan lat dan lon berubah-ubah
$app->get("/sadewa/sst/range/", function (Request $request, Response $response, $args){
    $cari_lat_awal = $request->getQueryParam("lat_awal");
    $cari_lat_akhir = $request->getQueryParam("lat_akhir");

    $cari_lon_awal = $request->getQueryParam("lon_awal");
    $cari_lon_akhir = $request->getQueryParam("lon_akhir");

    $cari_tgl = $request->getQueryParam("tgl");
    $cari_jam = $request->getQueryParam("jam");

    //conversi data ke float
    $lat_1 = floatval($cari_lat_awal);
    $lat_2 = floatval($cari_lat_akhir);
    $lon_1 = floatval($cari_lon_awal);
    $lon_2 = floatval($cari_lon_akhir);

    //reset lon
    $lon_reset = floatval($cari_lon_awal);

    //perbandingan untuk lat awal dan akhir
    if ($lat_1 < $lat_2 and $lon_1 < $lon_2) {
        //loop
        $i=1;
        //mencari data per lat nila lat awal lebih kecil dari lat akhir
        while ($lat_1<=$lat_2) {
            //untuk mencari data per lon dari lat yang sama, bila lon awal lebih kecil dari lon akhir
            while ($lon_1<=$lon_2) {
                //cari data dengan fungsi
                $data = cari_data($lat_1,$lon_1,$cari_tgl,$cari_jam,$i);
               
                //var_dump ($data);
                $myJSON = json_encode(["status" => "success", "data_SST_Ke_".$i => $data],200);
                echo $myJSON;

                //iterasi lon
                $i=$i+1;
                $lon_1 = $lon_1+0.1;
            }
            //iterasi lat
            $lat_1 = $lat_1+0.1;
            //reset lon
            $lon_1 = $lon_reset;

        }
    }
    elseif ($lat_1 < $lat_2 and $lon_1 > $lon_2) {
        //loop
        $i=1;
        //mencari data lat bila lat awal lebih kecil dari lat akhir
        while ($lat_1<=$lat_2) {
            //melakukan looping dengan while dengan kondisi lon awal lebih besar dari lon akhir
            while ($lon_1>=$lon_2) {
                //cari data dengan fungsi
                $data = cari_data($lat_1,$lon_1,$cari_tgl,$cari_jam,$i);
               
                //var_dump ($data);
                $myJSON = json_encode(["status" => "success", "data_SST_Ke_".$i => $data],200);
                echo $myJSON;

                 //iterasi untuk lon
                $i=$i+1;
                $lon_1 = $lon_1-0.1;
            }
            //iterasi untuk lat
            $lat_1 = $lat_1+0.1;

             //reset lon
            $lon_1 = $lon_reset;
        }
    }
    elseif ($lat_1 > $lat_2 and $lon_1 < $lon_2) {
        //loop
        $i=1;
        //perulangan untuk lat awal lebih besar dari lat akhir
        while ($lat_1>=$lat_2) {
            //perulangan untuk lon awal lebih kecil dari lon akhir
            while ($lon_1<=$lon_2) {
                //cari data dengan fungsi
                $data = cari_data($lat_1,$lon_1,$cari_tgl,$cari_jam,$i);
               
                //var_dump ($data);
                $myJSON = json_encode(["status" => "success", "data_SST_Ke_".$i => $data],200);
                echo $myJSON;

                 //iterasi untuk lon
                $i=$i+1;
                $lon_1 = $lon_1+0.1;
            }
            //iterasi untuk lat
            $lat_1 = $lat_1-0.1;

            //reset lon
            $lon_1 = $lon_reset;
        }
    }
    elseif ($lat_1 > $lat_2 and $lon_1 > $lon_2) {
        //loop
        $i=1;
        //perulangan untuk lat awal lebih besar dari lat akhir
        while ($lat_1>=$lat_2) {
            //perulangan untuk lon awal lebih besar dari lon akhir
            while ($lon_1>=$lon_2) {
                //cari data dengan fungsi
                $data = cari_data($lat_1,$lon_1,$cari_tgl,$cari_jam,$i);
               
                //var_dump ($data);
                $myJSON = json_encode(["status" => "success", "data_SST_Ke_".$i => $data],200);
                echo $myJSON;

                 //iterasi untuk lon
                $i=$i+1;
                $lon_1 = $lon_1-0.1;
            }
            //iterasi untuk lat
            $lat_1 = $lat_1-0.1;

            //reset lon
            $lon_1 = $lon_reset;
        }
    }

});


/*=========================================================================================================*/