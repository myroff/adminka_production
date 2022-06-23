<html>
	<head>
		<title>SWIFF: PayDay.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style-print.css" type="text/css" media="print" />

		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>

		<script src="<?=BASIS_URL?>/Public/js/zebra_datepicker.js"></script>
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/zebra_default.css">

		<style>
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
			<div id="search-panel">
				<form method="GET" action="" style="">
					<table>
						<tr>
							<th>Zahlungstag</th>

							<td rowspan="2"><input class="search" type="submit" value="" /></td>
						</tr>
						<tr>
							<td><input class="zebra_datepicker" name='date' type='text' value="<?=$date?>"/></td>
						</tr>
					</table>
				</form>

				<form method="GET" action="" >
					<table>
						<tr>
							<th>Zahlungsmonat</th>

							<td rowspan="2"><input class="search" type="submit" value="" /></td>
						</tr>
						<tr>
							<td><input class="zebra_datepicker_my" name='month' type='text' value="<?=$month?>"/></td>
						</tr>
					</table>
				</form>
			</div>

			<table class="standardTable">
				<tr>
					<th>Kunde</th><th>Summe</th><th>Zahlungsart</th><th>Am Tag</th><th>Für Monat</th><th>An Mitarbeiter</th><th>Kommentar</th>
				</tr>
			<?php
				$sum = 0;
				if(!empty($res))
				{
					foreach($res as $r)
					{
						$m = strtotime($r['rnMonat']);
						$d = strtotime($r['rnErstelltAm']);
						echo "<tr style='".($r['payment_id'] === "1" ? "Bar" : "background:rgba(255,255,0,0.7);")."' >";

						echo "<td>".$r['kndName'].", ".$r['kndVorname']."</td>";
						echo "<td>".$r['summe']." €</td>";
					//Zahlungsart
						echo "<td>";
						echo $r['payment_id'] === "1" ? "Bar" : "Lastschrift";
						echo "</td>";

						echo "<td>".date('H:i [d.m.Y]', $d)."</td>";
						echo "<td>".date('m.Y', $m)."</td>";
						echo "<td>".$r['mtbVorname']." ".$r['mtbName']."</td>";
						echo "<td>".$r['rnKomm']."</td>";
						echo "</tr>";

						$sum += (float) $r['summe'];
					}
				}
			?>
				<tr><td>Summe:</td><td colspan="6"><?=$sum?> €</td></tr>
			</table>
		</div><!-- Main Content Ende -->

		<script>
			$(".zebra_datepicker").Zebra_DatePicker({
				format: 'd.m.Y',	//  note that becase there's no day in the format
				offset:	[10,350],			//  users will not be able to select a day!
			});

		//Monatspicker
			$(".zebra_datepicker_my").Zebra_DatePicker({
				format: 'm.Y',		//  note that becase there's no day in the format
				offset:	[10,350],	//  users will not be able to select a day!
			});
		</script>
	</body>
</html>