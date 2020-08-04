<html>
	<head>
		<title>SWIFF: Statistik.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />
		
		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>
		<script src="<?=BASIS_URL?>/Public/js/Chart.js/Chart.min.js"></script>
		
		<style>
			.standardTable{float:left;margin-left: 10px;}
			#tables{float:left;}
			#ChartJS{float:left;margin-left:50px;}
		</style>
	</head>
	<body>
		<div id="horizontalMenu">
			<?php
			require_once BASIS_DIR.'/Templates/Menu.class.php';
			TemplateTools\Menu::adminMenu();
			?>
		</div>
		<!-- START OF CONTENT -->
		<div id="mainContent">
			
			<table>
				<tr>
					<td>Saison</td>
				</tr>
				<tr>
					<td><?php echo TmplTls::getSeasonsSelector("s_season", "s_season", $sArr[':season'], "Season", 0); ?></td>
				</tr>
			</table>
			
			<h3>Teilnehmer-Statistik</h3>
			<div id="tables">
				<table >
					<tr>
						<th>
							Angemeldete Kunde<br>
							einschl. Probe-Kunde
						</th>
						<td><?=$kres['countKunde']?></td>
					</tr>
					<tr>
						<th>Kunden-Kurse</th>
						<td><?=$kres['countKurs']?></td>
					</tr>
					<tr>
						<th>Herren</th>
						<td><?=$kres['countHerren']?></td>
					</tr>
					<tr>
						<th>Frauen</th>
						<td><?=$kres['countFrauen']?></td>
					</tr>
					<tr>
						<th>Gesammt eigentragen</th>
						<td><?=$kres['countAlle']?></td>
					</tr>
				</table>

				<table class="standardTable">
					<tr><td colspan="2"><h3>Bevölkerung-Statistik</h3></td></tr>
					<tr>
						<th>Stadt</th>
						<th>Kunden</th>
					</tr>
					<?php
					foreach($pres as $r)
					{
					?>
					<tr>
						<td><?=$r['stadt']?></td>
						<td><?=$r['population']?></td>
					</tr>
					<?php
					}
					?>
				</table>

				<table class="standardTable">
					<tr><td colspan="2"><h3>Alter-Statistik</h3></td></tr>
					<tr>
						<th>Alter</th>
						<th>Anzahl</th>
					</tr>
					<?php
					foreach($ares as $r)
					{
					?>
					<tr>
						<td><?=$r['age']?></td>
						<td><?=$r['number']?></td>
					</tr>
					<?php
					}
					?>
				</table>
			</div>
			
			<div id="ChartJS">
				<div class="buttons">
					<button id="button_gender">Gender-Statistik</button>
					<button id="button_population">Bevölkerung-Statistik</button>
					<button id="button_ageStat">Alter-Statistik</button>
					<button id="button_klassesStat">Klassen-Statistik</button>
				</div>
				<canvas id="myChart" width="800" height="600"></canvas>
			</div>
		</div><!-- Main Content Ende -->
		
		<script>
			// Get context with jQuery - using jQuery's .get() method.
			//var ctx = $("#myChart").get(0).getContext("2d");
			var ctx = document.getElementById("myChart").getContext("2d");
			
			var actualChart;
			
			var options = {
				///Boolean - Whether grid lines are shown across the chart
				scaleShowGridLines : true,

				//String - Colour of the grid lines
				scaleGridLineColor : "rgba(0,0,0,.05)",

				//Number - Width of the grid lines
				scaleGridLineWidth : 1,

				//Boolean - Whether to show horizontal lines (except X axis)
				scaleShowHorizontalLines: true,

				//Boolean - Whether to show vertical lines (except Y axis)
				scaleShowVerticalLines: true,

				//Boolean - Whether the line is curved between points
				bezierCurve : true,

				//Number - Tension of the bezier curve between points
				bezierCurveTension : 0.4,

				//Boolean - Whether to show a dot for each point
				pointDot : true,

				//Number - Radius of each point dot in pixels
				pointDotRadius : 4,

				//Number - Pixel width of point dot stroke
				pointDotStrokeWidth : 5,

				//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
				pointHitDetectionRadius : 20,

				//Boolean - Whether to show a stroke for datasets
				datasetStroke : true,

				//Number - Pixel width of dataset stroke
				datasetStrokeWidth : 5,

				//Boolean - Whether to fill the dataset with a colour
				datasetFill : false,
				// Boolean - If we want to override with a hard coded scale
				scaleOverride: false,

				// ** Required if scaleOverride is true **
				// Number - The number of steps in a hard coded scale
				//scaleSteps: null,
				// Number - The value jump in the hard coded scale
				//scaleStepWidth: null,
				// Number - The scale starting value
				scaleStartValue: null,//null
				// String - Colour of the scale line
				scaleLineColor: "rgba(0,0,0,.5)",
				// Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
				scaleBeginAtZero: false,
			};
			
		//Doughnut,Line,Bar
		//gender statistik
			$("#button_gender").click(function(){
				$.ajax({
					url:'<?=BASIS_URL?>/admin/getChartsData',
					type:'POST',
					data:{charReq: 'gender'},
					dataType:'TEXT',
					success:function(response){
						// eval("("+'{a:"www"}'+")")
						var data = eval("("+response+")");//[{'label':'Man','value':'141'},{'label':'Frau','value':'166'}];JSON.parse(response)
						if(actualChart)
							actualChart.destroy();
						actualChart = new Chart(ctx).Pie(data,options);
					},
					error:function(errorThrown){
						alert("Error: "+errorThrown);
					}
				});
			});
			
		//Bebölkerung statistik
			$("#button_population").click(function(){
				$.ajax({
					url:'<?=BASIS_URL?>/admin/getChartsData',
					type:'POST',
					data:{charReq: 'population'},
					dataType:'TEXT',
					success:function(response){
						var data = eval("("+response+")");
						if(actualChart)
							actualChart.destroy();
						actualChart = new Chart(ctx).Bar(data,options);
					},
					error:function(errorThrown){
						alert("Error: "+errorThrown);
					}
				});
			});
			
		//Bebölkerung statistik
			$("#button_ageStat").click(function(){
				$.ajax({
					url:'<?=BASIS_URL?>/admin/getChartsData',
					type:'POST',
					data:{charReq: 'ageStat'},
					dataType:'TEXT',
					success:function(response){
						var data = eval("("+response+")");
						if(actualChart)
							actualChart.destroy();
						actualChart = new Chart(ctx).Bar(data,options);
					},
					error:function(errorThrown){
						alert("Error: "+errorThrown);
					}
				});
			});
		
		//Klassen-statistik
			$("#button_klassesStat").click(function(){
				$.ajax({
					url:'<?=BASIS_URL?>/admin/getChartsData',
					type:'POST',
					data:{charReq: 'klassesStat'},
					dataType:'TEXT',
					success:function(response){
						var data = eval("("+response+")");
						if(actualChart)
							actualChart.destroy();
						actualChart = new Chart(ctx).Bar(data,options);
					},
					error:function(errorThrown){
						alert("Error: "+errorThrown);
					}
				});
			});
			
		</script>
	</body>
</html>