<?php
define("__DEBUG__", false);

function debug($text){
	if( __DEBUG__ ){
		echo "$text\n";
	}
}

function compare( $a, $b ){
	if( $a["code"] == $b["code"] ){
		return 0;
	}
	return ($a["code"] < $b["code"]) ? -1 : 1;
}

?>
