<?php
// prelude ====================
include_once("lib/params.php");
$p = new parameters();
$loc = $p->index;
if( $loc === "" ) $loc = "/";
header("Location: $loc");
// prelude-end ================

?>