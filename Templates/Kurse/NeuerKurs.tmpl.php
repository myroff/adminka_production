<?php
require_once BASIS_DIR.'/Tools/TmplTools.php';
use Tools\TmplTools as TmplTls;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>SWIFF: Neuer Kurs.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />

		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>

		<script src="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.css">

		<style>
			.decimalSpiner{width:30px;};
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
			<h2>Neuer Kurs</h2>
			<div id="meldung">
				<?php
				if(isset($meldung))
				{
					echo $meldung;
				}
				?>
			</div>
			<form method="post">
				<table id="inputTable">
					<tr>
						<th>
							Kursname
						</th>
						<td>
							<input name='kurName' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							Max. Teilnehmern<br>in Gruppe
						</th>
						<td>
							<input name='maxKnd' type='text'/>
						</td>
					</tr>
					<tr>
						<th>
							Lehrer
						</th>
						<td>
							<?php echo TmplTls::getLehrerSelector("lehrId", "lehrId"); ?>
						</td>
					</tr>
					<tr>
						<th>
							Beschreibung
						</th>
						<td>
							<textarea name='kurBeschreibung' rows="4" ></textarea>
						</td>
					</tr>
					<tr>
						<th>
							Preis in Euro
						</th>
						<td>
							<input name='kurPreis' type='text' />
						</td>
					</tr>
					<tr>
						<th>
							Zahlungstyp
						</th>
						<td>
							<select name='kurIsStdPreis' >
								<option value="proMonat">pro Monat</option>
								<option value="proStunde">pro Stunde</option>
							</select>
						</td>
					</tr>
					<tr>
						<th >
							Altersgruppen
						</th>
					</tr>

					<tr>
						<td colspan="2">
							<b>Jahren</b>
						</td>
					</tr>
					<tr>
						<td>
							Jungste
						</td>
						<td>
							Älteste
						</td>
					</tr>
					<tr>
						<td>
							<input name="kurMinAlter" type="text" class="decimalSpiner" />
							Jahren
						</td>
						<td>
							<input name="kurMaxAlter" type="text" class="decimalSpiner" />
							Jahren
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<b>Klassen</b>
						</td>
					</tr>
					<tr>
						<td>
							Jungsten
						</td>
						<td>
							Ältesten
						</td>
					</tr>
					<tr>
						<td>
							<input name="kurMinKlasse" type="text" class="decimalSpiner" />
							Klasse
						</td>
						<td>
							<input name="kurMaxKlasse" type="text" class="decimalSpiner" />
							Klasse
						</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center;">
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

			$('.decimalSpiner').spinner({
				min:0,
				max:100,
				start:0,
				step:1,
				numberFormat: "n"
			});

			$('form').submit(function (e){
				e.preventDefault();
				var postData = $(this).serializeArray();

				meldung.text(postData);

				$.ajax({
					url:'<?=BASIS_URL?>/admin/ajaxSaveNewKurs',
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
