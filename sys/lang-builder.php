#!/usr/bin/php
<?php
include_once("../lib/readLines.php");

$langs = array();
foreach( readLineFromFile("languages.dat") as $line ){
	$r = explode( "\t", trim($line) );
	$langs[trim($r[0])] = trim($r[1]);
}
$json = json_encode( array( "language" => "en", "options" => $langs ), JSON_PRETTY_PRINT );
file_put_contents("language.json", $json);
?>
