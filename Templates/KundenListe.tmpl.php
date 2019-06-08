<?php
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
require_once BASIS_DIR.'/Tools/TmplTools.php';
use Tools\TmplTools as TmplTls;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>SWIFF. Kundenliste.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style-print.css" type="text/css" media="print" />
		
		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>

	</head>
	<body>
		<div id="horizontalMenu" class="dont-print">
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
			<div id="searchPanel" class="dont-print">
				<form id="searchFrom" method="POST">
					<table>
						<tr>
							<th>
								Vorname
							</th>
							<th>
								Name
							</th>
							<th>
								Kurs
							</th>
							<th>
								Lehrer
							</th>
							<th>
								Tag
							</th>
							<th>
								Zeit
							</th>
							<th>
								Integra.
							</th>
							<th>
								Abgemeldet
							</th>
							<td rowspan="2">
								<input class="search" type='submit' value='' style="padding: 10px 10px;">
							</td>
						</tr>
						<tr>
							<td>
								<input name="vorname" type="text" value="<?=$sArr[':vorname']?>" />
							</td>
							<td>
								<input name="name" type="text" value="<?=$sArr[':name']?>" />
							</td>
							<td>
								<?php TmplTls::getKursSelectorById("s_kurId", "s_kurId", $sArr[':kurId']); ?>
							</td>
							<td>
								<?php echo TmplTls::getLehrerSelector("s_lehrId", "s_lehrId", $sArr[':lehrId']); ?>
							</td>
							<td>
								<?php echo TmplTls::getWeekdaySelector("wochentag", "wochentag", $sArr[':wochentag']); ?>
							</td>
							<td>
								<?php echo TmplTls::getTimeSelector("zeit", "zeit", $sArr[':zeit']); ?>
							</td>
							<td><input type="checkbox" name="showIntegra" value="i" <?=$sArr[':showIntegra'] ? "checked='checked'" : "" ?> /></td>
							<td><input type="checkbox" name="abgemeldet" value="i" <?=$sArr[':abgemeldet'] ? "checked='checked'" : "" ?> /></td>
						</tr>
					</table>
				</form>
				<button id="print_table">Print Table</button>
				Überschrift für Ausdruck:<input type="text" id="print_titel" name="print_titel" size="50"/>
			</div>
			<div id="anzDerKunden">
				<?=count($res)?> Kunden
			</div>
			<div id="resultsDiv">
				<table class="kundenResTbl">
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
							Knd.-Nr. <input type="checkbox" class="print_select" name="print_kndnr" />
						</th>
						<th>
							Anrede <input type="checkbox" class="print_select" name="print_anrede" />
						</th>
						<th>
							Vorname <input type="checkbox" class="print_select" name="print_vorname" />
						</th>
						<th>
							Name <input type="checkbox" class="print_select" name="print_name" />
						</th>
						<th>
							Alter <input type="checkbox" class="print_select" name="print_alter" />
						</th>
						<th>
							Geburtsdatum <input type="checkbox" class="print_select" name="print_geburtsdatum" />
						</th>
						<th>
							Adresse
						</th>
						<th>
							Telefon <input type="checkbox" class="print_select" name="print_telefon" />
						</th>
						<th>
							Handy <input type="checkbox" class="print_select" name="print_handy" />
						</th>
						<th>
							Email <input type="checkbox" class="print_select" name="print_email" />
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
						//$isCash = $r['isCash'] ? "<img src='".BASIS_URL."/Public/img/euro_cash.jpg' style='height:100px;'/>" : "<img src='".BASIS_URL."/Public/img/Lastschrift.png' style='height:30px;'/>";
						$bild = "";
						switch ($r['isCash']) {
							case "0":
								$bild = "lastschrift.001.png";
								break;
							case "1":
								$bild = "euro_cash.jpg";
								break;
							case "2":
								$bild = "bamf.002.jpeg";// bamf.002.jpg
								break;
							case "3":
								$bild = "euro_cash.jpg";
								break;
							case "4":
								$bild = "euro_cash.jpg";
								break;
							default:
								$bild .= "Zahlungsart ist nicht gefunden.";
								break;
						}
						$isCash = "<img src='".BASIS_URL."/Public/img/".$bild."' style='height:100px;'/>";
						
					//Kunde hat keine Kurse mehr
						$stl = is_null($r['kndIdInKhk']) ? "background:yellow;" : "";
					//wenn der kundenNummer fängt mit "i" an. (Für Integra)
						$stl = (substr($r['kundenNummer'], 0, 1) === 'i') ? "background:Khaki;" : "";//Fuchsia
						$stl .= $r['istFotoErlaubt'] === "0" ? "border-left:5px solid red;" : "";
						echo "<tr style='$stl'>";
						
						echo "<td><a href='".BASIS_URL."/admin/kundeById/".$r['kndId']."'>Info</a><br>$isCash</td>";
						echo "<td><a href='".BASIS_URL."/admin/bezahlenById/".$r['kndId']."'>Bezahlen</a></td>";
						echo "<td>";
						echo Fltr::printSqlKursTermin($r['kurse']); //Fltr::printSqlKursTermin($r['kurse'], ";;", "|", ";", "->", "<br>");
						//echo $r['kurse'];
						echo "</td>";
						echo "<td>".$r['kundenNummer']."</td>";
						echo "<td>".$r['anrede']."</td>";
						echo "<td>".$r['vorname']."</td>";
						echo "<td>".$r['name']."</td>";
						$alterColor = (int)$r['alter'] < 4 ? "red" : "";
						echo "<td style='color:$alterColor;font-weight:bold;' >".$r['alter']."</td>";
						echo "<td>".  Fltr::sqlDateToStr($r['geburtsdatum'])."</td>";
						echo "<td>".$r['strasse']." ".$r['strNr']."<br>".$r['plz']." ".$r['stadt']."</td>";
						echo "<td>".$r['telefon']."</td>";
						echo "<td>".$r['handy']."</td>";
						echo "<td>".$r['email']."</td>";
						
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

$('#print_table').click(function(){

	var postData = $('#searchFrom').clone();
	postData.css('display', 'none');
	postData.attr('action', '<?=BASIS_URL?>/admin/kundenListePrint');
	postData.attr('target', '_blank');
	postData.clone($('.print_select:checkbox:checked'));
	$('.print_select:checkbox:checked').clone().appendTo(postData);
//add print titel if not empty
	if($.trim($('#print_titel').val()) !== ''){
		$('#print_titel').val( $.trim($('#print_titel').val()) );
		$('#print_titel').clone().appendTo(postData);
   }
	//meldung.html(postData);
	postData.appendTo('body');
	postData.submit();
	postData.remove();
});
		</script>
	</body>
</html>
