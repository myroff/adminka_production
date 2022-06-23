<!DOCTYPE html>
<html>
	<head>
		<title>SWIFF: Mitarbeiter.</title>
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
				<form method="POST">
					<table>
						<tr>
							<th>
								Vorname
							</th>
							<th>
								Name
							</th>
							<td rowspan="2">
								<input class="search" type='submit' value='Suchen' >
							</td>
						</tr>
						<tr>
							<td>
								<input name="vorname" type="text" value="<?=$sArr[':vorname']?>" />
							</td>
							<td>
								<input name="name" type="text" value="<?=$sArr[':name']?>" />
							</td>
						</tr>
					</table>
				</form>
			</div>

			<div id="resultsDiv">
				<table id="kundenResTbl">
					<tr>
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
							Geburtsdatum
						</th>
						<th>
							Strasse
						</th>
						<th>
							Haus
						</th>
						<th>
							PLZ
						</th>
						<th>
							Stadt
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
						<td colspan="11">Nach Ihrer Anfrage wurden keine Daten gefunden.</td>
					</tr>
				<?php
				}
				else
				{
					foreach ($res as $r)
					{
						echo "<tr>";

						echo "<td>".$r['anrede']."</td>";
						echo "<td>".$r['vorname']."</td>";
						echo "<td>".$r['name']."</td>";
						echo "<td>".$r['geburtsdatum']."</td>";
						echo "<td>".$r['strasse']."</td>";
						echo "<td>".$r['strNr']."</td>";
						echo "<td>".$r['plz']."</td>";
						echo "<td>".$r['stadt']."</td>";
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
			//$("form [name=vorname]").css({'background':'red'});
			//$('form').attr("name", "plz").css({'background':'red'});


		</script>
	</body>
</html>
