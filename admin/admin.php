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

function cell( $data, $attr, $blank){
	if( $blank ) $data = "&nbsp;";
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

$langjson = json_decode( file_get_contents("../data/language.json"), false );
$lang = $langjson->language;
?>
<html lang="<?php echo $lang;?>">
<head>
	<!-- default skin is skin1 -->

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link id=hstyle rel="stylesheet" href="/style/admin.css" type="text/css" />
	<script>
		<?php
			include_once("admin_common.php");
			global $skin, $missions, $places;

		?>
	</script>
</head>
<body>
<div class=admin_topbar>
	<h1>Configuration</h1>
</div>
<form method=post action="/admin/adminsv.php">

<div class=admin_content>
		
		<div class=div_mclock>
			<div class=mclock_title>&nbsp;Main Clock</div>
			<input type=radio name=rsw_mars id=mars value=0 <?php if(!$skin->switch) echo "checked";?>> <label for=mars>Mars</label><br>
			<input type=radio name=rsw_mars id=earth value=1 <?php if($skin->switch) echo "checked";?>> <label for=earth>Earth</label>
		</div>

		<div class=div_selector>
			<div class=selector_title>&nbsp;Clock from</div>
			<input type=radio name=locopt id=loco_opt0 value=0 <?php if($skin->locopt == 0) echo "checked";?>> <label for=loco_opt0>Experiment</label><br>
			<input type=radio name=locopt id=loco_opt1 value=1 <?php if($skin->locopt == 1) echo "checked";?>> <label for=loco_opt1>Location</label><br>
			<input type=radio name=locopt id=loco_opt2 value=2 <?php if($skin->locopt == 2) echo "checked";?>> <label for=loco_opt2>Coordinates</label>
		</div>

		<div class=div_opts>
			<div class=opts_title>&nbsp;Options</div>
			<input type=checkbox name=stable id=stable value="on" <?php if( $skin->table ) echo "checked";?>> <label for=stable>Info</label><br>
			<input type=checkbox name=seconds id=seconds value="on" <?php if( $skin->seconds ) echo "checked";?>> <label for=seconds>Seconds</label><br>
		</div>

		<div class=div_exper>
			<div class=exper_title>&nbsp;Experiments</div>
			<select name=exper id=exper class="selector exper">
				<?php
					foreach( $missions as $m ){
						$e = $m->idx == $skin->experiment ? " selected" : "";
						echo "<option value=\"$m->idx\"$e>$m->name</option>\n";
					}
				?>
			</select>
		</div>

		<div class=div_places>
			<div class=places_title>&nbsp;Locations</div>
			<select name=places id=places class="selector places">
				<?php
					foreach( $places as $m ){
						$e = $m->idx == $skin->places ? " selected" : "";
						echo "<option value=\"$m->idx\"$e>$m->name</option>\n";
					}
				?>
			</select>
		</div>
		<div class="slidecontainer">
			<div class=latv id=latv>60</div>
  			<input name=lat type="range" min="0.00" step="0.01" max="360" value="<?php echo $skin->input->lat;?>" class="slider" id="latitude">
  			<label for=latitude>Lat</label>
  			<br>
			<div class=lonv id=lonv>80</div>
  			<input name=lon type="range" min="0.00" step="0.01" max="360" value="<?php echo $skin->input->lon;?>" class="slider" id="longitude">
  			<label for=longitude>Lon</label>
		</div>

		<div class=fsubmit>
		</div>

</div>
<div class=admin_footer>
	<input type=submit name="button" class="button bsubmit" value="Save Changes">
    <button id="options" name="button" class="button admin_return" onClick="location.href = '/index.php';" value="options">Return</button> &nbsp; &nbsp;
    <button id="shutdown" name="button" class="button admin_shutdown" onClick="location.href = '/admin/shutdown.php';" value="shutdown">Power</button>
</div>
</form>
<script>
	window.addEventListener("DOMContentLoaded", () => {
		var latitude = document.getElementById("latitude");
		var latitude_out = document.getElementById("latv");

		var longitude = document.getElementById("longitude");
		var longitude_out = document.getElementById("lonv");
		latitude_out.innerHTML = latitude.value; // Display the default slider value
		longitude_out.innerHTML = longitude.value; // Display the default slider value

		// Update the current slider value (each time you drag the slider handle)
		latitude.oninput = function() {
			latitude_out.innerHTML = this.value;
		}
		longitude.oninput = function() {
			longitude_out.innerHTML = this.value;
		}
		document.getElementById("shutdown").addEventListener("click", function(event){
			event.preventDefault();
			document.location.assign("/admin/shutdown.php");
		})
	});
</script>
</body>
</html>