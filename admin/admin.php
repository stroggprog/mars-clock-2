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
$language = $langjson->language;
$lang = array();
include_once("../lang/settings.php"); 		// initialise array and sub-arrays
include_once("../lang/en/settings.php");	// populate with default data (english)
if( $language != "en" && is_file("lang/$language/settings.php") ){
	include_once("../lang/$language/settings.php");	// populate with partial or full optional language
}

// get list of language files
$lset = array();
$path = "../lang";
if( $dh = opendir($path) ){
	while( false !== ( $entry = readdir( $dh ) ) ){
		if( is_dir("$path/$entry") ){
			if( $entry != "." && $entry != ".." ){
				if( file_exists("$path/$entry/settings.php") ){
					$lset[] = $entry;
				}
			}
		}
	}
}
else {
	$lset[] = "Error";
}


?>
<html lang="<?php echo $language;?>">
<head>
	<!-- default skin is skin1 -->

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link id=hstyle rel="stylesheet" href="/style/admin.css" type="text/css" />
	<script>
		<?php
			include_once("admin_common.php");
			global $skin, $missions, $places;
			echo "let language_options = ". json_encode($lset);

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
			<div class=mclock_title>&nbsp;<?php echo $lang['admin']['titles']['main_clock'];?></div>
			<input type=radio name=rsw_mars id=mars value=0 <?php if(!$skin->switch) echo "checked";?>> <label for=mars><?php echo $lang['admin']['main_clock']['mars'];?></label><br>
			<input type=radio name=rsw_mars id=earth value=1 <?php if($skin->switch) echo "checked";?>> <label for=earth><?php echo $lang['admin']['main_clock']['earth'];?></label>
		</div>

		<div class=div_selector>
			<div class=selector_title>&nbsp;<?php echo $lang['admin']['titles']['clock_from'];?></div>
			<input type=radio name=locopt id=loco_opt0 value=0 <?php if($skin->locopt == 0) echo "checked";?>> <label for=loco_opt0><?php echo $lang['admin']['clock_from']['experiment'];?></label><br>
			<input type=radio name=locopt id=loco_opt1 value=1 <?php if($skin->locopt == 1) echo "checked";?>> <label for=loco_opt1><?php echo $lang['admin']['clock_from']['location'];?></label><br>
			<input type=radio name=locopt id=loco_opt2 value=2 <?php if($skin->locopt == 2) echo "checked";?>> <label for=loco_opt2><?php echo $lang['admin']['clock_from']['coordinates'];?></label>
		</div>

		<div class=div_opts>
			<div class=opts_title>&nbsp;<?php echo $lang['admin']['titles']['options'];?></div>
			<input type=checkbox name=stable id=stable value="on" <?php if( $skin->table ) echo "checked";?>> <label for=stable><?php echo $lang['admin']['options']['info'];?></label><br>
			<input type=checkbox name=seconds id=seconds value="on" <?php if( $skin->seconds ) echo "checked";?>> <label for=seconds><?php echo $lang['admin']['options']['seconds'];?></label><br>
			<input type=checkbox name=lsubs id=lsubs value="on" <?php if( $skin->lsubs ) echo "checked";?>> <label for=lsubs><?php echo $lang['admin']['options']['lsubs'];?></label><br>

		</div>

		<div class=div_exper>
			<div class=exper_title>&nbsp;<?php echo $lang['admin']['titles']['experiments'];?></div>
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
			<div class=places_title>&nbsp;<?php echo $lang['admin']['titles']['locations'];?></div>
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
  			<label for=latitude><?php echo $lang['admin']['slider']['lat'];?></label>
  			<br>
			<div class=lonv id=lonv>80</div>
  			<input name=lon type="range" min="0.00" step="0.01" max="360" value="<?php echo $skin->input->lon;?>" class="slider" id="longitude">
  			<label for=longitude><?php echo $lang['admin']['slider']['lon'];?></label>
		</div>

		<div class=fsubmit>
		</div>

</div>
<div class=admin_footer>
	<input type=submit name="button" class="button bsubmit" value="<?php echo $lang['admin']['button']['save'];?>">

<!--	<div class=language_div> -->
    <label for="language" class="language_label"><?php echo $lang['admin']['lang'];?> &nbsp;</label><select name=language id=language class="language_selector">
    
    	<?php
    		foreach( $lset as $opt ){
    			$e = $opt == $language ? " selected" : "";
    			echo "<option value=\"$opt\"$e>$opt</option>\n";
    		}
    	?>
    	
    </select>
<!--	</div> -->


    <button id="options" name="button" class="button admin_return" onClick="location.href = '/index.php';" value="options"><?php echo $lang['admin']['button']['return'];?></button> &nbsp; &nbsp;
    <button id="shutdown" name="button" class="button admin_shutdown" onClick="location.href = '/admin/shutdown.php';" value="shutdown"><?php echo $lang['admin']['button']['power'];?></button>
    

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
