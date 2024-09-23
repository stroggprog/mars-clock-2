<?php
/* shutdown calls in PHP:
	system("shutdown now"); // basic shutdown task
	system("shutdown -r now"); // reboot
	system("shutdown -h now"); // shutdown and halt
	system("shutdown -P now"); // shutdown and power off
*/
function makeTable( $aRows ){
	$r = "<table border=0 class=\"center\">";
	foreach( $aRows as $row ){
		$r .= $row;
	}
	return $r."</table>";
}

function cell( $data, $attr, $blank, $autoblank ){
	if( $blank || $autoblank == "" ) $data = "&nbsp;";
	else if( is_numeric($data) ){
		$data = number_format( $data, 6 );
	}
	return "<td $attr>$data</td>";
}

function makeRow( $aData ){
	$r = "<tr>";
	foreach( $aData as $cell ){
		$r .= $cell;
	}
	return $r."</tr>";
}

$langjson = json_decode( file_get_contents("data/language.json"), false );
$lang = $langjson->language;
?>
<html lang="<?php echo $lang;?>">
<head>
	<!-- default skin is skin1 -->
	<link id=hstyle rel="stylesheet" href="style/global.css" type="text/css" />
	<script>
		<?php
			$files = "";
			$fc = 0;
			$idir = __DIR__."/images/slides";
			$dataDir = __DIR__."/data/";
			$h = opendir($idir);
			while( false !== ( $entry = readdir($h) ) ){
				if( $entry != "." && $entry != ".." ){
					if( $fc ){
						$files .= ",";
					}
					$fc = 1;
					$files .= '"'.$entry.'"';
				}
			}
			echo "let slideDir = '/images/slides/'\n";
			echo "let slides = [$files];\n";
			//$siteInfo = "Lorem Ipsum has been the industry's standard dummy text ever since the 1500s";
			$skin = file_get_contents($dataDir."skin.json");
			echo "let skin = $skin;\n";
			$skin = json_decode( $skin, false );
			$site = $skin->siteinfo;

			$rows = array();
			$blank = !$skin->table;
			$rows[] = makeRow( array(cell( "Code", "class=left", $blank, $site->code), cell( $site->code, "class=right", $blank, $site->code )) );
			$rows[] = makeRow( array(cell( "Name", "class=left", $blank, $site->name), cell( $site->name, "class=right", $blank, $site->name )) );
			$rows[] = makeRow( array(cell( "Type", "class=left", $blank, $site->type), cell( $site->type, "class=right", $blank, $site->type )) );
			$rows[] = makeRow( array(cell( "Lat" , "class=left", $blank, $site->lat), cell( $site->lat , "class=right", $blank, $site->lat )) );
			$rows[] = makeRow( array(cell( "Lon" , "class=left", $blank, $site->lon), cell( $site->lon , "class=right", $blank, $site->lon )) );

			$rows[] = makeRow( array(cell( "L<sub>S</sub>", "class=left", false, "1" ),cell( "", "id=lsubs class=right", false, "1")) );

		?>
	</script>
	<script type="module" src="/scripts/index.js" defer></script>
</head>
<body>
<div class=mdiv>
	<center>
		<div id="info" class="info2"><?php echo makeTable($rows);?></div>
		<div id="time" class=content>
			Initialising...
		</div>
	</center>
</div>
<img src="/images/gears-png-file-3c.png" class="gear" onClick="location.href = '/admin/admin.php';">
<div id=debug class=debug></div>
<script>

</script>
</body>
</html>
