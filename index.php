<?PHP

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$Latitude=49;
$Longitude=-123.1;
$Zenith=90;
$TZOffset=-8;

$secondsinday = 24*60*60;

function converttoseconds($timevar) {
	$timearray = explode(':',$timevar);
	return $timearray[0]*60*60 + $timearray[1]*60;
}

	for ($i=-183; $i<=182; $i++) {
		$dayofyear = time() + ($i * $secondsinday);
		//echo date("D M d Y"). ', sunrise time for '. date("M d, Y", $dayofyear) .': ' .date_sunrise($dayofyear, SUNFUNCS_RET_STRING, $Latitude, $Longitude, $Zenith, $TZOffset);
		$sunrise = date_sunrise($dayofyear, SUNFUNCS_RET_STRING, $Latitude, $Longitude, $Zenith, $TZOffset);
		$DayinQuestion = date('D, d M Y', $dayofyear);
		$seconds = converttoseconds($sunrise);
		$sedondspercent = $seconds / $secondsinday;
		//echo "$DayinQuestion -- TimeofDay: $sunrise -- Seconds: $seconds -- SecondsPercent: $sedondspercent<br>\n";
		$newdata =  array (
			'day' => $i,
			'seconds' => $seconds
		);
		$sunrisearray[]=$newdata;
	}

	//print_r($sunrisearray);

function DrawGraph($points, $color, $graph) {
	$secondsinday = 24*60*60;
	$width=count($points);
	for ($i=0; $i<$width; $i++) {
		$coord['x'][] = (($graph['x'] / 2) + $points[$i]['day']) * ($graph['x'] / $width);
		$coord['y'][] = $graph['y'] - (($points[$i]['seconds'] / $secondsinday) * $graph['y']);
	}
	echo "
    <canvas id=\"myCanvas\" width=\"".$graph['x']."\" height=\"".$graph['y']."\" style=\"border:1px solid #000000;\"></canvas>
		<script>
		var canvas = document.getElementById('myCanvas');
		var context = canvas.getContext('2d');
		context.beginPath()
		context.moveTo(".$coord['x'][0].", ".$coord['y'][0].");";
	
	for ($i=1; $i<count($points); $i++) {
		echo "\n		context.lineTo(".$coord['x'][$i].", ".$coord['y'][$i].");";
	}
	echo "\n		context.lineJoin = 'round';
		context.lineWidth = 2;
		context.strokeStyle = '$color';
		context.stroke();
		</script>\n";
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
	
	$graph =  array ('x' => 1000, 'y' => 1000);
	DrawGraph($sunrisearray, "blue", $graph);

echo "\n	</body>
</html>";
?>