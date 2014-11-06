<?PHP
$Latitude=49;
$Longitude=-123.1;
$Zenith=90;
$TZOffset=-8;

$secondsinday = 24*60*60;
	$graph =  array (
		'x' => "1000",
		'y' => "1000"
	);

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

//	print_r($sunrisearray);
			 
$sunset = array( array( day => 1, 
                      x => 84,
                      y => 15 
                    ),
               array( day => 2, 
                      x => 127,
                      y => 39
                    ),
               array( day => 3, 
                      x => 400,
                      y => 354 
                    ),
               array( day => 4, 
                      x => 189,
                      y => 34
                    ),
               array( day => 5, 
                      x => 245,
                      y => 79 
                    ),
               array( day => 6, 
                      x => 111,
                      y => 222 
                    ),
               array( day => 7, 
                      x => 8,
                      y => 200 
                    )
             );



function DrawLine($points, $color) {
	for ($i=0; $i<count($points); $i++) {
		$xcoord[] = ($graph['x']/2) + $points[$i]['day'];
		$ycoord[] = $graph['y'] - (($points[$i]['seconds'] / $secondsinday) * $graph['y']);
	}
	echo "\n		context.beginPath()
		context.moveTo(".$xcoord[0].", ".$ycoord[0].");";
	
	for ($i=1; $i<count($points); $i++) {
		echo "\n		context.lineTo(".$xcoord[$i].", ".$ycoord[$i].");";
	}
	echo "\n		context.lineJoin = 'round';
		context.lineWidth = 2;
		context.strokeStyle = '$color';
		context.stroke();\n";
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
	<body>
    <canvas id=\"myCanvas\" width=\"".$graph['x']."\" height=\"".$graph['y']."\"></canvas>
		<script>
		var canvas = document.getElementById('myCanvas');
		var context = canvas.getContext('2d');";

/*
echo "\n		// round line join (middle)
		context.beginPath();
		context.moveTo(100, 100);
		context.lineTo(100, 200);
		context.lineTo(200, 100);
		context.lineJoin = 'round';
		context.stroke();";
*/

//DrawLine($sunrise, "red");
DrawLine($sunrisearray, "blue");
	
echo "\n		</script>
	</body>
</html>";      
print_r($xcoord);
print_r($ycoord);
?>