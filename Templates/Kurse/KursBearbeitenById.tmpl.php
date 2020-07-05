<?php
require_once BASIS_DIR.'/Tools/TmplTools.php';
use Tools\TmplTools as TmplTls;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>SWIFF: Kurs bearbeiten bei id.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />
		
		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>
		
		<script src="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.css">
		
		<style>
			#updateItemTable, #updateAlterTable, #updateKlassenTable, #updateBeschreibungTable, #addTermin,
			#updateLehrerTable, #updateTerminTable, #updateZahlungstypeTable, #messageBox, #updateIsKurInactiveTable
			{
				display:none;
				border:2px solid #a1a1a1;
				padding:20px; 
				background:#dddddd;
				width:460px;
				border-radius:20px;

				position:fixed;
				top:250px;
				left:30%;
				z-index:100;
			}
			
			.kurTrmDiv{border-top:1px solid black;padding:3px 0px;width:400px;}
		</style>
	</head>
	<body>
		<div id="horizontalMenu">
			<?php
			require_once BASIS_DIR.'/Templates/Menu.class.php';
			TemplateTools\Menu::adminMenu();
			
			$alter = $res['kurMinAlter'];
			$alter .= $res['kurMinAlter'] < $res['kurMaxAlter'] ? " bis ".$res['kurMaxAlter'] : '';

			$klasse = $res['kurMinKlasse'];
			$klasse .= $res['kurMinKlasse'] < $res['kurMaxKlasse'] ? " bis ".$res['kurMaxKlasse'] : "";
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

			<table>
				<tr>
					<th class="itemName">
						Löschen
					</th>
					<td class="itemValue">
						<?=$res['kurName']?>
					</td>
					<td>
						<button type="button" class="delete deleteKurs" kurId='<?=$res['kurId']?>' >Löschen</button>
					</td>
				</tr>
				<tr>
					<th class="itemName">
						Name
					</th>
					<td class="itemValue">
						<?=$res['kurName']?>
					</td>
					<td>
						<button type="button" class="editItem">Bearbeiten</button>
					</td>
				</tr>
				<tr>
					<th class="itemName">
						Max. Kunden
					</th>
					<td class="itemValue">
						<?=$res['maxKnd']?>
					</td>
					<td>
						<button type="button" class="editItem">Bearbeiten</button>
					</td>
				</tr>
				<tr>
					<th class="itemName">
						Lehrer
					</th>
					<td class="itemValue">
						<?=$res['lehrVorname']?> <?=$res['lehrName']?>
					</td>
					<td>
						<button type="button" id="updateLehrer">Bearbeiten</button>
					</td>
				</tr>
				<tr>
					<th class="itemName">
						Beschreibung
					</th>
					<td class="itemValue">
						<?=$res['kurBeschreibung']?>
					</td>
					<td>
						<button type="button" id="updateBeschreibung">Bearbeiten</button>
					</td>
				</tr>
				<tr>
					<th class="itemName">
						Preis
					</th>
					<td class="itemValue">
						<?=$res['kurPreis']?>
					</td>
					<td>
						<button type="button" class="editItem">Bearbeiten</button>
					</td>
				</tr>
				<tr>
					<th class="itemName">
						Zahlungstype
					</th>
					<td class="itemValue">
						<?php
							if($res['kurIsStdPreis']) echo 'pro Stunde';
							else echo 'pro Monat';
						?>
					</td>
					<td>
						<button type="button" class="editItem">Bearbeiten</button>
					</td>
				</tr>
				<tr>
					<th class="itemName">
						Alter
					</th>
					<td class="itemValue">
						<?=$alter?>
					</td>
					<td>
						<button type="button" id="updateAlter">Bearbeiten</button>
					</td>
				</tr>
				<tr>
					<th class="itemName">
						Klassen
					</th>
					<td class="itemValue">
						<?=$klasse?>
					</td>
					<td>
						<button type="button" id="updateKlassen">Bearbeiten</button>
					</td>
				</tr>
				<tr>
					<th class="itemName">
						Kurs aktiv?
					</th>
					<td class="itemValue">
						<?php
							if($res['isKurInactive'])
							{
								echo "nein";
							}
							else{
								echo "ja";
						   }
						?>
					</td>
					<td>
						<button type="button" id="updateIsKurInactive">Bearbeiten</button>
					</td>
				</tr>
			</table>
			<div>
				<button style="padding:10px;" onclick="addNewTermin();">Neuen Termin</button>
			</div>
			<div style="margin-top:10px;">
			<?php
				if(empty($trm))
				{
					echo 'Keine Termine gesetz';
				}
				else
				{
					foreach($trm as $t)
					{
						echo"<div class='kurTrmDiv'>";

						echo "Saison: ".$t['season_name']."<br>";
						echo "Raum ".$t['raum']."<br>";
						echo Fltr::indxToWeekday($t['wochentag'])."<br>";
						echo Fltr::sqlTimeToStr($t['anfang'])." - ".Fltr::sqlTimeToStr($t['ende']);
						
						echo "<button type='button' class='editTermin editItemButton' stnPlId='".$t['stnPlId']."' raum='".$t['raum']."'"." season_id=".$t['season_id']
								. " wochentag='".$t['wochentag']."' anfang='".Fltr::sqlTimeToStr($t['anfang'])."' ende='".Fltr::sqlTimeToStr($t['ende'])."' >1</button>";
						echo "<button type='button' class='deleteTermin deleteButton' stnPlId='".$t['stnPlId']."' >2</button>";
						echo"</div>";
					}
				}
			?>
			</div>
		</div>
		<!-- END OF CONTENT -->
		<div id="updateItemTable">
			<div id="updateItemTable_Title"></div>
			<div>
				<form id="updateItemTable_Form" method="post" action="<?=$_SERVER['REQUEST_URI']?>">
					<input type="hidden" id="updateItemTable_Form_Name" name="updateItemTable_Form_Name" value=""/>
					<input type="text" id="updateItemTable_Form_Value" name="updateItemTable_Form_Value"/>
					<div>
						<input id="updateItemTable_Form_ButtonSpeichern" type='submit' value='' class="submit" />
						<button id="updateItemTable_Form_ButtonAbbrechen" class="cancel" ></button>
					</div>
				</form>
			</div>
		</div>
		<!-- Update für Zahlungstype -->
		<div id="updateZahlungstypeTable">
			<div id="updateItemTable_Title"></div>
			<div>
				<form id="upateZahlungstypeTable_Form" method="post" action="<?=$_SERVER['REQUEST_URI']?>">
					<input type="hidden" id="updateItemTable_Form_Name" name="updateItemTable_Form_Name" value="Zahlungstype"/>
					<select id="updateItemTable_Form_Value" name="updateItemTable_Form_Value" >
						<option value="proMonat">pro Monat</option>
						<option value="proStunde">pro Stunde</option>
					</select>
					<div>
						<input id="updateItemTable_Form_ButtonSpeichern" type='submit' value='' class="submit" />
						<button id="updateZahlungstypeTable_Form_ButtonAbbrechen" class="cancel" ></button>
					</div>
				</form>
			</div>
		</div>
		<!-- Update für Lehrer -->
		<div id="updateLehrerTable">
			<div>Lehrer</div>
			<div>
				<form method="post" action="<?=$_SERVER['REQUEST_URI']?>">
					<input type="hidden" id="updateItemTable_Form_Name" name="updateItemTable_Form_Name" value="Lehrer"/>
					<?php echo TmplTls::getLehrerSelector("updateItemTable_Form_Value", "lehrId"); ?>
					<div>
						<input type='submit' value='' class="submit" />
						<button id="closeUpdateLehrer" class="cancel" ></button>
					</div>
				</form>
			</div>
		</div>
		<!-- Update für Beschreibung -->
		<div id="updateBeschreibungTable">
			<div>Beschreibung</div>
			<div>
				<form method="post" action="<?=$_SERVER['REQUEST_URI']?>">
					<input type="hidden" id="updateItemTable_Form_Name" name="updateItemTable_Form_Name" value="Beschreibung"/>
					<textarea id="Beschreibung_Form_Value" name="updateItemTable_Form_Value" cols="45" rows="4"></textarea>
					<div>
						<input  type='submit' value='' class="submit" />
						<button id="closeUpdateBeschreibung" class="cancel" ></button>
					</div>
				</form>
			</div>
		</div>
		<!-- Update für Alter -->
		<div id="updateAlterTable">
			<div>Alter</div>
			<div>
				<form method="post" action="<?=$_SERVER['REQUEST_URI']?>">
					<input type="hidden" name="updateItemTable_Form_Name" value="Alter"/>
					<input type="hidden" name="updateItemTable_Form_Value" value="leer"/>
					von <input name='kurMinAlter' type='text' class='decimalSpiner' />
					bis <input name='kurMaxAlter' type='text' class='decimalSpiner' />
					<div>
						<input id="updateItemTable_Form_ButtonSpeichern" type='submit' value='' class="submit" />
						<button id="closeUpdateAlter" class="cancel" ></button>
					</div>
				</form>
			</div>
		</div>
		<!-- Update für Klasse -->
		<div id="updateKlassenTable">
			<div>Klassen</div>
			<div>
				<form method="post" action="<?=$_SERVER['REQUEST_URI']?>">
					<input type="hidden" name="updateItemTable_Form_Name" value="Klassen"/>
					<input type="hidden" name="updateItemTable_Form_Value" value="leer"/>
					von <input name='kurMinKlasse' type='text' class='decimalSpiner' />
					bis <input name='kurMaxKlasse' type='text' class='decimalSpiner' />
					<div>
						<input id="updateItemTable_Form_ButtonSpeichern" type='submit' value='' class="submit" />
						<button id="closeUpdateKlassen" class="cancel" ></button>
					</div>
				</form>
			</div>
		</div>
		<!-- Form für neuen Termin -->
		<div id="addTermin">
			<form id="addTerminForm" method="post" action="<?=$_SERVER['REQUEST_URI']?>">
				<input type="hidden" name="kurId" value="<?=$res['kurId']?>"/>
				<table>
					<tr>
						<th>
							Saison
						</th>
						<td>
							<?= TmplTls::getSeasonsSelector("season_id", "season_id")?>
						</td>
					</tr>
					<tr>
						<th>
							Raum
						</th>
						<td>
							<input name="raum" type="text" />
						</td>
					</tr>
					<tr>
						<th>
							Tag
						</th>
						<td>
							<?php echo TmplTls::getWeekdaySelector("wochentag"); ?>
						</td>
					</tr>
					<tr>
						<th>
							Zeit<br>(format: hh:mm)
						</th>
						<td>
							von <input name="anfang" type="time" />
							bis <input name="ende" type="time" />
						</td>
					</tr>
					<tr>
						<td colspan="2"><input type='submit' value='' class="submit" />
						<button id="closeAddTermin" class="cancel" ></button></td>
					</tr>
				</table>
			</form>
		</div>
		<!-- update Termin -->
		<div id="updateTerminTable">
			<form id="updateTerminForm" method="post" action="<?=$_SERVER['REQUEST_URI']?>">
				<input type="hidden" name="stnPlId" value="" id="updateTerminForm_stnPlId"/>
				<table>
					<tr>
						<th>
							Saison
						</th>
						<td>
							<?= TmplTls::getSeasonsSelector("season_id", "updateTerminForm_season_id")?>
						</td>
					</tr>
					<tr>
						<th>
							Raum
						</th>
						<td>
							<input name="raum" type="text" id="updateTerminForm_Raum"/>
						</td>
					</tr>
					<tr>
						<th>
							Tag
						</th>
						<td>
							<?php echo TmplTls::getWeekdaySelector("wochentag", "updateTerminForm_Wochentag"); ?>
						</td>
					</tr>
					<tr>
						<th>
							Zeit<br>(format: hh:mm)
						</th>
						<td>
							von <input name="anfang" type="time" class='timepicker'id="updateTerminForm_Anfang"/>
							bis <input name="ende" type="time" class='timepicker' id="updateTerminForm_Ende"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type='submit' value='' class="submit"/>
							<button id="closeUpdateTermin" class="cancel" ></button>
						</td>
					</tr>
				</table>
			</form>
		</div>
		<!-- message box !-->
		<div id="messageBox">
			<button id="messageBox_OkButton" class="submit" ></button>
			<div id="messageBox_message"></div>
		</div>
		<!-- Update für isKurInactive -->
		<div id="updateIsKurInactiveTable">
			<div>Kurs aktiv?</div>
			<div>
				<form method="post" action="<?=$_SERVER['REQUEST_URI']?>">
					<input type="hidden" id="updateItemTable_Form_Name" name="updateItemTable_Form_Name" value="isKurInactive"/>
					<select name="updateItemTable_Form_Value">
						<option value="ja">ja</option>
						<option value="nein">nein</option>
					</select>
					<div class="buttonsOkCancelDiv">
						<input type='submit' class="submit" />
						<button id="closeUpdateIsKurActiveTable" class="cancel" ></button>
					</div>
				</form>
			</div>
		</div>
		<!--JavaScript -->
		<script>
			var meldung = $('#meldung');
			var updateTable = $('#updateItemTable');
			var updateTable_Title = $('#updateItemTable_Title');
			var upateTable_Form = $('#updateItemTable_Form');
			var updateTable_Form_Name = $('#updateItemTable_Form_Name');
			var updateTable_Form_Value = $('#updateItemTable_Form_Value');
			var updateTable_Form_ButtonAbbrechen = $('#updateItemTable_Form_ButtonAbbrechen');
			var itemName;
			
			//click at button "Bearbeiten"
			$('.editItem').click(function()
			{
				var parent = $(this).parent().parent();
				itemName = parent.children('.itemName').text().trim();
				itemName = itemName.replace(/\s+/, '');
				var itemValue = parent.children('.itemValue').text().trim();
				
				updateTable_Title.text(itemName);
				updateTable_Form_Value.val(itemValue);
				updateTable_Form_Name.val(itemName);
				
				if(typeof(itemName) == "undefined")
				{
					meldung.html("itemName ist nicht definiert.");
					return;
				}
				
				if(itemName == "Zahlungstype")
				{
					$('#updateZahlungstypeTable').slideDown(1000);
				}
				else
				{
					updateTable.slideDown(1000);
				}
				
				if(typeof(itemValue) === "undefined")
				{
					meldung.html("itemValue ist nicht definiert.");
					return;
				}
			});
			
		//close update pop-up
			updateTable_Form_ButtonAbbrechen.click(function(e)
			{
				e.preventDefault();
				
				updateTable.slideUp('slow');
			});
			
		//close Zahlungstype
			$('#updateZahlungstypeTable_Form_ButtonAbbrechen').click(function(e){
				e.preventDefault();
				$('#updateZahlungstypeTable').slideUp(1000);
			});
			
		//update Lehrer
			$('#updateLehrer').click(function(){
				meldung.text("click");
				$('#updateLehrerTable').slideDown(1000);
			});
			$('#closeUpdateLehrer').click(function(e)
			{
				e.preventDefault();
				$('#updateLehrerTable').slideUp(1000);
			});
		//update Beschreibung
			$('#updateBeschreibung').click(function()
			{
				parent = $(this).parent().parent();
				itemValue = parent.children('.itemValue').text().trim();
				
				$('#Beschreibung_Form_Value').val(itemValue);
				
				$('#updateBeschreibungTable').slideDown(1000);
			});
			$('#closeUpdateBeschreibung').click(function(e)
			{
				e.preventDefault();
				$('#updateBeschreibungTable').slideUp(1000);
			});
		//update Alter
			$('#updateAlter').click(function()
			{
				$('.decimalSpiner').spinner({
					min:0,
					max:100,
					start:0,
					step:1,
					numberFormat: "n"
				});
				
				$('#updateAlterTable').slideDown(1000);
			});
			$('#closeUpdateAlter').click(function(e)
			{
				e.preventDefault();
				$('#updateAlterTable').slideUp(1000);
				$(".decimalSpiner").spinner( "destroy" );
			});
		//update Klassen
			$('#updateKlassen').click(function()
			{
				$('.decimalSpiner').spinner({
					min:0,
					max:100,
					start:0,
					step:1,
					numberFormat: "n"
				});
				$('#updateKlassenTable').slideDown(1000);
			});
			$('#closeUpdateKlassen').click(function(e)
			{
				e.preventDefault();
				$('#updateKlassenTable').slideUp(1000);
				$(".decimalSpiner").spinner( "destroy" );
			});
//add new Termin
			function addNewTermin()
			{
				$('#addTermin').slideDown(1000);
			};
			
			$('#addTerminForm').submit(function (e){
				e.preventDefault();
				var postData = $(this).serializeArray();
				 
				$.ajax({
					url:'<?=BASIS_URL?>/admin/ajaxAddNewTermin',
					type:'POST',
					data:postData,
					dataType:'JSON',
					success:function(response){
						if(typeof response.fehler === 'undefined')
						{
							$("#addTerminForm [name]").css({'background':''});
							meldung.html(response.info);
							meldung.append("<br>");
							
							for(var i in response.data)
							{
								meldung.append(i+" = ");
								meldung.append(response.data[i]);
								meldung.append("<br>");
							}
							meldung.append("<br><br>");
							meldung.append(response);
							setTimeout(function () {
								location.reload();
							}, 2000);
						}
						else
						{
							meldung.html(response.fehler);
							$("#addTerminForm [name]").css({'background':''});
							for(i=0; i<response.fehlerInput.length; i++)
							{
								//$("form [name="+response.fehlerInput[i]+"]").css({'background':'red'});
								$("#addTerminForm [name="+response.fehlerInput[i]+"]").css({'background':'red'});
							}
							meldung.append(response.fehlerInput);
						}
					},
					error:function(errorThrown){
						meldung.html(errorThrown);
					}
				});
			});
			$('#closeAddTermin').click(function(e)
			{
				e.preventDefault();
				$('#addTermin').slideUp(1000);
			});
		//update Termin
			$('.editTermin').click(function(){
				$button = $(this);
				$('#updateTerminForm_season_id').val($button.attr('season_id'));
				$('#updateTerminForm_stnPlId').val($button.attr('stnPlId'));
				$('#updateTerminForm_Raum').val($button.attr('raum'));
				$('#updateTerminForm_Anfang').val($button.attr('anfang'));
				$('#updateTerminForm_Ende').val($button.attr('ende'));
				$('#updateTerminForm_Wochentag').val($button.attr('wochentag'));
				
				$('#updateTerminTable').slideDown(1000);
			});
			$('#closeUpdateTermin').click(function(e)
			{
				e.preventDefault();
				$('#updateTerminTable').slideUp(1000);
			});
			
			$('#updateTerminForm').submit(function (e){
				e.preventDefault();
				var postData = $(this).serializeArray();
				 
				$.ajax({
					url:'<?=BASIS_URL?>/admin/ajaxUpdateTermin',
					type:'POST',
					data:postData,
					dataType:'JSON',
					success:function(response){
						if(response.status === 'ok')
						{
							alert(response.status);
							location.reload();
						}
						else
						{
							alert("Fehler: "+response.status);
						}
					},
					error:function(errorThrown){
						meldung.html(errorThrown);
					}
				});
			});
			
			$('.deleteTermin').click(function(e){
				if(confirm('Wollen Sie wirklich diesen Termin löschen?')){
					stnId = $(this).attr('stnPlId');
					$.ajax({
						url:'<?=BASIS_URL?>/admin/ajaxDeleteTermin',
						type:'POST',
						data:{stnPlId:stnId},
						dataType:'JSON',
						success:function(response){
							if(response.status === 'ok')
							{
								alert(response.status);
								location.reload();
							}
							else
							{
								alert("Fehler: "+response.status);
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
			
			$('.deleteKurs').click(function(e){
				if(confirm('Wollen Sie wirklich diesen Termin löschen?')){
					krId = $(this).attr('kurId');
					
					$.ajax({
						url:'<?=BASIS_URL?>/admin/ajaxDeleteKurs',
						type:'POST',
						data:{kurId:krId},
						dataType:'JSON',
						success:function(response){
							if(response.status === 'ok')
							{
								alert(response.message);
								self.location = "/admin/kurseBearbeitenListe";
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
			
		//update isKurInactive
			$('#updateIsKurInactive').click(function(){
				$('#updateIsKurInactiveTable').slideDown(1000);
			});
			
			$('#closeUpdateIsKurActiveTable').click(function(e){
				e.preventDefault();
				$('#updateIsKurInactiveTable').slideUp(1000);
			});
		</script>
	</body>
</html>