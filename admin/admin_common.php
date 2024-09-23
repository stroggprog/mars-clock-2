<?php
$dataDir = __DIR__."/../data/";
$skin = json_decode( file_get_contents($dataDir."skin.json"), false );
$missions = json_decode( file_get_contents( $dataDir."missions.json"), false )->landers;
$places = json_decode( file_get_contents( $dataDir."places.json"), false )->places;

$dummy_site = '{
        "lat": 0,
        "lon": 0,
        "code": "",
        "name": "",
        "type": "",
        "msd_offset": 0,
        "status": ""
    }';

$dummy_site = json_decode( $dummy_site, false );

function saveSkin(){
	global $dataDir, $skin;
	file_put_contents($dataDir."skin.json", json_encode($skin, JSON_PRETTY_PRINT));
}

?>
