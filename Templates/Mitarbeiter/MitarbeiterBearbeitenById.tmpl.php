<!DOCTYPE html>
<html>
	<head>
		<title>SWIFF: bearbeiten Mitarbeiter bei id.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />
		
		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>
		
		<script src="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.css">
		
		<style>
			#updateItemTable
			{
				display:none;
				border:2px solid #a1a1a1;
				padding:20px; 
				background:#dddddd;
				width:400px;
				border-radius:20px;

				position:fixed;
				top:250px;
				left:30%;
				z-index:100;
			}
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

			<table>
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
						Eingestellt am
					</th>
					<td class="itemValue">
						<?=$res['eingestelltAm']?>
					</td>
					<td>
						<button type="button" class="editItem">Bearbeiten</button>
					</td>
				</tr>
				<tr>
					<th class="itemName">
						Entlassen am
					</th>
					<td class="itemValue">
						<?=$res['entlassenAm']?>
					</td>
					<td>
						<button type="button" class="editItem">Bearbeiten</button>
					</td>
				</tr>
			</table>
		</div>
		<!-- END OF CONTENT -->
		<div id="updateItemTable">
			<div id="updateItemTable_Title"></div>
			<div>
				<form id="upateItemTable_Form" method="post" action="<?=$_SERVER['REQUEST_URI']?>">
					<input type="hidden" id="updateItemTable_Form_Name" name="updateItemTable_Form_Name" value="hiddenValue"/>
					<input type="text" id="updateItemTable_Form_Value" name="updateItemTable_Form_Value"/>
					<div>
						<input id="updateItemTable_Form_ButtonSpeichern" type='submit' value='Speichern' />
						<button id="updateItemTable_Form_ButtonAbbrechen" >Abbrechen</button>
					</div>
				</form>
			</div>
		</div>
		<!--JavaScript -->
		<script>
			var meldung = $('#meldung');
			var updateTable = $('#updateItemTable');
			var updateTable_Title = $('#updateItemTable_Title');
			var upateTable_Form = $('#upateItemTable_Form');
			var updateTable_Form_Name = $('#updateItemTable_Form_Name');
			var updateTable_Form_Value = $('#updateItemTable_Form_Value');
			var updateTable_Form_ButtonAbbrechen = $('#updateItemTable_Form_ButtonAbbrechen');
			
			//$("form [name=vorname]").css({'background':'red'});
			//$('form').attr("name", "plz").css({'background':'red'});
			var itemName;
			//click at button "Bearbeiten"
			$('.editItem').click(function()
			{
				meldung.html("Bearbeiten ist clicked.");
				updateTable.slideDown(1000);
				var parent = $(this).parent().parent();
				itemName = parent.children('.itemName').text().trim();
				itemName = itemName.replace(/\s+/, '');
				var itemValue = parent.children('.itemValue').text().trim();
				
				if(typeof(itemName) === "undefined")
				{
					meldung.html("itemName ist nicht definiert.");
					return;
				}
				
				if(itemName === "Anrede")
				{
					
				updateTable_Form_Value.replaceWith(
						"<select name='updateItemTable_Form_Value'>"+
							"<option value='Frau'>Frau</option>"+
							"<option value='Herr'>Herr</option>"+
						"</select>");
				}
				
				if(itemName === "Geburtsdatum(dd.mm.yyyy)" ||
					itemName === "Eingestelltam" ||
					itemName === "Entlassenam")
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
				}
				
				if(typeof(itemValue) === "undefined")
				{
					meldung.html("itemValue ist nicht definiert.");
					return;
				}
				
				updateTable_Title.text(itemName);
				updateTable_Form_Value.val(itemValue);
				updateTable_Form_Name.val(itemName);
				
				meldung.append("<br>"+itemName);
				meldung.append("<br>"+itemValue);
			});
			
			//close update pop-up
			updateTable_Form_ButtonAbbrechen.click(function(e){
				e.preventDefault();
				meldung.append("<br> 'Abbrechen' is clicked.");
				updateTable.slideUp('slow');
				
				if(itemName === "Geburtsdatum(dd.mm.yyyy)" ||
					itemName === "Eingestelltam" ||
					itemName === "Entlassenam")
				{
					updateTable_Form_Value.datepicker('destroy');
				}
			});
			/*
			$('form').submit(function (e){
				e.preventDefault();
				var postData = $(this).serializeArray();
				
				$.ajax({
					url:'<?BASIS_URL?>/admin/ajaxSaveNewKunde',
					type:'POST',
					data:postData,
					dataType:'JSON',
					success:function(response){
						if(typeof response.fehler === 'undefined')
						{
							$("form [name]").css({'background':''});
							meldung.html(response.info);
							meldung.append("<br>");
							for(var i in response.data)
							{
								meldung.append(i+" = ");
								meldung.append(response.data[i]);
								meldung.append("<br>");
							}
							
						}
						else
						{
							meldung.html(response.fehler);
							$("form [name]").css({'background':''});
							for(i=0; i<response.fehlerInput.length; i++)
							{
								$("form [name="+response.fehlerInput[i]+"]").css({'background':'red'});
							}
						}
					},
					error:function(errorThrown){
						meldung.html(errorThrown);
					}
				});
			});
			*/
		</script>
	</body>
</html>
