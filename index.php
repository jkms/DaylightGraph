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
                    )
             );



function DrawLine($points) {
	echo "\n		context.beginPath()
		context.moveTo(".$points[0]["x"].", ".$points[0]["y"].");";
	echo "\n		context.lineTo(".$points[1]["x"].", ".$points[1]["y"].");";
	echo "\n		context.lineTo(".$points[2]["x"].", ".$points[2]["y"].");";
	echo "\n		context.lineJoin = 'round';
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

DrawLine($sunrise);
DrawLine($sunset);
		
echo "\n		context.lineWidth = 2;
		// context.strokeStyle = 'blue';
		context.stroke();
		</script>
	</body>
</html>";      

?>