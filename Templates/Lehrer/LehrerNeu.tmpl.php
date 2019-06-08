<!DOCTYPE html>
<html>
	<head>
		<title>SWIFF: Neuer Lehrer.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />
		
		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>

		<script src="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.css">
		
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
			<form method="post" action="<?=BASIS_URL?>/admin/saveNewLehrer">
				<table>
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
							<input class="datePicker" name='geburtsdatum' type='text'/>
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
							Stadt
						</th>
						<td>
							<input name='stadt' type='text'/>
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
							Fach
						</th>
						<td>
							<input name='fach' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							Eingestellt am
						</th>
						<td>
							<input class="datePicker" name='eingestelltAm' type='text' value="<?=date('d.m.Y')?>"/>
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
		
		<!--JavaScript -->
		<script>
			var meldung = $('#meldung');
			//$("form [name=vorname]").css({'background':'red'});
			//$('form').attr("name", "plz").css({'background':'red'});
			
			$('.datePicker').datepicker({
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
			
			$('form').submit(function (e){
				e.preventDefault();
				var postData = $(this).serializeArray();
				
				$.ajax({
					url:'<?=BASIS_URL?>/admin/ajaxSaveNewLehrer',
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
							meldung.append("<br><br>");
							meldung.append(response);
						}
						else
						{
							meldung.html(response.fehler);
							$("form [name]").css({'background':''});
							for(i=0; i<response.fehlerInput.length; i++)
							{
								$("form [name="+response.fehlerInput[i]+"]").css({'background':'red'});
							}
							meldung.append(response.fehlerInput);
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
