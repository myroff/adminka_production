<html>
	<head>
		<title>SWIFF: Statistik-Buchhaltung.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style-print.css" type="text/css" media="print" />

		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>

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
			<h3>Zahlungseingang</h3>
			<table class="standardTable">
				<tr>
					<th>Monat</th><th>Bar</th><th>Lastschrift</th><th>Summe</th><th>Без скидок</th><th>Разница</th>
				</tr>
			<?php
				foreach($rres as $r)
				{
					$m = strtotime($r['rnMonat']);
					echo "<tr>";
					echo "<td>".date('m.Y', $m)."</td>";
					echo "<td>".$r['barSumme']." €</td>";
					echo "<td>".$r['lastschriftSumme']." €</td>";
					echo "<td>".$r['summe']." €</td>";
					echo "<td>".$r['summeKurPreis']." €</td>";
					echo "<td>".($r['summe'] - $r['summeKurPreis'])." €</td>";
					echo "</tr>";
				}
			?>
			</table>
		</div><!-- Main Content Ende -->
	</body>
</html>