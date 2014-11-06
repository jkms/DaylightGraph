<?PHP

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
		
echo "		context.lineWidth = 2;
		// context.strokeStyle = 'blue';
		context.stroke();
		</script>
	</body>
</html>";      

?>