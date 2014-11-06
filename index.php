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


function DrawLine($points) {
	echo " 		context.beginPath()
		context.moveTo(".$points[1]["x"].", ".$points[1]["y"].");";
	echo "context.lineTo(".$points[2]["x"].", ".$points[2]["y"].");";
	echo "context.lineTo(".$points[3]["x"].", ".$points[3]["y"].");";
	echo "		context.lineJoin = 'round';
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

echo "		// round line join (middle)
		context.beginPath();
		context.moveTo(100, 100);
		context.lineTo(100, 200);
		context.lineTo(200, 100);
		context.lineJoin = 'round';
		context.stroke();";

# DrawLine($sunrise);
		
echo "		context.lineWidth = 2;
		// context.strokeStyle = 'blue';
		context.stroke();
		</script>
	</body>
</html>";      

?>