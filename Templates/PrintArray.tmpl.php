<!DOCTYPE html>
<html>
	<head>
		<title>Print data.</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<style>
			@media print, screen{
				table{border-collapse:collapse;}
				th{padding:0px 4px;}
				td{border-top:1px solid black;padding:2px 5px;}
				#header{margin:20px 0px; font-size:24px;}
				.igtranslator-main-div{display:none;}
			}
		</style>
	</head>
	<body>
		<div id="header">
			<?php
			if(array_key_exists('print_titel', $arrayToPrint)){
				echo $arrayToPrint['print_titel'];
				unset($arrayToPrint['print_titel']);
			}
			?>
		</div>
		<table>
		<?php
		echo "<tr>";
	//print header with arrays keys
		echo "<th>#</th>";
		foreach ($arrayToPrint[0] as $k=>$a){
			echo "<th>$k</th>";
		}
		echo "</tr>";
		foreach ($arrayToPrint as $k=>$a){
			echo "<tr>";
			echo "<td>".($k+1)."</td>";
			foreach($a as $b){
				echo "<td>".$b."</td>";
			}
			echo "</tr>";
		}
		?>
		</table>
	</body>
</html>