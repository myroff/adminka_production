<!DOCTYPE html>
<html>
	<head>
		<title>SWIFF. Neuer Kunde.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />

		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>

		<script src="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.css">

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
			<form method="post" id="saveNewClientForm" action="<?=BASIS_URL?>/admin/saveNewKunde">
				<table>
					<!--<tr>
						<th>
							Eltern
						</th>
						<td>

						</td>
					</tr>-->
					<tr>
						<th>
							Kunden-Nummer
						</th>
						<td>
							<input name='kundenNummer' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							Anrede
						</th>
						<td>
							<select name='anrede'>
								<option value="Frau">Frau</option>
								<option value="Herr">Herr</option>
							</select>
						</td>
					</tr>
					<tr>
						<th>
							Vorname
						</th>
						<td>
							<input name='vorname' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							Name
						</th>
						<td>
							<input name='name' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							Geburtsdatum<br>
							(dd.mm.yyyy)
						</th>
						<td>
							<input class="zebra_datepicker" name='geburtsdatum' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							Telefon
						</th>
						<td>
							<input name='telefon' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							Handy
						</th>
						<td>
							<input name='handy' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							Email
						</th>
						<td>
							<input name='email' type='email'/>
						</td>
					</tr>
					<tr>
						<th>
							Strasse
						</th>
						<td>
							<input name='strasse' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							Haus
						</th>
						<td>
							<input name='haus' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							PLZ
						</th>
						<td>
							<input name='plz' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							Stadt
						</th>
						<td>
							<input name='stadt' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							ist Foto<br>Erlaubt?
						</th>
						<td>
							<select name="istFotoErlaubt">
								<option value="ja">Ja</option>
								<option value="nein">Nein</option>
							</select>
						</td>
					</tr>
					<tr>
						<th>
							Muttersprache
						</th>
						<td>
							<input name='muttersprache' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							Geburtsland
						</th>
						<td>
							<input name='geburtsland' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							Empfohlen durch
						</th>
						<td>
							<?php
							require_once BASIS_DIR.'/BLogic/Kunde/Empfohlen.php';
							use Kunde\Empfohlen as Empf;
							Empf::setButton("empfohlenId");
							?>
						</td>
					</tr>
					<tr>
						<th colspan="2">Kontodaten</th>
					</tr>
					<tr>
						<th>
							Bezahlen mit
						</th>
						<td>
							<select name='zahlenMit'>
								<option value="bar">Bar</option>
								<option value="lastschrift">Lastschrift</option>
								<option value="bamf">BAMF</option>
								<option value="zuzahler">Zuzahler</option>
								<option value="selbstzahler">Selbstzahler</option>
							</select>
						</td>
					</tr>
					<tr>
						<th>
							Kontoinhaber
						</th>
						<td>
							<input name='kontoinhaber' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							Strasse
						</th>
						<td>
							<input name='zdStrasse' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							Hausnummer
						</th>
						<td>
							<input name='zdHausnummer' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							PLZ
						</th>
						<td>
							<input name='zdPlz' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							Ort
						</th>
						<td>
							<input name='zdOrt' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							Bankname
						</th>
						<td>
							<input name='zdBankname' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							IBAN
						</th>
						<td>
							<input name='zdIban' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							BIC
						</th>
						<td>
							<input name='zdBic' type='text'/>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align: center;">
							<input type='submit' value='Speichern'>
						</td>
					</tr>
				</table>
			</form>
		</div>
		<!-- END OF CONTENT -->
		<div id="messageBox">
			<div id="messageBox_message"></div>
			<div class="buttonsOkCancelDiv">
				<button class="cancel" id="closeMessageBox"></button>
			</div>
		</div>
		<!--JavaScript -->
		<script>
		    var meldung = $('#meldung');
		    //$("form [name=vorname]").css({'background':'red'});
		    //$('form').attr("name", "plz").css({'background':'red'});

			$(".zebra_datepicker").Zebra_DatePicker({
				format: 'd.m.Y',	//  note that becase there's no day in the format
				offset:	[0,200],			//  users will not be able to select a day!
			});

		    $('#saveNewClientForm').submit(function (e){
				e.preventDefault();
				var postData = $(this).serializeArray();

				$.ajax({
					url:'<?=BASIS_URL?>/admin/ajaxSaveNewKunde',
					type:'POST',
					data:postData,
					dataType:'JSON',
					success:function(response){
						if(typeof response.fehler === 'undefined')
						{
							$("form [name]").css({'background':''});
							meldung.html(response.info);
							/*
							meldung.append("<br>");
							for(var i in response.data)
							{
							meldung.append(i+" = ");
							meldung.append(response.data[i]);
							meldung.append("<br>");
							}
							*/
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
		</script>
	</body>
</html>
