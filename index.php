<?PHP
$loadtime = microtime();
$loadtime = explode(' ', $loadtime);
$loadtime = $loadtime[1] + $loadtime[0];
$starttime = $loadtime;

ini_set('display_errors', 'On');
error_reporting(E_ALL);

date_default_timezone_set('America/Vancouver');

$Latitude=49;
$Longitude=-123.1;
$Zenith=90;


$secondsinday = 24*60*60;

function converttoseconds($timevar) {
	$timearray = explode(':',$timevar);
	return $timearray[0]*60*60 + $timearray[1]*60;
}

$runpath = "Data/johnrun/";

function loadRunKeeper ($file) {
	$xml=simplexml_load_file($file) or die("Error: Cannot create object");
	return $xml;
}

//foreach (glob($runpath."*.gpx") as $filename) {
$filename = "Data/johnrun/2014-01-01-0951.gpx";

    echo "<h1>$filename</h1>\n";
	    $gpx = loadRunKeeper($filename);
	$j = count($gpx->trk->trkseg->trkpt);
	for ($i=0; $i<$j; $i++) {
		print_r($gpx->trk->trkseg->trkpt[$i]->time);
	}
//}

$start = date('z') * -1;
$finish = 366 - date('z');

	for ($i=$start; $i<=$finish; $i++) {
		$dayofyear  = mktime(0, 0, 0, date("m")  , date("d")+$i, date("Y"));
		//echo date("D M d Y"). ', sunrise time for '. date("M d, Y", $dayofyear) .': ' .date_sunrise($dayofyear, SUNFUNCS_RET_STRING, $Latitude, $Longitude, $Zenith, $TZOffset);
		$TZOffset=-8+date('I', $dayofyear);
		$sunrise = date_sunrise($dayofyear, SUNFUNCS_RET_STRING, $Latitude, $Longitude, $Zenith, $TZOffset);
		$sunset = date_sunset($dayofyear, SUNFUNCS_RET_STRING, $Latitude, $Longitude, $Zenith, $TZOffset);
		
		$SSdata =  array (
			'day' => $i,
			'seconds' => converttoseconds($sunset)
		);
		$SRdata =  array (
			'day' => $i,
			'seconds' => converttoseconds($sunrise)
		);
//		echo "Day: ".$i." Sunrise: ".$sunrise."|".end($SRdata['seconds'])." Sunset: ".$sunset."|".end($SSdata['seconds'])."<br>\n";		
		$sunrisearray[]=$SRdata;
		$sunsetarray[]=$SSdata;
	}
	
	if (isset($_POST['work_time'])){
		$startwork = converttoseconds($_POST['work_time']);
		$startworkformvalue = $_POST['work_time'];
	} else {
		$startwork = converttoseconds('09:00');
		$startworkformvalue = '09:00';
	}
	if (isset( $_POST['home_time'] )) {
		$gohome = converttoseconds($_POST['home_time']);
		$gohomeformvalue = $_POST['home_time'];
	} else {
		$gohome = converttoseconds('17:30');
		$gohomeformvalue = '17:30';
	}

function DrawGraph($line1, $color1, $line2, $color2, $line3, $color3, $line4, $color4, $graph) {
	$secondsinday = 24*60*60;
	echo "
    <canvas id=\"myCanvas\" width=\"".$graph['x']."\" height=\"".$graph['y']."\" style=\"border:1px solid #000000;\"></canvas>
		<script>
		var canvas = document.getElementById('myCanvas');
		var context = canvas.getContext('2d');";
		
	//Grid
		//Vertical
		for ($i=1; $i<=12; $i++) {
			$coord[1000]['x'][0][] = ($i/12) * $graph['x'];
			$coord[1000]['y'][0][] = $graph['x'] * 0;
			$coord[1000]['z'][0][] = (($i/12) * $graph['x']) - ($graph['x']/24);
			$coord[1000]['x'][1][] = ($i/12) * $graph['x'];
			$coord[1000]['y'][1][] = $graph['x'] * 1;
		}
		for ($i=0; $i<11; $i++) {
			$datetext = date('M', mktime (0,0,0,$i+1));
			echo "\n		context.beginPath();
			context.moveTo(".$coord[1000]['x'][0][$i].", ".$coord[1000]['y'][0][$i].");
			context.lineTo(".$coord[1000]['x'][1][$i].", ".$coord[1000]['y'][1][$i].");
			context.lineWidth = 1;
			context.strokeStyle = 'gray';
			context.stroke();
			context.font = '8pt Calibri';
			context.textAlign = 'center';
			context.textBaseline = 'top';
			context.fillText('$datetext', ".$coord[1000]['z'][0][$i].", 0);";
		}
		$datetext = date('M', mktime (0,0,0,12));
		echo "\n			context.font = '8pt Calibri';
			context.textAlign = 'center';
			context.textBaseline = 'top';
			context.fillText('$datetext', ".$coord[1000]['z'][0][11].", 0);";
		
		//Horizontal
		for ($i=1; $i<24; $i++) {
			$coord[1001]['x'][0][] = $graph['x'] * 0;
			$coord[1001]['y'][0][] = ($i/24) * $graph['y'];
			$coord[1001]['x'][1][] = $graph['x'] * 1;
			$coord[1001]['y'][1][] = ($i/24) * $graph['y'];
		}
		for ($i=0; $i<23; $i++) {
			$timetext = date('H:i', mktime ($i+1,0));
			echo "\n		context.beginPath();
			context.moveTo(".$coord[1001]['x'][0][$i].", ".$coord[1001]['y'][0][$i].");
			context.lineTo(".$coord[1001]['x'][1][$i].", ".$coord[1001]['y'][1][$i].");
			context.lineWidth = 1;
			context.strokeStyle = 'gray';
			context.stroke();
			context.font = '8pt Calibri';
			context.textAlign = 'left';
			context.textBaseline = 'middle';
			context.fillText('$timetext', ".$coord[1001]['x'][0][$i].", ".$coord[1001]['y'][0][$i].");";
		}

	//Line 1
	$width=count($line1)-1;
	for ($i=0; $i<$width; $i++) {
		$coord[0]['x'][] = ($i * ($graph['x'] / $width));
		$coord[0]['y'][] = ($line1[$i]['seconds'] / $secondsinday) * $graph['y'];
	}
	$coord[0]['x'][] = $graph['x'];
	$coord[0]['y'][] = ($line1[$width]['seconds'] / $secondsinday) * $graph['y'];
        $coord[0]['x'][] = $graph['x'];
        $coord[0]['y'][] = 0;
        $coord[0]['x'][] = 0;
        $coord[0]['y'][] = 0;
        $coord[0]['x'][] = $coord[0]['x'][0];
        $coord[0]['y'][] = $coord[0]['y'][0];


	echo "\n		context.beginPath();
		context.moveTo(".$coord[0]['x'][0].", ".$coord[0]['y'][0].");";
	$width += 3;
	for ($i=1; $i<=$width; $i++) {
		echo "\n		context.lineTo(".$coord[0]['x'][$i].", ".$coord[0]['y'][$i].");";
	}
	echo "\n		context.closePath();
		context.globalAlpha = 0.5;
		context.lineJoin = 'round';
		context.lineWidth = 2;
		context.fillStyle = '$color1';
		context.fill();
		context.strokeStyle = '$color1';
		context.stroke();";
		
	//Line 2

	$width=count($line2)-1;
	for ($i=0; $i<$width; $i++) {
		$coord[1]['x'][] = ($i * ($graph['x'] / $width));
		$coord[1]['y'][] = ($line2[$i]['seconds'] / $secondsinday) * $graph['y'];
	}
	$coord[1]['x'][] = $graph['x'];
	$coord[1]['y'][] = ($line2[$width]['seconds'] / $secondsinday) * $graph['y'];
	    $coord[1]['x'][] = $graph['x'];
        $coord[1]['y'][] = $graph['y'];
        $coord[1]['x'][] = 0;
        $coord[1]['y'][] = $graph['y'];
        $coord[1]['x'][] = $coord[1]['x'][0];
        $coord[1]['y'][] = $coord[1]['y'][0];

	echo "\n		context.beginPath();
		context.moveTo(".$coord[1]['x'][0].", ".$coord[1]['y'][0].");";
	$width += 3;
	for ($i=1; $i<=$width; $i++) {
		echo "\n		context.lineTo(".$coord[1]['x'][$i].", ".$coord[1]['y'][$i].");";
	}
	echo "\n		context.closePath();
		context.globalAlpha = 0.5;
		context.lineJoin = 'round';
		context.lineWidth = 2;
		context.fillStyle = '$color2';
		context.fill();
		context.strokeStyle = '$color2';
		context.stroke();";

	//Work block
	$coord[2]['x'][0] = 0;
	$coord[2]['y'][0] = ($line3 / $secondsinday) * $graph['y'];
	$coord[2]['x'][1] = $graph['x'];
	$coord[2]['y'][1] = ($line3 / $secondsinday) * $graph['y'];
	$coord[3]['x'][0] = 0;
	$coord[3]['y'][0] = ($line4 / $secondsinday) * $graph['y'];
	$coord[3]['x'][1] = $graph['x'];
	$coord[3]['y'][1] = ($line4 / $secondsinday) * $graph['y'];
	echo "\n		context.beginPath();
		context.moveTo(".$coord[2]['x'][0].", ".$coord[2]['y'][0].");
		context.lineTo(".$coord[2]['x'][1].", ".$coord[2]['y'][1].");
		context.lineTo(".$coord[3]['x'][1].", ".$coord[3]['y'][1].");
		context.lineTo(".$coord[3]['x'][0].", ".$coord[3]['y'][0].");

		context.closePath();
		context.globalAlpha = 0.5;
		context.lineWidth = 2;
		context.fillStyle = '$color3';
		context.fill();
		context.strokeStyle = '$color3';
		context.stroke();";
		
		//Stupid shit for mark
		$coord[1002]['x'][0] = $graph['x'] * (date('z') / 365);
		$coord[1002]['y'][0] = $graph['y'] * 0;
		$coord[1002]['x'][1] = $graph['x'] * (date('z') / 365);
		$coord[1002]['y'][1] = $graph['y'] * 1;
		$nowtext = date('D, d M Y H:i:s');
		echo "\n		context.beginPath();
			context.moveTo(".$coord[1002]['x'][0].", ".$coord[1002]['y'][0].");
			context.lineTo(".$coord[1002]['x'][1].", ".$coord[1002]['y'][1].");
			context.lineWidth = 2;
			context.strokeStyle = 'green';
			context.stroke();";

		$secondstoday = date("H:i");
		$nowbar = converttoseconds($secondstoday);
		$coord[1003]['x'][0] = $graph['x'] * 0;
		$coord[1003]['y'][0] = $graph['y'] * $nowbar / $secondsinday;
		$coord[1003]['x'][1] = $graph['x'] * 1;
		$coord[1003]['y'][1] = $graph['y'] * $nowbar / $secondsinday;
		echo "\n		context.beginPath();
			context.moveTo(".$coord[1003]['x'][0].", ".$coord[1003]['y'][0].");
			context.lineTo(".$coord[1003]['x'][1].", ".$coord[1003]['y'][1].");
			context.lineWidth = 2;
			context.strokeStyle = 'green';
			context.stroke();
			context.font = '8pt Calibri';
			context.textAlign = 'left';
			context.fillStyle = 'black';
			context.textBaseline = 'bottom';
			context.fillText('$nowtext', ".$coord[1002]['x'][0].", ".$coord[1003]['y'][0].");";
	  
		
echo "\n		</script>\n";
} 

echo "<!DOCTYPE HTML>
<html>
	<head>
		<title>Daylight Graph</title>
		<style>
			body {
				margin: 0px;
				padding: 0px;
			}
		</style>
	</head>
	<body>";
	
	$graph =  array ('x' => 1000, 'y' => 700);
	DrawGraph($sunrisearray, "blue", $sunsetarray, "red", $startwork, "black", $gohome, "black", $graph);
	
echo "\n
<form action=\"index.php\" method=\"post\">
	When do you go to work:
	<input type=\"time\" name=\"work_time\" value=\"$startworkformvalue\">
	When do you go home:
	<input type=\"time\" name=\"home_time\" value=\"$gohomeformvalue\">
	<input id=\"submit\" name=\"submit\" type=\"submit\" value=\"Submit\">
</form>
	</body>
</html>";

$loadtime = microtime();
$loadtime = explode(' ', $loadtime);
$loadtime = $loadtime[1] + $loadtime[0];
$finishtime = $loadtime;
$total_time = round(($finishtime - $starttime), 4);
echo 'Page generated in '.$total_time.' seconds.';
?>
