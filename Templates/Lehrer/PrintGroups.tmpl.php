<?php
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
require_once BASIS_DIR.'/Tools/TmplTools.php';
use Tools\TmplTools as TmplTls;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>SWIFF: Print Gruppen für Lehrer.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style-print.css" type="text/css" media="print" />

		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>
		<style>
			.kundenResTbl{border:1px solid black;width:70%;margin-top:20px;}
		</style>
	</head>
	<body>
		<div id="horizontalMenu" >
			<?php

			TemplateTools\Menu::adminMenu();
			?>
		</div>
		<!-- START OF CONTENT -->
		<div id="mainContent">
			<div id="searchPanel" class="dont-print">
				<form id="searchFrom" method="GET">
					<table>
						<tr>
							<th>
								Saison
							</th>
							<th>
								Lehrer
							</th>
							<th>
								Tag
							</th>
							<td rowspan="2">
								<input class="search" type='submit' value='' style="padding: 10px 10px;">
							</td>
						</tr>
						<tr>
							<td>
								<?php echo TmplTls::getSeasonsSelector("s_season", "s_season", $sArr[':season'], "Season", 0); ?>
							</td>
							<td>
								<?php echo TmplTls::getLehrerSelector("s_lehrId", "s_lehrId", $sArr[':lehrId']); ?>
							</td>
							<td>
								<?php echo TmplTls::getWeekdaySelector("wochentag", "wochentag", $sArr[':wochentag']); ?>
							</td>
						</tr>
					</table>
				</form>
			</div>

			<div class="resultsDiv">
				<?php
				if(!empty($res)){
					$curKurId = $res[0]['kurId'];
					$curDay = $res[0]['wochentag'];
					$curAnfang = $res[0]['anfang'];
					$curEnde = $res[0]['ende'];
					$curKurName = $res[0]['kurName'];
					$peopleCounter = 0;
					$curLehrId = $res[0]['lehrId'];
					$curLehrer = $res[0]['lVorname']." ".$res[0]['lName'];
				//start table
					?>
					<table class="kundenResTbl">
						<tr><th colspan="5"><?= Fltr::indxToWeekday($curDay)?> - <?=date('H:i', strtotime($curAnfang) )?> - <?=$curKurName?> - <?=$curLehrer?></th></tr>
						<tr>
							<th>#</th><th>Anrede</th><th>Vorname</th><th>Name</th><th>Alter</th>
						</tr>
					<?php
					foreach($res as $r){
						if($curKurId !== $r['kurId']){
							$peopleCounter = 0;
							$curKurId = $r['kurId'];
							$curKurName = $r['kurName'];
							$curDay = $r['wochentag'];
							$curAnfang = $r['anfang'];
							$curLehrer = $curLehrId === $r['lehrId'] ? $curLehrer : $r['lVorname']." ".$r['lName'] ;
							?>
							</table>
							<table class="kundenResTbl">
								<tr><th colspan="5"><?=Fltr::indxToWeekday($curDay)?> - <?=date('H:i', strtotime($curAnfang) )?> - <?=$curKurName?> - <?=$curLehrer?></th></tr>
								<tr>
									<th>#</th><th>Anrede</th><th>Vorname</th><th>Name</th><th>Alter</th>
								</tr>
							<?php
						}
						++$peopleCounter;
						?>
								<tr>
									<td><?=$peopleCounter?></td><td><?=$r['anrede']?></td><td><?=$r['vorname']?></td><td><?=$r['name']?></td><td><?=$r['alter']?></td>
								</tr>
						<?php
					}
					echo "</table>"; //end table
				}
				//phpinfo();
				?>
			</div>
		</div>
	</body>
</html>
