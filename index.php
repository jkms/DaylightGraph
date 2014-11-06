<?PHP
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
		echo date("D M d Y"). ', sunrise time for '. date("M d, Y", $dayofyear) .': ' .date_sunrise($dayofyear, SUNFUNCS_RET_STRING, $Latitude, $Longitude, $Zenith, $TZOffset);
		$sunrise = date_sunrise($dayofyear, SUNFUNCS_RET_STRING, $Latitude, $Longitude, $Zenith, $TZOffset);
		$DayinQuestion = date('D, d M Y', $dayofyear);
		$seconds = converttoseconds($sunrise);
		$sedondspercent = $seconds / $secondsinday;
		echo "$DayinQuestion -- TimeofDay: $sunrise -- Seconds: $seconds -- SecondsPercent: $sedondspercent<br>\n";
		$coords = array($i,$sedondspercent);
		array_push($sunrise,$coords);
	}

	print_r($sunrise);
			 
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
	echo "\n		context.beginPath()
		context.moveTo(".$points[0]["x"].", ".$points[0]["y"].");";
	
	for ($i=1; $i<=count($points)-1; $i++) {
		echo "\n		context.lineTo(".$points[$i]["x"].", ".$points[$i]["y"].");";
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
    <canvas id=\"myCanvas\" width=\"578\" height=\"578\"></canvas>
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
DrawLine($sunset, "blue");
	
echo "\n		</script>
	</body>
</html>";      

?>