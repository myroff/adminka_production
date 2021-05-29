<?php
require_once BASIS_DIR.'/BLogic/Kurse/KursSelector.php';
use Kurse\KursSelector as KurSel;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
require_once BASIS_DIR.'/BLogic/Kunde/CommentToolsHtml.php';
use Kunde\CommentToolsHtml as CmntTlsHtml;
$nextYear = ACTUAL_YEAR + 1;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>SWIFF. Edit Kunde by id.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />
		
		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>
		
		<script src="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.css">
		
		<script src="<?=BASIS_URL?>/Public/js/zebra_datepicker.js"></script>
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/zebra_default.css">
		
		<style>
			#updateItemTable, #addKursTable, #updateItem_Anrede, #UpdateKur_Table, #Zahlungsformular, #updateItem_istFotoErlaubt, 
			#updateBankDates, #updateBankDates_isCash, #editSonderPreis_Table, #editKundenKursKomm_Table, #changeKurs_Table, #changeKursConfirm_Table
			{
				display:none;
				border:2px solid #a1a1a1;
				padding:20px; 
				background:#dddddd;
				min-width:450px;
				border-radius:20px;

				position:fixed;
				top:200px;
				left:30%;
				z-index:100;
			}
			
			.zebra_datepicker, .zebra_datepicker_dmy{width:70px;}
			
			#privateDates{overflow: hidden;}
			#privateDates table{float:left;}
			#privateDates table th{text-align: right;}
			
			#chooseKurs{}
			#chooseKurs{margin-left:10px;}
			#kurListe{clear:both;margin-top:10px;}
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

			<div id="privateDates">
				<table>
					<!--<tr>
						<th>
							Eltern
						</th>
						<td>

						</td>
					</tr>-->
					<tr>
						<th>Erstellt vom</th>
						<td><?=$res['mitarbeiter']?><br>
							[<?=date('d.m.Y H:i', strtotime($res['erstelltAm']))?>]
						</td>
					</tr>
					<tr>
						<th>Kunden kopieren</th>
						<td></td>
						<td><button type="button" id="kundeKopierenBtn">Kopieren ins <?=$nextYear?></button></td>
					</tr>
					<tr>
						<th>Kunden Löschen</th>
						<td></td>
						<td><button type="button" id="kundeLoeschenBtn">Löschen</button></td>
					</tr>
					<tr>
						<th class="itemName">
							Kunden-Nummer
						</th>
						<td class="itemValue">
							<?=$res['kundenNummer']?>
						</td>
						<td>
							<button type="button" class="editItem">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">
							Anrede
						</th>
						<td class="itemValue">
							<?=$res['anrede']?>
						</td>
						<td>
							<button type="button" class="editItem">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">
							Vorname
						</th>
						<td class="itemValue">
							<?=$res['vorname']?>
						</td>
						<td>
							<button type="button" class="editItem">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">
							Name
						</th>
						<td class="itemValue">
							<?=$res['name']?>
						</td>
						<td>
							<button type="button" class="editItem">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">
							Geburtsdatum<br>
							(dd.mm.yyyy)
						</th>
						<td class="itemValue">
							<?=$res['geburtsdatum']?>
						</td>
						<td>
							<button type="button" class="editItem">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">
							Telefon
						</th>
						<td class="itemValue">
							<?=$res['telefon']?>
						</td>
						<td>
							<button type="button" class="editItem">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">
							Handy
						</th>
						<td class="itemValue">
							<?=$res['handy']?>
						</td>
						<td>
							<button type="button" class="editItem">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">
							Email
						</th>
						<td class="itemValue">
							<?=$res['email']?>
						</td>
						<td>
							<button type="button" class="editItem">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">
							Strasse
						</th>
						<td class="itemValue">
							<?=$res['strasse']?>
						</td>
						<td>
							<button type="button" class="editItem">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">
							Haus
						</th>
						<td class="itemValue">
							<?=$res['strNr']?>
						</td>
						<td>
							<button type="button" class="editItem">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">
							Stadt
						</th>
						<td class="itemValue">
							<?=$res['stadt']?>
						</td>
						<td>
							<button type="button" class="editItem">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">
							PLZ
						</th>
						<td class="itemValue">
							<?=$res['plz']?>
						</td>
						<td>
							<button type="button" class="editItem">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">
							Geburtsland
						</th>
						<td class="itemValue">
							<?=$res['geburtsland']?>
						</td>
						<td>
							<button type="button" class="editItem">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">
							Muttersprache
						</th>
						<td class="itemValue">
							<?=$res['muttersprache']?>
						</td>
						<td>
							<button type="button" class="editItem">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">
							istFotoErlaubt
						</th>
						<td class="itemValue">
							<?=$res['istFotoErlaubt'] ? 'Ja' : 'Nein' ?>
						</td>
						<td>
							<button type="button" class="editItem">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th>
							Empfohlen durch
						</th>
						<td>
							<?=$res['empfohlenDurch']?><br>
							<?php
							require_once BASIS_DIR.'/BLogic/Kunde/Empfohlen.php';
							use Kunde\Empfohlen as Empf;
							Empf::setButton("empfohlenId");
							?>
						</td>
						<td>
							<button type="button" id="empfohlenIdSpeichern" class="editItem">Speichern</button>
						</td>
					</tr>
				</table>
			<!-- Zahlunsdaten -->
			<table style="margin-left:20px;">
					<tr>
						<th class="itemName">Zahlungsart</th>
						<td>
							<?php
							echo Fltr::printZahlungsArt($res['payment_id']);
							?>
						</td>
						<td>
							<button type="button" class="editBankDates">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">Kontoinhaber</th>
						<td class="itemValue">
							<?=$res['kontoinhaber']?>
						</td>
						<td>
							<button type="button" class="editBankDates">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">Bank</th>
						<td class="itemValue">
							<?=$res['bankName']?>
						</td>
						<td>
							<button type="button" class="editBankDates">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">IBAN</th>
						<td class="itemValue">
							<?=$res['iban']?>
						</td>
						<td>
							<button type="button" class="editBankDates">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">BIC</th>
						<td class="itemValue">
							<?=$res['bic']?>
						</td>
						<td>
							<button type="button" class="editBankDates">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">Strasse</th>
						<td class="itemValue">
							<?=$res['zdStrasse']?>
						</td>
						<td>
							<button type="button" class="editBankDates">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">Hausnummer</th>
						<td class="itemValue">
							<?=$res['zdHausnummer']?>
						</td>
						<td>
							<button type="button" class="editBankDates">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">Ort</th>
						<td class="itemValue">
							<?=$res['zdOrt']?>
						</td>
						<td>
							<button type="button" class="editBankDates">Bearbeiten</button>
						</td>
					</tr>
					<tr>
						<th class="itemName">PLZ</th>
						<td class="itemValue">
							<?=$res['zdPlz']?>
						</td>
						<td>
							<button type="button" class="editBankDates">Bearbeiten</button>
						</td>
					</tr>
				</table>
			</div>
			
			<div id="chooseKurs">
				<?php KurSel::getKursSelector("kurId", 'k_kurId', "10", "/admin/ajaxKursSelectorUpdate"); ?>
				<button onclick="addKurs()">Hinzufügen</button>
			</div>
			
			<div id="kurListe">
				<table id="kundenResTbl">
					<tr>
						<th>
							Kurs
						</th>
						<th>
							Lehrer
						</th>
						<th>
							Preis
						</th>
						<th>
							Sonderpreis
						</th>
						<th>
							Beschreibung
						</th>
						<th>
							Kommentar
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
						<th>
							Entfernen
						</th>
					</tr>
				<?php
				if(empty($res))
				{
				?>
					<tr>
						<td colspan="13">Nach Ihrer Anfrage wurden keine Daten gefunden.</td>
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
						
						echo "<td>".$r['kurName']."<br>";
						echo Fltr::printSqlTermin($r['termin']);
						echo "<br><button class='changeKursButton'  kurId='".$r['kurId']."' eintrId='".$r['eintrId']."'>Ändern</button>";
						echo "</td>";
						
						echo "<td>".$r['vorname']." ".$r['name']."</td>";
						//Preis
						echo "<td>".$r['kurPreis']." ";
						echo $r['kurIsStdPreis'] > 0 ? 'pro Stunde' : 'pro Monat';
						echo "</td>";
						//SonderPreis
						echo "<td>";
						if($r['sonderPreis'])
						{
							echo $r['sonderPreis']." ";
							echo $r['khkIsStdPreis'] > 0 ? 'pro Stunde' : 'pro Monat';
						}
						echo "<button class='editItemButton editSonderPreis' eintrId='".$r['eintrId']."' kurName='".$r['kurName']."'></button>";
						echo "</td>";
						echo "<td>".$r['kurBeschreibung']."</td>";
						echo "<td>".$r['khkKomm']."<button class='editItemButton editBeschreibung' eintrId='".$r['eintrId']."' kurName='".$r['kurName']."'></button>";
						echo "</td>";
						echo "<td>".$alter."</td>";
						echo "<td>".$klasse."</td>";
						echo "<td>".$vonVal."<button class='updateKurDateButton' "
								. "kurId='".$r['kurId']."' kurName='".$r['kurName']."' kurDateType='von' dateVal='$vonVal' eintrId='".$r['eintrId']."' ></button></td>";
						echo "<td>".$bisVal."<button class='updateKurDateButton' "
								. "kurId='".$r['kurId']."' kurName='".$r['kurName']."' kurDateType='bis' dateVal='$bisVal' eintrId='".$r['eintrId']."' ></button></td>";
						echo "<td><button class='kurEntfernenButton' eintrId='".$r['eintrId']."' >Löschen</button></td>";
						
						echo "</tr>";
					}
				}
				?>
				</table>
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
	<!-- Update Kundendaten -->
		<div id="updateItemTable">
			<div id="updateItemTable_Title"></div>
			<div>
				<form id="upateItemTable_Form" method="post" action="<?=$_SERVER['REQUEST_URI']?>">
					<input type="hidden" id="updateItemTable_Form_Name" name="updateItemTable_Form_Name" value="hiddenValue"/>
					<div id="upateItemTable_Form_ValueContainer">
						<input type="text" id="updateItemTable_Form_Value" name="updateItemTable_Form_Value"/>
					</div>
					<div class="buttonsOkCancelDiv">
						<input class="submit" id="updateItemTable_Form_ButtonSpeichern" type='submit' value='' />
						<button id="updateItemTable_Form_ButtonAbbrechen" class="cancel"></button>
					</div>
				</form>
			</div>
		</div>
	<!-- Update Anrede -->
		<div id="updateItem_Anrede">
			<div>Anrede</div>
			<div>
				<form method="post" action="<?=$_SERVER['REQUEST_URI']?>">
					<input type="hidden" name="updateItemTable_Form_Name" value="Anrede"/>
					<div>
						<select name="updateItemTable_Form_Value">
							<option value="Frau">Frau</option>
							<option value="Herr">Herr</option>
						</select>
					</div>
					<div class="buttonsOkCancelDiv">
						<input  class="submit" id="updateItem_Anrede_Form_ButtonSpeichern" type='submit' value='' />
						<button  id="updateItem_Anrede_ButtonAbbrechen" class="cancel"></button>
					</div>
				</form>
			</div>
		</div>
	<!-- Update istFotoErlaubt -->
		<div id="updateItem_istFotoErlaubt">
			<div>Ist Foto Erlaubt?</div>
			<div>
				<form method="post" action="<?=$_SERVER['REQUEST_URI']?>">
					<input type="hidden" name="updateItemTable_Form_Name" value="istFotoErlaubt"/>
					<div>
						<select name="updateItemTable_Form_Value">
							<option value="ja">Ja</option>
							<option value="nein">Nein</option>
						</select>
					</div>
					<div class="buttonsOkCancelDiv">
						<input  class="submit" id="updateItem_istFotoErlaunt_Form_ButtonSpeichern" type='submit' value='' />
						<button class="cancel" id="updateItem_istFotoErlaubt_ButtonAbbrechen" ></button>
					</div>
				</form>
			</div>
		</div>
	<!-- Kurs hinzufügen -->
		<div id="addKursTable">
			<div>Kurs hinzufügen</div>
			<div id="addKursTable_KurName"></div>
			<div>
				<form id="addKursTable_Form" method="post" action="<?=$_SERVER['REQUEST_URI']?>">
					<input type="hidden" id="addKursTable_Form_kndId" name="kndId" value=""/>
					<input type="hidden" id="addKursTable_Form_kurId" name="kurId" value=""/>
					von <input type="text" id="addKursTable_Form_von" name="von" class="zebra_datepicker_dmy" />
					bis <input type="text" id="addKursTable_Form_von" name="bis" class="zebra_datepicker_dmy" />
					<div>
						<b>Sonderpreis</b> <input type="checkbox" name="isSonderPreisSet" value="1"/><br>
						Preis <input style="width:40px;" type="text" name="sonderPreis" />
						<select id="updateItemTable_Form_Value" name="khkIsStdPreis" >
							<option value="proMonat">pro Monat</option>
							<option value="proStunde">pro Stunde</option>
						</select>
					</div>
					<div>
						Kommentar<br>
						<textarea name="khkKomm"></textarea>
					</div>
					<div class="buttonsOkCancelDiv">
						<input class="submit" id="addKursTable_Form_ButtonSpeichern" type='submit' value='' />
						<button class="cancel" id="addKursTable_Form_ButtonAbbrechen" ></button>
					</div>
				</form>
			</div>
		</div>
		<!-- Update Kurs von/bis -->
		<div id="UpdateKur_Table">
			<div id="UpdateKur_Title"></div>
			<div id="UpdateKur_FormDiv">
				<form id="UpdateKur_Form" method="post" action="<?=BASIS_URL?>/admin/ajaxUpdateKurToKunde">
					<input type="hidden" id="UpdateKur_kndId" name="kndId" value=""/>
					<input type="hidden" id="UpdateKur_eintrId" name="eintrId" value=""/>
					<input type="hidden" id="UpdateKur_kurId" name="kurId" value=""/>
					<input type="hidden" id="UpdateKur_TypeVal" name="typeVal" value=""/>
					<input type="text" id="UpdateKur_Val" name="dateVal" value="" class="zebra_datepicker_dmy"/>
					<div class="buttonsOkCancelDiv">
						<input class="submit" id="UpdateKur_ButtonSpeichern" type='submit' value='' />
						<button class="cancel" id="UpdateKur_ButtonAbbrechen" class="cancel"></button>
					</div>
				</form>
			</div>
		</div>
		<!--Zahlungsformular -->
		<div id="Zahlungsformular" method="post" action="<?=BASIS_URL?>/admin/ajaxKurBezahlen">
			<div id="zfKurName"></div>
			<div>
				<form id="zfForm">
					<input type="hidden" id="zfKndId" name="kndId" value=""/>
					<input type="hidden" id="zfKurId" name="kurId" value=""/>
					<input type="hidden" id="zfEintrId" name="eintrId" value=""/>
					<table>
						<tr>
							<td>
								Bezahlt für<br>Monat
							</td>
							<td>
								<input type="date" id="zfBezMonat" name="bzMonatJahr" class="zebra_datepicker"/>
							</td>
						</tr>
						<tr>
							<td>
								Betrag
							</td>
							<td>
								<input type="text" id="zfBetrag" name="bzSumme" />
							</td>
						</tr>
						<tr>
							<td>
								ist Bezahlt?
							</td>
							<td>
								<select name="bzIstBezahlt">
									<option value="1" selected>Ja</option>
									<option value="0">Nein</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								Kommentar
							</td>
							<td>
								<textarea id="zfKomm" name="bzKommentar" ></textarea>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input class="submit" id="zfButtonSpeichern" type='submit' value='' />
								<button class="cancel" id="zfButtonAbbrechen" class="cancel"></button>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	<!-- Update Zahlungsart -->
		<div id="updateBankDates_isCash">
			<div>Zahlungsart</div>
			<div>
				<form class="updateBankDates_Form" method="post" action="<?=$_SERVER['REQUEST_URI']?>">
					<input type="hidden" name="updateBankDates_Form_Name" value="isCash"/>
					<input type="hidden" name="kndId" value="<?=$res['kndId']?>"/>
					<div>
						<select name="updateBankDates_Form_Value">
							<option value="bar">Bar</option>
							<option value="lastschrift">Lastschrift</option>
							<option value="bamf">BAMF</option>
							<option value="zuzahler">Zuzahler</option>
							<option value="selbstzahler">Selbstzahler</option>
						</select>
					</div>
					<div class="buttonsOkCancelDiv">
						<input class="submit" id="updateBankDates_Form_ButtonSpeichern" type='submit' value='' />
						<button class="cancel" id="updateBankDates_isCash_ButtonAbbrechen" ></button>
					</div>
				</form>
			</div>
		</div>
	<!-- Update Bankdaten -->
		<div id="updateBankDates">
			<div id="updateBankDates_Title"></div>
			<div>
				<form class="updateBankDates_Form" method="post" action="<?=$_SERVER['REQUEST_URI']?>">
					<input type="hidden" id="updateBankDates_Form_Name" name="updateBankDates_Form_Name" value=""/>
					<input type="hidden" name="kndId" value="<?=$res['kndId']?>"/>
					<div id="updateBankDates_Form_ValueContainer">
						<input type="text" id="updateBankDates_Form_Value" name="updateBankDates_Form_Value"/>
					</div>
					<div class="buttonsOkCancelDiv">
						<input class="submit" id="updateBankDates_Form_ButtonSpeichern" type='submit' value='' />
						<button class="cancel" id="updateBankDates_Form_ButtonAbbrechen" class="cancel"></button>
					</div>
				</form>
			</div>
		</div>
	<!-- Update Sonderpreis -->
		<div id="editSonderPreis_Table">
			<div id="editSonderPreis_Titel"><button id="deleteSonderPreisButton" class="deleteButton" ></button>Sonderpreis bearbeiten. </div>
			<div>
				<form id="editSonderPreis_Form" method="post" action="<?=$_SERVER['REQUEST_URI']?>">
					<input type="hidden" id="editSonderPreis_eintrId" name="eintrId" value=""/>
					<div>
						SonderPreis: <input type="text" id="editSonderPreis_sonderPreis" name="sonderPreis" size="5"/>
						<select id="editSonderPreis_khkIsStdPreis" name="khkIsStdPreis" >
							<option value="proMonat">pro Monat</option>
							<option value="proStunde">pro Stunde</option>
						</select>
					</div>
					<div class="buttonsOkCancelDiv">
						<input class="submit" id="editSonderPreis_Form_ButtonSpeichern" type='submit' value=""/>
						<button class="cancel" id="editSonderPreis_Form_ButtonAbbrechen" ></button>
					</div>
				</form>
			</div>
		</div>
	<!-- Update KundenKurs-Kommentar -->
		<div id="editKundenKursKomm_Table">
			<div id="editKundenKursKomm_Titel"></div>
			<form id="editKundenKursKomm_Form" method="post" action="<?=$_SERVER['REQUEST_URI']?>">
				<input type="hidden" id="editKundenKursKomm_eintrId" name="eintrId"/>
				<div><textarea id="KursBeschreibung_Form_Textarea" name="khkKomm"></textarea></div>
				<div class="buttonsOkCancelDiv">
					<input class="submit" id="editKundenKursKomm_Form_ButtonSpeichern" type='submit' value="" />
					<button class="cancel" id="editKundenKursKomm_Form_ButtonAbbrechen" ></button>
				</div>
			</form>
		</div>
	<!-- message box !-->
		<div id="messageBox">
			<button id="messageBox_OkButton" class="submit" ></button>
			<div id="messageBox_message"></div>
		</div>
<!-- change Kurs -->
		<div id="changeKurs_Table">
				<input type="hidden" id="changeKurs_oldKurId" />
				<input type="hidden" id="changeKurs_eintrId" />
				<?php KurSel::getKursSelector("newKurId", "changeKurs_newKurId",  "10", "/admin/ajaxKursSelectorUpdate"); ?>
				<div class="buttonsOkCancelDiv">
					<button class="submit" id="changeKurs_Table_ButtonSpeichern" ></button>
					<button class="cancel" id="changeKurs_Table_ButtonAbbrechen" ></button>
				</div>
		</div>

		<div id="changeKursConfirm_Table" style="z-index:150;">
			<table>
				<tr>
					<td><span id="changeKursConfirm_Table_oldKurName"></span></td>
					<td>--></td>
					<td><span id="changeKursConfirm_Table_newKurName"></span></td>
				</tr>
			</table>
			<form id="changeKursConfirm_Form">
				<input type="hidden" id="changeKursConfirm_Form_kndId" name="kndId"/>
				<input type="hidden" id="changeKursConfirm_Form_eintrId" name="eintrId"/>
				<input type="hidden" id="changeKursConfirm_Form_oldKurId" name="oldKurId"/>
				<input type="hidden" id="changeKursConfirm_Form_newKurId" name="newKurId"/>
				<div class="buttonsOkCancelDiv">
					<input class="submit" id="changeKursConfirm_Form_ButtonSpeichern" type='submit' value='' />
					<button class="cancel" id="changeKursConfirm_Form_ButtonAbbrechen" ></button>
				</div>
			</form>
		</div>
		<!--JavaScript -->
		<script>
			var kndId = <?=$res['kndId']?>;
			var meldung = $('#meldung');
			var updateTable = $('#updateItemTable');
			var updateTable_Title = $('#updateItemTable_Title');
			var upateTable_Form = $('#upateItemTable_Form');
			var updateTable_Form_Name = $('#updateItemTable_Form_Name');
			var Update_ValueContainter = $('#upateItemTable_Form_ValueContainer');
			var updateTable_Form_Value = $('#updateItemTable_Form_Value');
			var updateTable_Form_ButtonAbbrechen = $('#updateItemTable_Form_ButtonAbbrechen');
			
			//$("form [name=vorname]").css({'background':'red'});
			//$('form').attr("name", "plz").css({'background':'red'});
			var itemName;
			
			$(".zebra_datepicker").Zebra_DatePicker({
				format: 'm.Y'   //  note that becase there's no day in the format
								//  users will not be able to select a day!
			});
			
			$(".zebra_datepicker_dmy").Zebra_DatePicker({
				format: 'd.m.Y',	//note that becase there's no day in the format
						//users will not be able to select a day!
			});
			  
			//click at button "Bearbeiten"
			$('.editItem').click(function()
			{
				var parent = $(this).parent().parent();
				itemName = parent.children('.itemName').text().trim();
				itemName = itemName.replace(/\s+/, ' ');
				var itemValue = parent.children('.itemValue').text().trim();
				
				if(typeof(itemName) === "undefined")
				{
					meldung.html("itemName ist nicht definiert.");
					return;
				}
				
				if(itemName === "Anrede")
				{
					$('#updateItem_Anrede').slideDown(1000);
					return;
				}
				
				if(itemName === "istFotoErlaubt")
				{
					$('#updateItem_istFotoErlaubt').slideDown(1000);
					return;
				}
				
				if(itemName === "Geburtsdatum (dd.mm.yyyy)")
				{
					
					updateTable_Form_Value.datepicker({
						dateFormat: "dd.mm.yy",
						changeMonth: true,
						changeYear: true,
						yearRange: "-90:+10",
						/*
						showOn: "button",
						buttonImage: "images/calendar.gif",
						buttonImageOnly: true,
						buttonText: "Geburtstag wählen"
						*/
					});
					/*
					updateTable_Form_Value.Zebra_DatePicker({
						format: 'd.m.Y'
					  });*/
				}
				
				updateTable_Title.text(itemName);
				updateTable_Form_Value.val(itemValue);
				updateTable_Form_Name.val(itemName);
				
				updateTable.slideDown(1000);
			});
			
			$('#updateItem_Anrede_ButtonAbbrechen').click(function(e){
				e.preventDefault();
				$('#updateItem_Anrede').slideUp('slow');
			});
			
			$('#updateItem_istFotoErlaubt_ButtonAbbrechen').click(function(e){
				e.preventDefault();
				$('#updateItem_istFotoErlaubt').slideUp('slow');
			});
			
			//close update pop-up
			updateTable_Form_ButtonAbbrechen.click(function(e){
				e.preventDefault();
				updateTable.slideUp('slow');
				
				if(itemName === "Geburtsdatum (dd.mm.yyyy)")
				{
					updateTable_Form_Value.datepicker('destroy');
					/*
					var dp = $('#updateItemTable_Form_Value').data('Zebra_DatePicker');
					dp.destroy();
					dp.update();
					*/
				}
			});
			
//unterricht hinzufügen
			function addKurs()
			{
				var kurId = $('#k_kurId').val();
				var text = $('#k_kurId option:selected').text();
				
				$('#addKursTable_KurName').html(text);
				$('#addKursTable_Form_kndId').val(kndId);
				$('#addKursTable_Form_kurId').val(kurId);
				
				$('#addKursTable').slideDown('slow');
			}
			
			$('#addKursTable_Form_ButtonAbbrechen').click(function(e){
				e.preventDefault();
				$('#addKursTable').slideUp('slow');
			});
			
			$('#addKursTable_Form').submit(function(e){
				e.preventDefault();
				var postData = $(this).serializeArray();
				
				$.ajax({
					url:'<?=BASIS_URL?>/admin/ajaxAddKursToKunde',
					type:'POST',
					data:postData,
					dataType:'JSON',
					success:function(response){
						if(response.status == 'ok')
						{
							alert(response.status);
							window.location.reload(true);
						}
						else
						{
							alert(response.status);
						}
						meldung.html(response.info);//response.dataPost
					},
					error:function(errorThrown){
						meldung.html(errorThrown);
					}
				});
			});
			
//change Kurs
			
			$('.changeKursButton').click(function(){
				kurId = $(this).attr('kurid');
				eintrId = $(this).attr('eintrid');
				$('#changeKurs_oldKurId').val(kurId);
				$('#changeKurs_eintrId').val(eintrId);
				$('#changeKurs_Table').slideDown('slow');
			});
			
			$('#changeKurs_Table_ButtonAbbrechen').click(function(e){
				e.preventDefault();
				$('#changeKurs_Table').slideUp('slow');
			});
			
			$('#changeKurs_Table_ButtonSpeichern').click(function(e){
				eintrId = $('#changeKurs_eintrId').val();
				oldKurId = $('#changeKurs_oldKurId').val();
				newKurId = $('#changeKurs_newKurId option:selected').val();
				
				//<input type="hidden" id="changeKursConfirm_Form_kndId" name="kndId"/>
				//<input type="hidden" id="changeKursConfirm_Form_oldKurId" name="oldKurId"/>
				
				$('#changeKursConfirm_Form_oldKurId').val(oldKurId);
				$('#changeKursConfirm_Form_eintrId').val(eintrId);
				$('#changeKursConfirm_Form_newKurId').val(newKurId);
				$('#changeKursConfirm_Form_kndId').val(kndId);
				
				$.ajax({
					url:'<?=BASIS_URL?>/admin/ajaxChangeKursInfo',
					type:'POST',
					data:{ oldKurId: oldKurId, newKurId: newKurId, eintrId:eintrId },
					dataType:'JSON',
					success:function(response){
						if(response.status == 'ok')
						{
							//kur = JSON.parse(response.message);
							$('#changeKursConfirm_Table_oldKurName').html(response.message.oldKur);
							$('#changeKursConfirm_Table_newKurName').html(response.message.newKur);
							
							//alert(response.message.oldKur);
						}
						else
						{
							alert(response.message);
						}
						meldung.html(response.message);//response.dataPost
					},
					error:function(errorThrown){
						meldung.html(errorThrown);
					}
				});
				
				$('#changeKursConfirm_Table').slideDown('slow');
			});
			
			$('#changeKursConfirm_Form_ButtonAbbrechen').click(function(e){
				e.preventDefault();
				$('#changeKursConfirm_Table').slideUp('slow');
			});
			
			$('#changeKursConfirm_Form').submit(function(e){
				e.preventDefault();
				var postData = $(this).serializeArray();
				
				$.ajax({
					url:'<?=BASIS_URL?>/admin/ajaxChangeKursReallyDo',
					type:'POST',
					data:postData,
					dataType:'JSON',
					success:function(response){
						if(response.status == 'ok')
						{
							alert(response.message);
							window.location.reload(true);
						}
						else
						{
							alert(response.message);
						}
						meldung.html(response.message);
					},
					error:function(errorThrown){
						meldung.html(errorThrown);
					}
				});
			});
			
		//update Kurs
			
			$('.updateKurDateButton').click(function()
			{
				kurDateType = $(this).attr('kurDateType').trim();
				kurId = $(this).attr('kurId');
				date = $(this).attr('dateVal').trim();
				eintrId = $(this).attr('eintrId');
				
				$('#UpdateKur_eintrId').val(eintrId);
				$('#UpdateKur_kndId').val(kndId);
				$('#UpdateKur_kurId').val(kurId);
				$('#UpdateKur_TypeVal').val(kurDateType);
				$('#UpdateKur_Val').val(date);
				$('#UpdateKur_Title').text(kurDateType);
				$('#UpdateKur_Table').slideDown('slow');
			});
			
			$('#UpdateKur_Form').submit(function(e){
				e.preventDefault();
				var postData = $(this).serializeArray();
				
				$.ajax({
					url:'<?=BASIS_URL?>/admin/ajaxUpdateKursToKunde',
					type:'POST',
					data:postData,
					dataType:'JSON',
					success:function(response){
						if(typeof response.fehler === 'undefined')
						{
							window.location.reload(true);
						}
						else
						{
							alert(response.fehler);
						}
					},
					error:function(errorThrown){
						meldung.html(errorThrown);
					}
				});
				
				$('#UpdateKur_Table').slideUp('slow');
			});
			
			$('#UpdateKur_ButtonAbbrechen').click(function(e){
				e.preventDefault();
				$('#UpdateKur_Table').slideUp('slow');
			});
			
		//Bezahlungsformular
			
			$('.kurBezahlenButton').click(function()
			{
				untName = $(this).attr('kurName').trim();
				kurId = $(this).attr('kurId');
				eintrId = $(this).attr('eintrId');
				
				$('#zfEintrId').val(eintrId);
				$('#zfKndId').val(kndId);
				$('#zfKurId').val(kurId);
				$('#zfKurName').html(untName);
				$('#Zahlungsformular').slideDown('slow');
			});
			
			$('#zfButtonAbbrechen').click(function(e){
				e.preventDefault();
				$('#Zahlungsformular').slideUp('slow');
			});
			
			$('#zfForm').submit(function(e){
				e.preventDefault();
				var postData = $(this).serializeArray();
				
				$.ajax({
					url:'<?=BASIS_URL?>/admin/ajaxKurBezahlen',
					type:'POST',
					data:postData,
					dataType:'JSON',
					success:function(response){
						if(typeof response.fehler === 'undefined')
						{
							//window.location.reload(true);
							alert(response.info);
						}
						else
						{
							alert(response.fehler);
						}
					},
					error:function(errorThrown){
						meldung.html(errorThrown);
					}
				});
				
				//$('#Zahlungsformular').slideUp('slow');
			});
		//Kurs entfernen
			$('.kurEntfernenButton').click(function()
			{
				eintrId = $(this).attr('eintrId');
				
				if (confirm('Wollen Sie wirklich diesen Kurs von dem Kunde entfernen?')) {
					$.ajax({
						url:'<?=BASIS_URL?>/admin/ajaxKursVomKundeEntfernen',
						type:'POST',
						data:{eintrId:eintrId},
						dataType:'JSON',
						success:function(response){
							if(typeof response.fehler === 'undefined')
							{
								alert(response.info);
								window.location.reload(true);
							}
							else
							{
								alert(response.fehler);
							}
						},
						error:function(errorThrown){
							meldung.html(errorThrown);
						}
					});
				} else {
					// Do nothing!
				}
			});
//edit Bankdaten
			$('.editBankDates').click(function()
			{
				var parent = $(this).parent().parent();
				bitemName = parent.children('.itemName').text().trim().toLowerCase();
				bitemName = bitemName.replace(/\s+/, ' ');
				var bitemValue = parent.children('.itemValue').text().trim();
				
				if(typeof(bitemName) === "undefined")
				{
					alert("BankItemName ist nicht definiert.");
					return;
				}
				
				if(bitemName === "zahlungsart")
				{
					$('#updateBankDates_isCash').slideDown(1000);
					return;
				}
				
				$('#updateBankDates_Title').text(bitemName);
				$('#updateBankDates_Form_Name').val(bitemName);
				$('#updateBankDates_Form_Value').val(bitemValue);
				
				$('#updateBankDates').slideDown(1000);
			});
			
		//close update BankDates
			$('#updateBankDates_Form_ButtonAbbrechen').click(function(e){
				e.preventDefault();
				$('#updateBankDates').slideUp('slow');
			});
			
			$('#updateBankDates_isCash_ButtonAbbrechen').click(function(e){
				e.preventDefault();
				$('#updateBankDates_isCash').slideUp('slow');
			});
			
		//update BankDates
			$('.updateBankDates_Form').submit(function (e){
				e.preventDefault();
				var postData = $(this).serializeArray();
				
				$.ajax({
					url:'<?=BASIS_URL?>/admin/ajaxBankDatesUpdate',
					type:'POST',
					data:postData,
					dataType:'JSON',
					success:function(response){
						if(response.status == 'ok')
						{
							alert(response.status);
							window.location.reload(true);
						}
						else
						{
							alert(response.status);
						}
						meldung.html(response.info);//response.dataPost
					},
					error:function(errorThrown){
						meldung.html(errorThrown);
					}
				});
			});
			
		//edit SonderPreis deleteSonderPreisButton
			$('.editSonderPreis').click(function(){
				eId = $(this).attr('eintrId');
				$('#editSonderPreis_Titel').prepend($(this).attr('kurName')+': ');
				$('#editSonderPreis_eintrId').val(eId);
				
			//prepare deleteButton
				$('#deleteSonderPreisButton').attr({eintrId:eId});
				
				$('#editSonderPreis_Table').slideDown(1000);
			});
			
			$('#editSonderPreis_Form_ButtonAbbrechen').click(function(e){
				e.preventDefault();
				$('#editSonderPreis_Table').slideUp('slow');
			});
			
			$('#editSonderPreis_Form').submit(function(e){
				e.preventDefault();
				var postData = $(this).serializeArray();
				
				$.ajax({
					url:'<?=BASIS_URL?>/admin/ajaxEditSonderPreis',
					type:'POST',
					data:postData,
					dataType:'JSON',
					success:function(response){
						if(response.status == 'ok')
						{
							alert(response.status);
							window.location.reload(true);
						}
						else
						{
							alert(response.status);
						}
						meldung.html(response.info);//response.dataPost
					},
					error:function(errorThrown){
						meldung.html(errorThrown);
					}
				});
			});
			
			$('#deleteSonderPreisButton').click(function(){
				eId = $(this).attr('eintrId');
				
				$.ajax({
					url:'<?=BASIS_URL?>/admin/ajaxDeleteSonderPreis',
					type:'POST',
					data:{eintrId:eId},
					dataType:'JSON',
					success:function(response){
						if(response.status == 'ok')
						{
							alert(response.status);
							window.location.reload(true);
						}
						else
						{
							alert(response.status);
						}
						meldung.html(response.info);//response.dataPost
					},
					error:function(errorThrown){
						meldung.html(errorThrown);
					}
				});
			});
			
	//edit Beschreibung
			$('.editBeschreibung').click(function(){
				$('#KursBeschreibung_Form_Textarea').val($(this).parent().text());
				
				eId = $(this).attr('eintrId');
				$('#editKundenKursKomm_eintrId').val(eId);
				
				$('#editKundenKursKomm_Titel').text("Kommentar: "+$(this).attr('kurName'));
				
				$('#editKundenKursKomm_Table').slideDown(1000);
			});
			
			$('#editKundenKursKomm_Form_ButtonAbbrechen').click(function(e){
				e.preventDefault();
				$('#editKundenKursKomm_Table').slideUp(1000);
			});
			
			//admin/ajaxUpdateKhkKomm
			$('#editKundenKursKomm_Form').submit(function(e){
				e.preventDefault();
				var postData = $(this).serializeArray();
				
				$.ajax({
					url:'<?=BASIS_URL?>/admin/ajaxUpdateKhkKomm',
					type:'POST',
					data:postData,
					dataType:'JSON',
					success:function(response){
						if(response.status == 'ok')
						{
							alert(response.status);
							window.location.reload(true);
						}
						else
						{
							alert(response.status);
						}
						meldung.html(response.info);//response.dataPost
					},
					error:function(errorThrown){
						meldung.html(errorThrown);
					}
				});
			});
	//Kunde Löschen
		$('#kundeLoeschenBtn').click(function(){
			if(confirm('Wollen Sie wirklich diesen Termin löschen?'))
			{
				$.ajax({
					url:'<?=BASIS_URL?>/admin/ajaxDeleteKunde',
					type:'POST',
					data:{kndId:kndId},
					dataType:'JSON',
					success:function(response){
						if(response.status === 'ok')
						{
							alert(response.message);
							self.location = "<?=BASIS_URL?>/admin/kundenBearbeitenListe";
						}
						else
						{
							$('#messageBox_message').html(response.message);
							$('#messageBox').slideDown(1000);
							//alert("<b>Fehler1:</b> "+response.message);
						}
					},
					error:function(errorThrown){
						meldung.html(errorThrown);
					}
				});
			}
			else{

			}
		});
	//message box schliessen
		$('#messageBox_OkButton').click(function(){
			$('#messageBox').slideUp(1000);
			$('#messageBox_message').html("");
		});
		
	//update empfohlenId
		$("#empfohlenIdSpeichern").click(function(){
			kndId = $("#empfohlenKndIdInput").val().trim();
			
			updateTable_Form_Value.val(kndId);
			updateTable_Form_Name.val("empfohlenId");
			
			upateTable_Form.submit();
		});
	//Kommentaren newKndCmntTable
		$('#newKndCmntTable_Open').click(function(){
			$('#newKndCmntTable').slideToggle(1000);
		});
		$('#newKndCmntTable_Close').click(function(){
			$('#newKndCmntTable').slideUp(1000);
		});
	//KommentarFunktionen
<?php echo CmntTlsHtml::newCommentsJsFnct($res['kndId']);?>

//Kunde kopieren ins neuen DB
		$('#kundeKopierenBtn').click(function(){
			if(confirm('Wollen Sie wirklich diesen Kunden ins Jahr <?=$nextYear?> kopieren?'))
			{
				$.ajax({
					url:'<?=BASIS_URL?>/admin/copyKndToNewDb',
					type:'POST',
					data:{kndId:kndId},
					dataType:'JSON',
					success:function(response){
						if(response.status === 'ok')
						{
							alert(response.message);
						}
						else
						{
							alert(response.message);
						}
					},
					error:function(errorThrown){
						meldung.html(errorThrown);
					}
				});
			}
			else{

			}
		});
		</script>
	</body>
</html>