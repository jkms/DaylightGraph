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
		$sunset = date_sunset($dayofyear, SUNFUNCS_RET_STRING, $Latitude, $Longitude, $Zenith, $TZOffset);
		$SSdata =  array (
			'day' => $i,
			'seconds' => converttoseconds($sunset)
		);
		$SRdata =  array (
			'day' => $i,
			'seconds' => converttoseconds($sunrise)
		);
		$sunrisearray[]=$SRdata;
		$sunsetarray[]=$SSdata;
	}
	
	$startwork = converttoseconds("09:00");
	$gohome = converttoseconds("17:30");

function DrawGraph($line1, $color1, $line2, $color2, $line3, $color3, $line4, $color4, $graph) {
	echo "
    <canvas id=\"myCanvas\" width=\"".$graph['x']."\" height=\"".$graph['y']."\" style=\"border:1px solid #000000;\"></canvas>
		<script>
		var canvas = document.getElementById('myCanvas');
		var context = canvas.getContext('2d');";
		
	//Grid
		for ($i=0; $i<12; $i++) {
			$coord[1000]['x'][0][] = ($i/12) * $graph['x'];
			$coord[1000]['y'][0][] = 0;
			$coord[1000]['x'][1][] = ($i/12) * $graph['x'];
			$coord[1000]['y'][1][] = $graph['x'];
		}

		echo "\n		context.beginPath();
		context.moveTo(".$coord[1000]['x'][0][0].", ".$coord[1000]['y'][0][0].");";
		
		for ($i=0; $i<12; $i++) {
			echo "\n		context.lineTo(".$coord[1000]['x'][1][0].", ".$coord[1000]['y'][1][0].");
			context.lineWidth = 1;
			context.strokeStyle = 'gray';
			context.stroke();";
		}
	/*
	for ($i=1; $i<12; $i++) {
		echo "\n		context.beginPath();
		context.moveTo(".($i / 12) * graph['x'].", ".graph['y'] * 0 .");
		context.lineTo(".($i / 12) * graph['x'].", ".graph['y'] * 1 .");
		context.lineWidth = 1;
		context.strokeStyle = 'gray';
		context.stroke();";
	}
	
	for ($i=1; $i<24; $i++) {
		echo "\n		context.beginPath();
		context.moveTo(".graph['x'] * 0 .", ".($i / 24) * graph['y'].");
		context.lineTo(".graph['x'] * 1 .", ".($i / 24) * graph['y'].");
		context.lineWidth = 1;
		context.strokeStyle = 'gray';
		context.stroke();";
	}
	*/
	$secondsinday = 24*60*60;
	//Line 1
	$width=count($line1);
	for ($i=0; $i<$width; $i++) {
		$coord[0]['x'][] = (($line1[$i]['day'] * ($graph['x'] / $width)) + ($graph['x'] / 2));
		$coord[0]['y'][] = $graph['y'] - (($line1[$i]['seconds'] / $secondsinday) * $graph['y']);
	}

	echo "
		context.beginPath();
		context.moveTo(".$coord[0]['x'][0].", ".$coord[0]['y'][0].");";
	
	for ($i=1; $i<count($line1); $i++) {
		echo "\n		context.lineTo(".$coord[0]['x'][$i].", ".$coord[0]['y'][$i].");";
	}
	echo "\n		context.lineJoin = 'round';
		context.lineWidth = 2;
		context.strokeStyle = '$color1';
		context.stroke();";
	//Line 32
	$width=count($line2);
	for ($i=0; $i<$width; $i++) {
		$coord[1]['x'][] = (($line2[$i]['day'] * ($graph['x'] / $width)) + ($graph['x'] / 2));
		$coord[1]['y'][] = $graph['y'] - (($line2[$i]['seconds'] / $secondsinday) * $graph['y']);
	}

	echo "
		context.beginPath();
		context.moveTo(".$coord[1]['x'][0].", ".$coord[1]['y'][0].");";
	
	for ($i=1; $i<count($line2); $i++) {
		echo "\n		context.lineTo(".$coord[1]['x'][$i].", ".$coord[1]['y'][$i].");";
	}
	echo "\n		context.lineJoin = 'round';
		context.lineWidth = 2;
		context.strokeStyle = '$color2';
		context.stroke();";
	
	//Line 3
	$coord[2]['x'][0] = 0;
	$coord[2]['y'][0] = ($line3 / $secondsinday) * $graph['y'];
	$coord[2]['x'][1] = $graph['x'];
	$coord[2]['y'][1] = ($line3 / $secondsinday) * $graph['y'];
	echo "\n		context.beginPath();
		context.moveTo(".$coord[2]['x'][0].", ".$coord[2]['y'][0].");
		context.lineTo(".$coord[2]['x'][1].", ".$coord[2]['y'][1].");
		context.lineWidth = 1;
		context.strokeStyle = '$color3';
		context.stroke();";
	
	//Line 4
	$coord[3]['x'][0] = 0;
	$coord[3]['y'][0] = ($line4 / $secondsinday) * $graph['y'];
	$coord[3]['x'][1] = $graph['x'];
	$coord[3]['y'][1] = ($line4 / $secondsinday) * $graph['y'];
	echo "\n		context.beginPath();
		context.moveTo(".$coord[3]['x'][0].", ".$coord[3]['y'][0].");
		context.lineTo(".$coord[3]['x'][1].", ".$coord[3]['y'][1].");
		context.lineWidth = 1;
		context.strokeStyle = '$color4';
		context.stroke();";
	  
		
echo "		</script>\n";
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
	DrawGraph($sunrisearray, "blue", $sunsetarray, "red", $startwork, "black", $gohome, "black", $graph);

echo "\n	</body>
</html>";
?>