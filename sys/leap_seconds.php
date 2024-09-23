#!/usr/bin/php
<?php
// This script is called externally (not by the web app), preferably as a cron job
// that runs once per month
define("LIB_DIR", "../lib/");
define("DATA_DIR","../data/");
define("LEAP_LIST_FILE", DATA_DIR."leap_list.txt");
define("SKIN_FILE", DATA_DIR."skin.json");
define("NTP_DIFF", 2208988800);
define("TEMP_DB", "leap-seconds-tmp.db");
define("LIVE_DB", "leap-seconds.db");

include_once(LIB_DIR."json.php");
include_once(LIB_DIR."sendMessage.php");
include_once(LIB_DIR."readLines.php");
include_once(LIB_DIR."db_sqlite.inc");

function NTPDate(){
	// get the current NTP date
	$expires = new DateTime();
	return $expires->getTimestamp()+NTP_DIFF;
}

function createDatabase(){
	if( file_exists(DATA_DIR.TEMP_DB) ){
		unlink( DATA_DIR.TEMP_DB );
	}
	$db = new DB_Sql();
	$db->Filename = DATA_DIR.TEMP_DB;
	$db->query("CREATE TABLE leap (ntp integer PRIMARY KEY, dtai integer)");
	$db->query("CREATE TABLE expire (expire integer PRIMARY KEY)");
	return $db;
}

function fetchExpiryDate($test){
	$r = $test-10;
	if( file_exists(DATA_DIR.LIVE_DB) ){
		$db = new DB_Sql();
		$db->Filename = DATA_DIR.LIVE_DB;
		$db->execute("select expire from expire");
		$r = $db->f("expire");
		$db->disconnect();
	}
	return $r;
}

$iers_list_url = "https://data.iana.org/time-zones/data/leap-seconds.list";

$expires = NTPDate();
$today = $expires;

$previous = fetchExpiryDate( $today );

if( $today > $previous ){
	$success = false;
	$data = sendMessage( $iers_list_url );

	file_put_contents(LEAP_LIST_FILE, $data);

	$db = createDatabase();
	$ncount = 0;

	foreach( readLineFromFile(LEAP_LIST_FILE) as $line ){
		$token = substr( $line, 0, 2 );
		if( $token == "#@"){
			// this is when the current list expires
			//echo "$line\n";
			$rexpires = (int)substr( trim($line), 2 );
			if( $rexpires > $expires ){
				//echo "$rexpires > $expires\n";
				$expires = $rexpires;
			}

		}
		if( substr( $line, 0, 1 ) != "#" ){
			// line of data
			$ntp = (int)trim( substr($line, 0, 12) );
			$secs = (int)trim( substr( $line, 12 ) );
			$ncount++;
			$db->query("insert into leap (ntp, dtai) values ($ntp, $secs)");
		}
	}
	$skin = json_decode(file_get_contents(SKIN_FILE), false);
	$db->query("insert into expire (expire) values ($expires)");
	$skin->leaptable->expires = $expires;


	// now we have all the data, fetch the current leap-second count based on NTP date
	$sql = "select * from leap where ntp < $today order by ntp desc limit 1";
	$db->execute($sql);
	$r = $db->as_obj();
	$date0 = dateFromNTP( $r->ntp );
	echo "Current Leap: $date0 ($r->ntp) S: $r->dtai\n";
	$leaps = array(json_decode("{\"ntptime\": $r->ntp, \"seconds\": $r->dtai, \"expires\": 0}", false) );
	$skin->leaptable->index = 0;
	$skin->tai = $r->dtai;

	$sql = "select * from leap where ntp > $today order by ntp asc limit 1";
	$db->query($sql);
	if( $db->num_rows() ){
		$r = $db->as_obj();
		$date1 = dateFromNTP( $r->ntp );
		echo "Next Leap: $date1 ($r->ntp) S: $r->dtai\n";
		$leaps[ count($leaps)-1 ]->expires = $r->dtai;
		$leaps[] = json_decode("{\"ntptime\": $r->ntp, \"seconds\": $r->dtai, \"expires\": 0}}", false);
	}
	else {
		echo "No upcoming leap second planned\n";
	}
	$leaps[ count($leaps)-1 ]->expires = $expires;
	$skin->leaptable->leaps = $leaps;
	$skin->leaptable->count = count($leaps);


	file_put_contents(SKIN_FILE, json_encode($skin, JSON_PRETTY_PRINT) );

	
	$db->disconnect();
	echo "$ncount records created.\n";
	rename(DATA_DIR.TEMP_DB, DATA_DIR.LIVE_DB);
}
else {
	echo "nothing to do\n";
}

function dateFromNTP( $ntp ){
	$d = new DateTime();
	$d->setTimeStamp( $ntp - NTP_DIFF );
	return $d->format("D jS M Y H:i:s");
}
?>