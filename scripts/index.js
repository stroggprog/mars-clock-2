let __DEBUG__ = false;
//##################################################
// Ls                 0     30     60     90     120    150    180    210    240   300    330    360/0
// month              1      2      3      4      5      6      7      8      9     10     11     12
// dust storm                                         ===============================================
// sol range start   0.0   61.2  126.6  193.3  257.8  317.5  371.9  421.6  468.5  514.6  562.0  612.9
// sol range ends   61.2  126.6  193.3  257.8  317.5  371.9  421.6  468.5  514.6  562.0  612.0  668.6
// duration (sols)  61.2   65.4   66.7   64.5   59.7   54.4   49.7   46.9   46.1   47.4   50.9   55.7
// aphelion Ls = 71 (month 3)  perihelion Ls = 251 (month 9)
let martian_months = [ 61, 126, 193, 257, 317, 371, 421, 468, 514, 562, 612, 668 ];

let mars_moment = 1.027491261574074;

let martian_day = 88775.244147;
let martian_year = 668.6 * martian_day;
let martian_sec = martian_day / 86400;
let martian_min = martian_sec * 60;
let martian_hour = martian_sec * 3600;
let elysium_offset = martian_hour*15;

let prime_meridian = false;

// add leap seconds (last update 2017)
// last checked 202-06-06
// https://hpiers.obspm.fr/eoppc/bul/bulc/bulletinc.dat
// http://maia.usno.navy.mil/ser7/tai-utc.dat
// ftp://hpiers.obspm.fr/iers/bul/bulc/bulletinc.dat
let TTUTC = 32.184;

let dbgEl;
let timeEl;
let lsubsEl;
let utc;
let tzOffset;
let infoEl;
let piri;
let debug1 = "";
let debug2 = "";

function longitude_time() {
    var s = new Date();
    var t = parseInt(s.getTime()/1000);
    tzOffset = s.getTimezoneOffset()*60;
    if( tzOffset < 0 ) tzOffset = Math.abs(tzOffset);
    else tzOffset = -Math.abs(tzOffset);
    if( skin.slide.slide && skin.slide.nextslide < t ){
    		randomImage(); // select random image
    		document.body.style.backgroundImage = randomImageURL(); // turn it into a url
    		skin.slide.nextslide = t+(skin.slide.interval-1);
    }

	while( (skin.leaptable.leaps[skin.leaptable.index].expires - skin.ntpdiff) < t && skin.leaptable.index < skin.leaptable.count ){
		skin.leaptable.index++;
	}
	if( skin.leaptable.index >= skin.leaptable.count ){
		window.reload();
	}
	if( (skin.leaptable.leaps[skin.leaptable.index].expires - skin.ntpdiff) < t ){
		window.reload();
	}
    utc = t;
    tzOffset = (tzOffset + utc) % 86400;
    //dumpText("some text");
    debug1 = skin.slide.name;
    var r = MartianTime(s);
    t = r[0];
    var lsubs = r[1];
    debug1 = 'L<sub>S</sub>: '+r[1];
    lsubsEl.innerHTML = lsubs;
    var time1 = secs2Time(tzOffset);
    var time2 = secs2Time( t % 86400 );
    var marker = " e";
    if( skin.switch ){
    		var t1 = time1;
    		time1 = time2;
    		time2 = t1;
    		marker = " m";
    }
    timeEl.innerHTML = '<span class=tiny1>'+time1+marker+'</span><br><span class=time>'+time2+'</span><br>'+
    										'<span class=tiny2> &nbsp; </span>';
}

function randomImage(){
		skin.slide.name = slides[Math.floor( Math.random() * slides.length )];
}

function randomImageURL(){
		return "url('"+slideDir+skin.slide.name+"')";
}

function secs2Time( seconds ){
		var h = 0;
		var m = 0;
		var s = seconds;

		if( s >= 3600 ){
			h = parseInt( s / 3600 );
			s = s % 3600;
		}
		if( s >= 60 ){
			m = parseInt( s / 60 );
			s = s % 60;
		}
		if( skin.seconds )	return tchunk(h)+':'+tchunk(m)+':'+tchunk(s);
		else {
			if( skin.pulse && seconds && seconds % 2){
				return tchunk(h)+'<span class=pulse-hidden>:</span>'+tchunk(m);
			}
			else {
				return tchunk(h)+'<span class=pulse-show>:</span>'+tchunk(m);
			}

		}

}

function tchunk( x ){
	x = '00'+x.toString();
	return x.slice(-2);
}

function normaliseEastWest( lon ){
	var dir = 'E';
	if( lon < 0 ){
		dir = 'W';
		lon = Math.abs(lon);
	}
	return [lon,dir];
}

function normaliseNorthSouth( lat ){
	var dir = 'N';
	if( lat < 0 ) dir = 'S';
	lat = Math.abs(lat)%90;
	return [lat,dir];
}

function solar_angle( l_s, nu_m, eot, jd_tt, piri ){
	var mst = (24*( ((jd_tt - 2451549.5) / 1.0274912517) + 44796.0 - 0.0009626 ))%24;
	var lon = piri.lon;
	if( normaliseEastWest(piri.lon) == "E" ){
		lon = 360 - lon;
	}
	lon = lon*(24/360);
	var lmst = (mst - lon)%24;
	var soldec = asin(0.42565*sin(l_s)) + 0.25 * sin(l_s);
	//conslog(toDegreesMinutesAndSeconds(soldec));

	var ltst = lmst + eot;

	// subsolar longitude
	var subsol = mst*(360/24) + eot + 180;
	var z = acos( sin(soldec) * sin(piri.lon) + cos(soldec) * cos(piri.lon) * cos(piri.lon - subsol) );

	var h_offset = acos( (sin(90-z) - sin(piri.lon) * sin( soldec )) / (cos(piri.lon) * cos( soldec )) );
	var w = (Math.atan(piri.lon)) * Math.tan(soldec);

	var r = 25.19*((90-z)/100);
	if( l_s > 180 ) r = -r;
	return r;
}

function radians_to_degrees(radians) {
  var pi = Math.PI;
  return radians * (180/pi);
}
function cos(deg) {
    return Math.cos(deg * Math.PI / 180);
}
function sin(deg) {
    return Math.sin(deg * Math.PI / 180);
}
function acos(deg) {
    return Math.acos(deg * Math.PI / 180);
}
function asin(deg) {
    return Math.asin(deg * Math.PI / 180);
}
function xtan(t) {
	return Math.tan(t);
}

function xcos(deg) {
    return Math.cos(deg);
}
function xsin(deg) {
    return Math.sin(deg);
}
function xacos(deg) {
    return Math.acos(deg);
}
function xasin(deg) {
    return Math.asin(deg);
}


function getMarsMonth( d ){
	m = 0;
	i = 0;
	while( martian_months[i] <= d ){
		m += 1;
		i += 1;
	}
	return m+1;
}

// this function courtesy of James Tauber
function altGetLSubS(j2000){
	var r = getEquationOfCentre(j2000);
	var nu_m = r[0];
	var m = r[1];
	var alpha_fms = r[2];
    var nu = nu_m + m;
    var l_s = (alpha_fms + nu_m) % 360;
    l_s += 0.00628; // bring into line with Mars24;
    return toFixed(l_s,5)+"&deg;";//" &nbsp; <span class=greyinfo>("+toFixed(l_s,5)+"&deg;)</span>";
}

function getEquationOfCentre(j2000){
	var m = (19.3870 + 0.52402075 * j2000) % 360;
	var alpha_fms = (270.3863 + 0.52403840 * j2000) % 360;
    var e = (0.09340 + 2.477E-9 * j2000);
    var pbs =
        0.0071 * cos((0.985626 * j2000 /  2.2353) +  49.409) +
        0.0057 * cos((0.985626 * j2000 /  2.7543) + 168.173) +
        0.0039 * cos((0.985626 * j2000 /  1.1177) + 191.837) +
        0.0037 * cos((0.985626 * j2000 / 15.7866) +  21.736) +
        0.0021 * cos((0.985626 * j2000 /  2.1354) +  15.704) +
        0.0020 * cos((0.985626 * j2000 /  2.4694) +  95.528) +
        0.0018 * cos((0.985626 * j2000 / 32.8493) +  49.095);
    var nu_m = (10.691 + 3.0E-7 * j2000) * sin(m) +
        0.623 * sin(2 * m) +
        0.050 * sin(3 * m) +
        0.005 * sin(4 * m) +
        0.0005 * sin(5 * m) +
        pbs;
    return [nu_m, m, alpha_fms];
}

// 214.7872340425532
// return value like .toFixed() but without rounding
function toFixed(num, fixed) {
    var re = new RegExp('^-?\\d+(?:\.\\d{0,' + (fixed || -1) + '})?');
    return num.toString().match(re)[0];
}

function modifiedJulianDate( jd ) {
	//jd = (jd - 2400001);// + 0.50400;
	jd = (jd - 2400001.001) + 0.50400;
	return jd;
}

function calcOffset(longitude){
	// 4 mins per degree = 240 secs
	return longitude*240;
}


function MartianTime(today){
		dumpText("#x1");
		//return parseInt(today.getTime()/1000);

		//elysium = piri;
		//whatisthetime();
		var sdate = new Date('1955-04-11');
		var start = sdate.getTime()/1000;
		//today = new Date();
		dumpText("#x2");
		TTUTC = 32.184+skin.tai;
		var meridian = ((today.getTime()+(TTUTC*1000)) - sdate.getTime())/1000;
		dumpText("#x3");

		var millis = today.getTime();
		millis -5000; // correction for new algorithms in Mars24J
    var jd_ut = 2440587.5 + (millis / 8.64E7);
    var jd_tt = jd_ut + (TTUTC + 32.184) / 86400; // 32.184 has to be added in again to align with Mars24 - error on their part?
    var j2000 = (jd_tt - 2451545.0) + 0.00005;
    var msd = (((j2000 - 4.5) / 1.027491252) + 44796.0 - 0.00096);
    var mtc = (24 * msd) % 24;

    //var mjd = modifiedJulianDate( jd_tt );
    //var mmsd = msd - 0.00036; // another fudge to align with Mars24

    var fudge = 24.85; // 31
    fudge -= 60.5;
    var mtc = (mtc * 3600)+fudge;  // convert from hours to seconds, apply the fudge
    var mtc = Math.floor(mtc);

    var lSubS = altGetLSubS(j2000);

    /*
		var xtstring = secs2Time(mtc);

		// this is a fluff, but it actually works. Amazing.
		var zyear = Math.floor(meridian / martian_year)+1;
		var zmonth = Math.floor(parseInt(lSubS)/30)+1;
		var zdays = Math.floor(((meridian % martian_year) / martian_day))+1;

		var mission_count = (parseInt(mmsd.toFixed()) - piri.msd_offset)+1;
		*/
	  var xxtc = Math.floor(mtc + calcOffset(piri.lon)) % 86400;
return [parseInt(xxtc),lSubS];
/*
    //conslog(mtc);
    //conslog(xxtc);
    if( xxtc < mtc && piri.lon < 180 ){
    	zdays++;
    	mission_count++;
    	if( zdays > 668 ){
    		zyear++;
    		zdays = 1;
    	}
    	zmonth = getMarsMonth( zdays );
    }
    else if( xxtc > mtc && piri.lon > 180 ){
		zdays--;
		mission_count--;
    	if( zdays == 0 ){
    		zyear--;
    		zdays = 668;
    	}
    	zmonth = getMarsMonth( zdays );
    }
    return parseInt(xxtc.toString());;

    nu_m = getEquationOfCentre(j2000)[0];
    l_s = parseFloat(lSubS);
	mars_eot = 2.861 * sin(2 * l_s) - 0.071 * sin(4 * l_s) + 0.002 * sin(6 * l_s) - nu_m;
	eoth = (mars_eot * 24 / 360)*3600;
	latOffset = 96*piri.lon;

	var h_offset = solar_angle( l_s, nu_m, mars_eot*24/360, jd_tt, piri );

	latOffset = (Math.abs( piri.lon - h_offset )*0.269)*60;

	var red = calcMiddayAndDayLen( jd_tt, piri, lSubS );

	ltst = Math.round((xxtc + eoth)%86400);

	// #############################
	// if sun and location in the same hemisphere, days are longer, else shorter
	if( (nu_m < 180 && piri.lon > 0) || (nu_m > 180 && piri.lon < 0) ){
		red = -red;
	}

	halfday = parseInt(21600+red);
	noon = parseInt(43200-eoth);

	sunrise = noon-halfday;
	sunset = noon+halfday;
	night = 86400 - (sunset - sunrise);

    colr = status_code_text[piri.status].code;
    //##################################################################################################
    mission_text = '<span style="color: '+colr+';">'+status_code_text[piri.status].text+"</span>";
    //##################################################################################################


    if( piri.status > 1 && piri.status < 4 ){
    	if( piri.status == 2 ){
    		mission_text = "sol "+(parseInt(mmsd.toFixed()) - piri.msd_offset)+" "+mission_text;
    	}
    	else {
   			mission_text = piri.active_date+' '+mission_text;  // use active date until we fix notes
    	}
    }

    if( piri.code ){
	    mclock = Math.floor((mtc + calcOffset(piri.lon))%86400);
	    mstatus = "<span style='color: "+status_code_text[piri.status].code+";'>"+status_code_text[piri.status].text+"</span>";

		document.getElementById("mission").innerHTML = secs2Time(mclock);
		document.getElementById('sstat').textContent = mission_count;
		document.getElementById('mstat').innerHTML = mstatus;

		document.getElementById('intended-latitude').textContent = piri.lat_norm[0];
		document.getElementById('intended-longitude').textContent = piri.lon_norm[0];
		document.getElementById('intended-latitude-dir').textContent = piri.lat_norm[1];
		document.getElementById('intended-longitude-dir').textContent = piri.lon_norm[1];

		adDate = new Date(piri.active_date);
		adDate = adDate.getTime()/1000;
		adSDate = today.getTime()/1000;
		adDate = adSDate - adDate;

		adTime = (adDate%86400);
		adDays = (adDate - adTime)/86400;
		document.getElementById('active_days').textContent = adDays;
	}
	else if( document.getElementById('vtype') ){
		document.getElementById('vtype').id = 'nvtype';
	}

	xxtc = parseInt(xxtc.toString());
	return xxtc;

	document.getElementById("actual").innerHTML = secs2Time(xxtc);
	document.getElementById("msd").textContent = mmsd.toFixed(5);
	document.getElementById("mjd").textContent = mjd.toFixed(5);

	// calendar
	document.getElementById("tyear").textContent = zyear;
	document.getElementById("tmonth").textContent = zmonth;
	document.getElementById("tsol").textContent = zdays;

	document.getElementById('lsubs').innerHTML = lSubS;
	document.getElementById('lsubsasdeg').innerHTML = convertDMS( 0, parseFloat(lSubS) )[1];


	document.getElementById('latitude').textContent = piri.actual_lat_norm[0];
	document.getElementById('longitude').textContent = piri.actual_lon_norm[0];
	document.getElementById('latitude-dir').textContent = piri.actual_lat_norm[1];
	document.getElementById('longitude-dir').textContent = piri.actual_lon_norm[1];

	// sunclock stuff
	if( config.skin == "skin_001" ){
		document.getElementById('sunrise').innerHTML = secs2Time(sunrise).substr(0,5);
		document.getElementById('sunset').innerHTML = secs2Time(sunset).substr(0,5);
		document.getElementById('daylen').innerHTML = secs2Time(sunset-sunrise).substr(0,5);
		document.getElementById('night').innerHTML = secs2Time(night).substr(0,5);
		updateSunClock(sunrise, sunset, xxtc);
		//conslog('done sunclock');
	}

	// v1.1 skins
	ltst = parseInt(ltst.toString());
	document.getElementById('ltst').innerHTML = secs2Time(ltst);

    return xxtc;
*/
}

function dumpText( text ){
		if( __DEBUG__ ) dbgEl.innerHTML = text;
}

window.addEventListener("DOMContentLoaded", () => {
  timeEl = document.querySelector("#time");
  piri = skin.siteinfo;
  dbgEl = document.querySelector("#debug");
  lsubsEl = document.querySelector("#lsubs");
  if( !skin.slide.slide ){
  		if( skin.slide.name == "" ) skin.slide.name = "75173f6dc13e559f632c40ac32237aa6.jpg";
  		document.body.style.backgroundImage = randomImageURL(); // turn it into a url
  }
  //infoEl = document.querySelector("#info");
  //infoEl.textContent = "Lorem Ipsum has been the industry's standard dummy text ever since the 1500s";
  window.setInterval( longitude_time, skin.interval );
  document.querySelector("#greet-form").addEventListener("submit", (e) => {
    e.preventDefault();
    greet();
  });
});
