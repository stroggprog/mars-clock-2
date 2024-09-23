<?php
function loadSkin( $file ){
	$file = adaptedPath( $file );
	$skin = file_get_contents($file);
	$skin = json_decode( $skin, false );
	return $skin;
}

function saveSkin( $file, $skin ){
	$file = adaptedPath( $file );
	return (file_put_contents($file, $skin ) == strlen($skin));
}

function adaptedPath( $path ){
	return __DIR__."/../$path";
}
?>