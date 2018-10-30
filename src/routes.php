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

$app->get("/sadewa/sst/", function (Request $request, Response $response, $args){
    $cari_lat = $request->getQueryParam("lat");
    $cari_lon = $request->getQueryParam("lon");
    $cari_tgl = $request->getQueryParam("tgl");

    // Data yang akan dipindah ke python
    $data = array($cari_lat,$cari_lon,$cari_tgl);

    // Execute the python script with the JSON data
    $result = shell_exec('python "/var/www/html/lapan-api/src/API/SST_Sadewa.py" ' . base64_encode(json_encode($data)));

    // Decode the result
    $resultData = json_decode($result, true);

    return $response->withJson(["status" => "success", "data_SST" => $resultData], 200);
});