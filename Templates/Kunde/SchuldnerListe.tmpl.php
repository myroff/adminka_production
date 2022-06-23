<?php
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
require_once BASIS_DIR.'/Tools/TmplTools.php';
use Tools\TmplTools as TmplTls;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>SWIFF. Schuldner-Liste.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />

		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>

		<script src="<?=BASIS_URL?>/Public/js/zebra_datepicker.js"></script>
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/zebra_default.css">

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
				<form method="POST">
					<table>
						<tr>
							<th>
								Start Monat
							</th>
							<th>
								End Monat
							</th>
							<th>
								Mit Lastschrift
							</th>
						<!--
							<th>
								Lehrer
							</th>
							<th>
								Tag
							</th>
							<th>
								Zeit
							</th>
						-->
							<td rowspan="2">
								<input class="search" type='submit' value=''>
							</td>
						</tr>
						<tr>
							<td>
								<input name="startMnt" type="text" value="<?=$sArr['startMnt']?>" class="zebra_datepicker_my" />
							</td>
							<td>
								<input name="endMnt" type="text" value="<?=$sArr['endMnt']?>" class="zebra_datepicker_my" />
							</td>
							<td>
								<input name="withLst" type="checkbox" <?=$sArr['withLst'] ? 'checked' : '' ?> />
							</td>
						</tr>
					</table>
				</form>
			</div>

			<div id="resultsDiv">
				<table id="kundenResTbl">
					<tr>
						<th>
							Info
						</th>
						<th>
							Bezahlen
						</th>
						<th>
							Kurse
						</th>
						<th>
							Knd.-Nr.
						</th>
						<th>
							Anrede
						</th>
						<th>
							Vorname
						</th>
						<th>
							Name
						</th>
						<th>
							Adresse
						</th>
						<th>
							Geburtsdatum
						</th>
						<th>
							Telefon
						</th>
						<th>
							Handy
						</th>
						<th>
							Email
						</th>
					</tr>
				<?php
				if(empty($res))
				{
				?>
					<tr>
						<td colspan="12">Nach Ihrer Anfrage wurden keine Daten gefunden.</td>
					</tr>
				<?php
				}
				else
				{
					foreach($res as $k=>$v)
					{
						echo "<tr>";

						echo "<td colspan='12' style='font-weight: bold;color:white;background:#575757;' >".Fltr::sqlDateToMonatYear($k).": ".count($v)." Teilnemern.<td></tr>";
						foreach ($v as $r)
						{
							if($r['payment_id'] === "1")
							{
								echo "<tr>";
							}
							else
							{
								echo "<tr style='background:yellow;'>";
							}

							echo "<td><a href='".BASIS_URL."/admin/kundeById/".$r['kndId']."'>Info</a></td>";
							echo "<td><a href='".BASIS_URL."/admin/bezahlenById/".$r['kndId']."'>Bezahlen</a></td>";
							echo "<td>".$r['kurse']."</td>";
							echo "<td>".$r['kundenNummer']."</td>";
							echo "<td>".$r['anrede']."</td>";
							echo "<td>".$r['vorname']."</td>";
							echo "<td>".$r['name']."</td>";
							echo "<td>".$r['strasse']." ".$r['strNr']."<br>";
							echo $r['plz']." ".$r['stadt']."</td>";
							echo "<td>".Fltr::sqlDateToStr($r['geburtsdatum'])."</td>";
							echo "<td>".$r['telefon']."</td>";
							echo "<td>".$r['handy']."</td>";
							echo "<td>".$r['email']."</td>";

							echo "</tr>";
						}
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

		//Monatspicker
		$(".zebra_datepicker_my").Zebra_DatePicker({
			offset: [10,200],
			format: 'Y-m',   //  note that becase there's no day in the format
							//  users will not be able to select a day!
		});
		</script>
	</body>
</html>

