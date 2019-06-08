<?php
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>SWIFF: Kurseliste.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />
		
		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>
		<style>
			.tooltip{position:relative;}
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
			<div id="meldung">
				<?php
				if(isset($meldung))
				{
					echo $meldung;
				}
				?>
			</div>
			<div id="searchPanel">
				<form method="POST">
					<table>
						<tr>
							<th>
								Kursname
							</th>
							<th>
								Alter
							</th>
							<th>
								Klasse
							</th>
							<td rowspan="2">
								<input type='submit' value='Suchen' style="padding: 10px 10px;">
							</td>
						</tr>
						<tr>
							<td>
								<input name="kurName" type="text" value="<?=$sArr[':kurName']?>" />
							</td>
							<td>
								<input name="kurAlter" type="text" value="<?=$sArr[':kurAlter']?>" />
							</td>
							<td>
								<input name="kurKlasse" type="text" value="<?=$sArr[':kurKlasse']?>" />
							</td>
						</tr>
					</table>
				</form>
			</div>
			
			<div id="resultsDiv">
				<table id="kundenResTbl">
					<tr>
						<th>
							Kurs
						</th>
						<th>
							Lehrer
						</th>
						<th>
							Raum
						</th>
						<th>
							Tag
						</th>
						<th>
							Zeit
						</th>
						<th>
							Preis
						</th>
						<th>
							Beschreibung
						</th>
						<th>
							Alter
						</th>
						<th>
							Klassen
						</th>
						<th>
							Teilnehmer
						</th>
					</tr>
				<?php
				if(empty($res))
				{
				?>
					<tr>
						<td colspan="11">Nach Ihrer Anfrage wurden keine Daten gefunden.</td>
					</tr>
				<?php
				}
				else
				{
					foreach ($res as $r)
					{
						$alter = $r['kurMinAlter'];
						$alter .= $r['kurMinAlter'] < $r['kurMaxAlter'] ? " bis ".$r['kurMaxAlter'] : '';
						
						$klasse = $r['kurMinKlasse'];
						$klasse .= $r['kurMinKlasse'] < $r['kurMaxKlasse'] ? " bis ".$r['kurMaxKlasse'] : "";
						
						echo "<tr>";
						
						echo "<td>".$r['kurName']."</td>";
						echo "<td>".$r['vorname']." ".$r['name']."</td>";
						echo "<td>".$r['raum']."</td>";
						echo "<td>".  Fltr::indxToWeekday($r['wochentag'])."</td>";
						echo "<td>".$r['anfang']." - ".$r['ende']."</td>";
						echo "<td>".$r['kurPreis']."</td>";
						echo "<td>".$r['kurBeschreibung']."</td>";
						echo "<td>".$alter."</td>";
						echo "<td>".$klasse."</td>";
						echo "<td class='tooltip' tooltip='".$r['tlnm_liste']."'>".$r['anzTeilnehmer']."/".$r['maxKnd']."</td>";
						
						echo "</tr>";
					}
				}
				?>
				</table>
			</div>
		</div>
		<!-- END OF CONTENT -->
		
		<!--JavaScript -->
<script>
var meldung = $('#meldung');
//$("form [name=vorname]").css({'background':'red'});
//$('form').attr("name", "plz").css({'background':'red'});
$(document).ready(function(){
	$('.tooltip').hover(
		function(){
			var txt = $(this).attr('tooltip');
			var top = $(this).outerHeight();
			$(this).prepend("<div style='position:absolute;top:"+top+"px;background:white;border:1px solid black;z-index:10;' id='tlp_tip'>"+txt+"</div>")
		},
		function(){
			$('#tlp_tip').remove();
		}
	);
});
</script>
	</body>
</html>
