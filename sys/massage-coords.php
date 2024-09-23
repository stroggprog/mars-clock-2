#!/usr/bin/php
<?php
/* coordinates.json is culled from Mars24
	coordinates2.json is the same file in pretty-print format for readability
*/
include_once("library.php");

$infile = "coordinates.json";
$pretty = "coordinates2.json";
$outfile = "places.json";
$data = json_decode( file_get_contents($infile), false );
file_put_contents( $pretty, json_encode($data, JSON_PRETTY_PRINT) );

$outdata = array();

foreach( $data->data as $o ){
	if( $o->code == "" ) $o->code = $o->name;
	$outdata[] = array( "lat" => $o->lat, "lon" => $o->lon, "code" => $o->code, "name" => $o->name, "type" => $o->vehicle_type );
}

uasort( $outdata, "compare" );

$ndata = array();
// prune numeric keys to recreate json array
$idx = 0;
foreach( $outdata as $o ){
	$s = '{"idx": '.$idx.', "lat": '.$o["lat"].', "lon": '.$o["lon"].', "code": "'.$o["code"].'", "name": "'.$o["name"].'", "type": "'.$o["type"].'"}';
	$ndata[] = $s;
	$idx++;
	debug($s);
}

$ndata = json_decode("{\"places\": [".implode(",", $ndata)."]}", false);
$data = json_encode( $ndata, JSON_PRETTY_PRINT);
file_put_contents( $outfile, $data );
debug($data);
rename( $outfile, "../data/$outfile");
?>
