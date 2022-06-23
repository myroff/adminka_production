<?php
require_once BASIS_DIR.'/BLogic/Kurse/KursSelector.php';
use Kurse\KursSelector as KurSel;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
require_once BASIS_DIR.'/Tools/PaidMonthes.php';
use Tools\PaidMonthes as PM;
require_once BASIS_DIR.'/BLogic/Kunde/CommentToolsHtml.php';
use Kunde\CommentToolsHtml as CmntTlsHtml;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>SWIFF. Kunde by id.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />

		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>

		<script src="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.css">

		<script src="<?=BASIS_URL?>/Public/js/zebra_datepicker.js"></script>
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/zebra_default.css">

		<style>
			#updateItemTable, #addUnterrichtTable, #updateItem_Anrede, #UpdateUnt_Table
			{
				display:none;
				border:2px solid #a1a1a1;
				padding:20px;
				background:#dddddd;
				width:300px;
				border-radius:20px;

				position:fixed;
				top:250px;
				left:30%;
				z-index:100;
			}
			.zebra_datepicker{width:50px;}

			#privateDates, #chooseUnterricht{}
			#privateDates table{border:1px solid black;}
			table th, td{border-right:1px solid black;} table th:last-child, td:last-child{border-right:none;}
			#chooseUnterricht{margin-left:10px;}
		</style>
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

			<div id="privateDates">
				<table>
					<tr>
						<th>
							Kunden-Nummer
						</th>
						<!--<th>
							Eltern
						</th>-->
						<th class="itemName">
							Anrede
						</th>
						<th class="itemName">
							Vorname
						</th>
						<th class="itemName">
							Name
						</th>
						<th class="itemName">
							Geburtsdatum<br>
							(dd.mm.yyyy)
						</th>
						<th class="itemName">
							Telefon
						</th>
						<th class="itemName">
							Handy
						</th>
						<th class="itemName">
							Email
						</th>
						<th class="itemName">
							Strasse
						</th>
						<th class="itemName">
							Haus
						</th>
						<th class="itemName">
							Stadt
						</th>
						<th class="itemName">
							PLZ
						</th>
						<th class="itemName">
							Foto erlaubt?
						</th>
						<th class="itemName">
							Empfohlen durch
						</th>
					</tr>
					<tr>
						<td>
							<?=$res['kundenNummer']?>
						</td>
						<!--<td>
							 ElternInfo
						</td>-->
						<td class="itemValue">
							<?=$res['anrede']?>
						</td>
						<td class="itemValue">
							<?=$res['vorname']?>
						</td>
						<td class="itemValue">
							<?=$res['name']?>
						</td>
						<td class="itemValue">
							<?=Fltr::sqlDateToStr($res['geburtsdatum'])?>
						</td>
						<td class="itemValue">
							<?=$res['telefon']?>
						</td>
						<td class="itemValue">
							<?=$res['handy']?>
						</td>
						<td class="itemValue">
							<?=$res['email']?>
						</td>
						<td class="itemValue">
							<?=$res['strasse']?>
						</td>
						<td class="itemValue">
							<?=$res['strNr']?>
						</td>

						<td class="itemValue">
							<?=$res['stadt']?>
						</td>
						<td class="itemValue">
							<?=$res['plz']?>
						</td>
						<td class="itemValue" style="<?=$res['istFotoErlaubt'] === "0" ? "background:red;" : "";?>">
							<?=$res['istFotoErlaubt'] === "0" ? "Nein" : "Ja";?>
						</td>
						<td class="itemValue">
							<?=$res['empfohlenDurch']?>
						</td>
					</tr>
				</table>
				<!--Konto-Daten-->
				<table>
					<tr>
						<th>
							Zahlungsart
						</th>
						<th>
							Kontoinhaber
						</th>
						<th>
							Strasse
						</th>
						<th>
							Haus-Nr.
						</th>
						<th>
							PLZ
						</th>
						<th>
							Ort
						</th>
						<th>
							Bank
						</th>
						<th>
							IBAN
						</th>
						<th>
							BIC
						</th>
					</tr>
					<tr>
						<td>
							<?php
								echo Fltr::printZahlungsArt($res['payment_id']);
							?>
						</td>
						<td>
							<?=$res['kontoinhaber']?>
						</td>
						<td>
							<?=$res['zdStrasse']?>
						</td>
						<td>
							<?=$res['zdHausnummer']?>
						</td>
						<td>
							<?=$res['zdPlz']?>
						</td>
						<td>
							<?=$res['zdOrt']?>
						</td>
						<td>
							<?=$res['bankName']?>
						</td>
						<td>
							<?=$res['iban']?>
						</td>
						<td>
							<?=$res['bic']?>
						</td>
					</tr>
				</table>
			</div>
			<div id="kurListe">
				<table id="kundenResTbl">
					<tr>
						<th>
							Unterricht
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
							Von
						</th>
						<th>
							Bis
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
					foreach ($ures as $r)
					{
						$alter = $r['kurMinAlter'];
						$alter .= $r['kurMinAlter'] < $r['kurMaxAlter'] ? " bis ".$r['kurMaxAlter'] : '';

						$klasse = $r['kurMinKlasse'];
						$klasse .= $r['kurMinKlasse'] < $r['kurMaxKlasse'] ? " bis ".$r['kurMaxKlasse'] : "";

						$vonVal = Fltr::sqlDateToStr($r['von']);
						$bisVal = Fltr::sqlDateToStr($r['bis']);

						echo "<tr>";

						echo "<td>";
						echo "<b>".$r['kurName']."</b><br>";
						echo "<i>".$r['lVorname']." ".$r['lName']."</i><br>";
						//echo $r['termin'];
						echo Fltr::printSqlTermin($r['termin']);
						echo "</td>";
						echo "<td>".$r['kurPreis']."</td>";
						echo "<td>".$r['kurBeschreibung']."</td>";
						echo "<td>".$alter."</td>";
						echo "<td>".$klasse."</td>";
						echo "<td>". $vonVal."</td>";
						echo "<td>".$bisVal."</td>";

						echo "</tr>";
					}
				}
				?>
				</table>
			</div>
			<div style="float:left;padding:10px;border:1px solid black;">
				<?=PM::getTable($kId)?>
			</div>
		<!-- Kommentaren -->
			<div id="Kommentaren">
				<p><b>Kommentaren</b> <button id="newKndCmntTable_Open">Neuen Kommentar hinzufügen</button></p>
				<div id='newKndCmntTable'>
					<?php echo CmntTlsHtml::newCommentsForm($res['kndId']);?>
					<div>
						<button id="newKndCmntTable_Close">Schliessen</button>
					</div>
				</div>
				<div>
					<?php echo CmntTlsHtml::showComments($res['kndId']);?>
				</div>
			</div>
		</div><!--main content -->
		<!-- END OF CONTENT -->

		<!--JavaScript -->
		<script>
	//Kommentaren newKndCmntTable
		$('#newKndCmntTable_Open').click(function(){
			$('#newKndCmntTable').slideToggle(1000);
		});
		$('#newKndCmntTable_Close').click(function(){
			$('#newKndCmntTable').slideUp(1000);
		});
	//KommentarFunktionen
<?php echo CmntTlsHtml::newCommentsJsFnct($res['kndId']);?>
		</script>
	</body>
</html>