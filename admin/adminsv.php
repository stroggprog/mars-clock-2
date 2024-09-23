<?php
header("Location: /");

include_once("../lib/params.php");
include_once("admin_common.php");
global $skin, $missions, $places;

$p = new parameters();

if( $p->button != "Save Changes" ){
	exit;
}

// checkbox only returns a value if checked, so any non-empty value means checked
// the parameters class returns "" when no value is available or variable does not exist
$rsw_mars 	= (int)$p->rsw_mars;
$locopt 	= (int)$p->locopt;
$exper 		= (int)$p->exper;
$place 		= (int)$p->places;
$stable 	= $p->stable == "" ? false : true;
$secs		= $p->seconds == "" ? false : true;

// dummy these for now
$lat = (float)$p->lat;
$lon = (float)$p->lon;

$data = "rsw_mars=$rsw_mars\n".
		"locopt=$locopt\n".
		"stable=$stable\n".
		"seconds=$secs\n".
		"exper=$exper\n".
		"places=$place\n".
		"lat=$lat\n".
		"lon=$lon\n";

file_put_contents("form_data.txt", $data);

// rsw_mars dictates which clock is the primary (large) clock
$skin->switch = $rsw_mars == 1 ? true : false;

// stable determines whether table data is displayed
$skin->table = $stable;

// whether seconds are displayed
$skin->seconds = $secs;

// record indexes of experiment and places files even if we don't use them
$skin->experiment = $exper;
$skin->places = $place;

// record latitude/longitude from manual input
$skin->input->lat = $lat;
$skin->input->lon = $lon;

// locopt dictates which source the location data is taken from
// 0 = Experiments, 1 = places, 2 = lat/lon input
$skin->locopt = $locopt;

if( $locopt == 0 ){
	$skin->siteinfo = $missions[$exper];
}
else if( $locopt == 1 ){
	$skin->siteinfo = $places[$place];
}
else {
	// chose manual input of lat/lon
	$skin->siteinfo = $dummy_site;
	$skin->siteinfo->lat = $lat;
	$skin->siteinfo->lon = $lon;
}


saveSkin();


?>
