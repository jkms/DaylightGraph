<?php
//Stolen from https://github.com/jnewbigin/sunrise

// http://en.wikipedia.org/wiki/Julian_day
// 0 = noon on January 1, 4713 BC Julian
// JD fractions of a day


// Constants

// There is a mix of degrees and radians here. In an ideal implementation, all angles would be
// in radians so we can easily use trig functions. However, orbital constants and coordinates
// are specified in degrees so we have to convert somewhere.

// The results of these calculations have been compared to
// http://www.esrl.noaa.gov/gmd/grad/solcalc/NOAA_Solar_Calculations_day.ods

$UNIX_daylen = 60 * 60 * 24; // 86400 (seconds)

// Julian Days
$J1970 = 2440587.5; // (days) // UNIX epoc
$J2000 = 2451545;  // (days)

// Earth Mean Anomaly (from J2000)
$M0 = 357.5291; // (degrees)
$M1 = 0.98560028; // (degrees / day)

// Solar transit
$J0 = 0.0009; // (days) time
$J1 = 0.0053; // (days) variation due to eccentricity
$J2 = -0.0069; // (days) variation due to obliquity
$J3 = 1; // (days) day length in days

// Kepler coefficients for earth orbit
$C1 = 1.9148; // constant
$C2 = 0.0200; // constant
$C3 = 0.0003; // constant

// Perihelion and the Obliquity of the Ecliptic
$P_deg = 102.9372; // earth ecliptic longitude Π
$e_rad = deg2rad(23.45); // earth obliquity of the equator ε

// sidereal time
$TH0 = 280.1600; // θ₀ (degrees)
$TH1 = 360.9856235; // θ₁ (degrees)

$h0 = -0.83; // (degrees) sunset angle
$d0 = 0.53;  // (degrees) sun diameter
$h1 = -6;    // (degrees) nautical twilight angle
$h2 = -12;   // (degrees) astronomical twilight angle
$h3 = -18;   // (degrees) darkness angle

// we don't want -ve results
function mod($dividend, $divisor)
{
	if($divisor > 0)
	{
		while($dividend < 0)
		{
			$dividend+= $divisor;
		}
	}
	return fmod($dividend, $divisor);
}

/**
 * Convert a unix timestamp into a julian date (and time)
 * @param $unixtime int seconds since 1970-01-01 00:00:00
 * @result $julian days since midday Jan 1, 4713 BC
 *
 */
function unixtimeToJulianDate( $unixtime ) 
{ 
	global $J1970;
	global $UNIX_daylen;

	$julian = $unixtime / $UNIX_daylen + $J1970; 

	return $julian;
}

/**
 * Convert a julian data (and time) into a unix timestamp
 * @param $julian float days since midday Jan 1, 4713 BC
 * @returns $unixtime int seconds since 1970-01-01 00:00:00
 */
function julianDateToUnixtime($julian) 
{ 
	global $J1970;
	global $UNIX_daylen;

	$unixtime = ($julian - $J1970) * $UNIX_daylen; 
	$unixtime = floor($unixtime);

	return $unixtime;
}
	
// (Jdate - 2451545 - 0.0009) - (lw/360)
/**
 * Calculate the julian cycle at this longitude (which day will the transit be on?)
 * @param $julian float julian date
 * @param $long float longitude in degrees
 */
function getJulianCycle($julian, $long_deg ) 
{ 
	global $J2000;
	global $J0;

	$cycle = round($julian - $J2000 - $J0 - $long_deg / 360); 

	return $cycle;
}
	
// solar noon J* = 2451545 + 0.0009 + (lw/360) + n
function getApproxSolarTransit($long_deg, $cycle, $h = 0 ) 
{ 
	global $J2000;
	global $J0;
	// we don't need J3 on earth

	return $J2000 + $J0 + (($h + $long_deg) / 360) + $cycle; 
}
	
// M = [357.5291 + 0.98560028 * (J* - 2451545)] mod 360
function getSolarMeanAnomaly($julian) 
{ 
	global $M0, $M1;
	global $J2000;

	$julian_day_number = floor($julian);

	$angle_deg = mod(($M0 + $M1 * ($julian_day_number - $J2000)), 360); 

	return $angle_deg;
}
	
// C = (1.9148 * sin(M)) + (0.0200 * sin(2 * M)) + (0.0003 * sin(3 * M)) 
function getEquationOfCenter( $anomaly_deg ) { 
	global $C1, $C2, $C3;

	$anomaly_rad = deg2rad($anomaly_deg);

	// this comes out in degrees as if by magic
	$angle_deg = $C1 * sin($anomaly_rad) + $C2 * sin(2 * $anomaly_rad) + $C3 * sin(3 * $anomaly_rad); 

	return $angle_deg;
}
	
// λ = (M + 102.9372 + C + 180) mod 360 
function getEclipticLongitude($anomaly) 
{ 
	global $P_deg;

	$elong = mod($anomaly + $P_deg + 180, 360);

	return $elong;
}
	
// Jtransit = J* + (0.0053 * sin(M)) - (0.0069 * sin(2 * λ)) 
function getSolarTransit($julian, $anomaly_deg, $elong_deg) 
{ 
	global $J1;
	global $J2;

	$anomaly_rad = deg2rad($anomaly_deg);
	$elong_rad = deg2rad($elong_deg);

	return $julian + ($J1 * sin($anomaly_rad)) + ($J2 * sin(2 * $elong_rad)); 
}
	
// δ = arcsin( sin(λ) * sin(23.45) )
function getSunDeclination( $elong ) { 
	global $e_rad;
	$elong = deg2rad($elong);

	$slong_rad = asin(sin($elong) * sin($e_rad)); 
	$slong_deg = rad2deg($slong_rad);

	return $slong_deg;
}

function getRightAscension($slong_deg) 
{
	global $e_rad;

	$slong_rad = deg2rad($slong_deg);

	$ascension_rad = atan2(sin($slong_rad) * cos($e_rad), cos($slong_rad));
	$ascension_deg = rad2deg($ascension_rad);

	return $ascension_deg;
}

function getSiderealTime($julian, $long_deg ) 
{
	global $TH0, $TH1;
	global $J2000;
	$sidereal = $TH0 + $TH1 * ($julian - $J2000) - $long_deg;
	$sidereal = mod($sidereal, 360);
	return $sidereal;
}

function getHourAngle1($sidereal, $ascension)
{
	$hour_deg = $sidereal - $ascension;

	return $hour_deg;
}

function getAzimuth($hour_deg, $lat_deg, $slong_deg) 
{
	$hour_rad = deg2rad($hour_deg);

	$lat_rad = deg2rad($lat_deg);

	$slong_rad = deg2rad($slong_deg);

	$azimuth_rad = atan2(sin($hour_rad), cos($hour_rad) * sin($lat_rad) - tan($slong_rad) * cos($lat_rad));
	$azimuth_deg = rad2deg($azimuth_rad);

	return $azimuth_deg;
}

function getAltitude($hour_deg, $lat_deg, $slong_deg) 
{
	$hour_rad = deg2rad($hour_deg);
	$lat_rad = deg2rad($lat_deg);
	$slong_rad = deg2rad($slong_deg);

	$altitude_rad = asin(sin($lat_rad) * sin($slong_rad) + cos($lat_rad) * cos($slong_rad) * cos($hour_rad));
	$altitude_deg = rad2deg($altitude_rad);

	return $altitude_deg;
}
	
// H = arccos( [sin(-0.83) - sin(ln) * sin(δ)] / [cos(ln) * cos(δ)] ) 
// sun_angle (-0.83 = sunset)
function getHourAngle2($sun_angle_deg, $lat_deg, $declination_deg) 
{ 
	$sun_angle_rad = deg2rad($sun_angle_deg);
	$lat_rad = deg2rad($lat_deg);
	$declination_rad = deg2rad($declination_deg);

	$hourangle_rad = acos((sin($sun_angle_rad) - sin($lat_rad) * sin($declination_rad)) / (cos($lat_rad) * cos($declination_rad))); 
	$hourangle_deg = rad2deg($hourangle_rad);

	return $hourangle_deg;
}

/*
	function getSunsetJulianDate( w0, M, Lsun, lw, n ) { 
		return getSolarTransit(getApproxSolarTransit(w0, lw, n), M, Lsun); 
	}
	
	function getSunriseJulianDate( Jtransit, Jset ) { 
		return Jtransit - (Jset - Jtransit); 
	}
	
	function getSunPosition( J, lw, phi ) {
		var M = getSolarMeanAnomaly(J),
			C = getEquationOfCenter(M),
			Lsun = getEclipticLongitude(M, C),
			d = getSunDeclination(Lsun),
			a = getRightAscension(Lsun),
			th = getSiderealTime(J, lw);
			
		return {
			azimuth: getAzimuth( th, a, phi, d ),
			altitude: getAltitude( th, a, phi, d )
		};
	}
*/

function latdegtolatdir($lat_deg)
{
	if($lat_deg >= 0)
	{
		$latdir = "{$lat_deg}°N";
	}
	else
	{
		$lat_deg = -$lat_deg;
		$latdir = "{$lat_deg}°S";
	}
	return $latdir;
}

function longdegtolongdir($long_deg)
{
	if($long_deg >= 0)
	{
		$longdir = "{$long_deg}°W";
	}
	else
	{
		$long_deg = -$long_deg;
		$longdir = "{$long_deg}°E";
	}
	return $longdir;
}

function get_sun_times($lat_deg, $long_deg, $time, $angle = false)
{
	global $verbose;

	if($verbose) echo "Calculating for ".latdegtolatdir($lat_deg).", ".longdegtolongdir($long_deg)." at ".date("c", $time)."\n";

	$julian = unixtimeToJulianDate($time);
	if($verbose) echo "Julian date is $julian\n";

	$cycle = getJulianCycle($julian, $long_deg);
	if($verbose) echo "Julian cycle at {$long_deg}° is $cycle\n";

	$anomoly = getSolarMeanAnomaly($julian);
	if($verbose) echo "Mean Anomaly is {$anomoly}°\n";

	$center = getEquationOfCenter($anomoly);
	if($verbose) echo "Equation of center is {$center}°\n";

	$anomoly = $anomoly + $center;
	if($verbose) echo "True anomaly = {$anomoly}°\n";

	$elong = getEclipticLongitude($anomoly);
	if($verbose) echo "The Ecliptic longitude is {$elong}°\n";

	$sun_long = getSunDeclination($elong);
	if($verbose) echo "The sun longitude is {$sun_long}°\n";

	$sun_ascension = getRightAscension($elong);
	if($verbose) echo "The sun right ascension is {$sun_ascension}°\n";

	$sidereal = getSiderealTime($julian, $long_deg);
	if($verbose) echo "The Sidereal Time is {$sidereal}°\n";

	$hourangle = getHourAngle1($sidereal, $sun_ascension);

	$azimuth = getAzimuth($hourangle, $lat_deg, $sun_long);
	if($verbose) echo "The sun azimuth is {$azimuth}°\n";

	$altitude = getAltitude($hourangle, $lat_deg, $sun_long);
	if($verbose) echo "The sun altitude is {$altitude}°\n";

	$transit = getApproxSolarTransit($long_deg, $cycle);
	if($verbose) echo "The solar transit is about {$transit}\n";

	$transit = getSolarTransit($transit, $anomoly, $elong );
	if($verbose) echo "The transit is at {$transit}\n";

	// The transit is 'solar noon' on that solar cycle

	if($verbose) echo date("c", julianDateToUnixtime($transit))."\n";

	global $h0, $h1, $h2, $h3;

	$angles = array('sun' => $h0,
	                'twilight' => $h1,
	                'nautical' => $h2,
			'astronomical' => $h3);

	$r = array();
	foreach($angles as $name => $angle)
	{
		if($verbose) echo "Using $name angle $angle\n";
		$h = getHourAngle2($angle, $lat_deg, $sun_long);
		if($verbose) echo "h = $h\n";

		// cheat method
		$sunset = $transit + $h / 360;
		$sunrise = $transit - $h / 360;
		if($verbose) echo "$name rise is at ".date("c", julianDateToUnixtime($sunrise))."\n";
		if($verbose) echo "$name set is at ".date("c", julianDateToUnixtime($sunset))."\n";
	
		$r["{$name}rise"] = julianDateToUnixtime($sunrise);
		$r["{$name}set"]  = julianDateToUnixtime($sunset);
	}

	return $r;
}

function format_time($seconds)
{
	if($seconds < 0)
	{
		return "Unknown";
	}
	$H = floor($seconds / 3600);
	$i = ($seconds / 60) % 60;
	$s = $seconds % 60;
	return sprintf("%02d:%02d:%02d", $H, $i, $s);
}

/**
 * Inputs:
 *  Time zone
 *  Query time
 *  Latitude & Longitude
 *  Output format
 *  Twilight
 *  Verbose/debug calculations
 */

date_default_timezone_set("Etc/UTC");
date_default_timezone_set("America/Vancouver");

/*
$lat_deg = -31.557; // Deg north (-ve is south)
$long_deg = 159.086; // Deg west (-ve is east)
$time = mktime(12, 0, 0, 7, 7, 2014);
*/
$lat_deg = -37.6241;
$long_deg = -144.6793;
/*$time = time();*/
$time = mktime(12, 0, 0, 9, 22, 2014);

/*
$lat_deg = 52;
$long_deg = -5;
$time = mktime(12, 0, 0, 4, 1, 2004);*/
//$time = time();

$verbose = false;

$time = time();
//$time = mktime(1, 0, 0, 9, 23, 2014);
$times = get_sun_times($lat_deg, $long_deg, $time);
//print_r($times);
//echo "$time\n";

$state = "unknown";
$time_from = -1;
$time_till = -1;
$twilight = false;

if($time < $times['sunrise'])
{
	$state = "Night";
	$time_till = $times['sunrise'] - $time;
	// time_from requires looking up yesterdays sunset time
	$yesterday = get_sun_times($lat_deg, $long_deg, $time - $UNIX_daylen);
	$time_from = $time - $yesterday['sunset'];
}
else if($time < $times['sunset'])
{
	$state = "Day";
	$time_from = $time - $times['sunrise'];
	$time_till = $times['sunset'] - $time;

}
else
{
	$state = "Night";
	$time_from = $time - $times['sunset'];
	// we need to check tomorrow's sunrise
	$tomorrow = get_sun_times($lat_deg, $long_deg, $time + $UNIX_daylen);
	$time_till = $tomorrow['sunrise'] - $time;

	// twilight calculations
	if($time < $times['twilightset'])
	{
		$twilight = "Civil";
	}
	else if($time < $times['nauticalset'])
	{
		$twilight = "Nautical";
	}
	else if($time < $times['astronomicalset'])
	{
		$twilight = "Astronomical";
	}
}

if($state == "Day")
{
	$prev_event = "Sunrise";
	$next_event = "Sunset";
}
else
{
	$prev_event = "Sunset";
	$next_event = "Sunrise";
}

$output_format = "human";
//$output_format = "computer";
if($output_format == "computer")
{
	echo "state=".strtolower($state)."\n";
	echo strtolower($prev_event)."=$time_from\n";
	echo strtolower($next_event)."=$time_till\n";
}
else
{
	echo "State:      \t$state\n";
	if($twilight)
	{
		echo "Twilight:\t$twilight\n";
	}
	echo "$prev_event was:\t".format_time($time_from)." ago\n";
	echo "$next_event is in: \t".format_time($time_till)."\n";
}

print_r($times);

/**
 * Use cases:
 * Turn off lights during the day
 * * day/night indicator
 *
 * turn lights on at dusk
 * * time till sunset (is there an angle for dusk?)
 *
 * turn on lights before dawn
 * * time till sunrise
 *
 *
 * Input parameters
 * * latitude
 * * longitude
 * * time zone
 * * query time
 *
 * Select output strings
 * * day/night flag
 * * time till sunrise/sunset
 * * time from last sunrise/sunset
 * * unit for times (human or seconds)
 */
?>
