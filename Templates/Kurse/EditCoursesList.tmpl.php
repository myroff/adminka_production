<?php
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>SWIFF: Kurse bearbeiten.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />

		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>

	</head>
	<body>
		<div id="horizontalMenu">
			<?php

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
				<form method="GET">
					<table>
						<tr>
							<th>
								Unterrichtsname
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
							Bearbeiten
						</th>
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
							max. Knd.
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

						echo "<td><a href='".BASIS_URL."/admin/kurseBearbeitenById/".$r['kurId']."' />Bearbeiten</a></td>";
						echo "<td>".$r['kurName']."</td>";
						echo "<td>".$r['vorname']." ".$r['name']."</td>";
						echo "<td>".$r['raum']."</td>";
						echo "<td>".  Fltr::indxToWeekday($r['wochentag'])."</td>";
						echo "<td>".$r['anfang']." - ".$r['ende']."</td>";
						echo "<td>".$r['kurPreis']."</td>";
						echo "<td>".$r['kurBeschreibung']."</td>";
						echo "<td>".$alter."</td>";
						echo "<td>".$klasse."</td>";
						echo "<td>".$r['maxKnd']."</td>";

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


		</script>
	</body>
</html>
