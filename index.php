<?PHP

$sunrise = array( array( day => 1, 
                      x => 50,
                      y => 50 
                    ),
               array( day => 2, 
                      x => 75,
                      y => 100,
                    ),
               array( day => 3, 
                      x => 100,
                      y => 150 
                    )
             );
			 
$sunset = array( array( day => 1, 
                      x => 84,
                      y => 15 
                    ),
               array( day => 2, 
                      x => 127,
                      y => 39,
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
		context.stroke();
		context.lineWidth = 2;
		context.strokeStyle = '$color';
		context.stroke();";
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
    <canvas id=\"myCanvas\" width=\"578\" height=\"200\"></canvas>
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

DrawLine($sunrise, "red");
DrawLine($sunset, "blue");
	
echo "\n		</script>
	</body>
</html>";      

?>