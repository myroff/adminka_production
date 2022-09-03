<?php
use Tools\TmplTools as TmplTls;
use Tools\Filter as Fltr;
use Tools\DateTools as DateTools;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>SWIFF: Anwesenheitsliste.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style-print.css" type="text/css" media="print" />

		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>

		<script src="<?=BASIS_URL?>/Public/js/zebra_datepicker.js"></script>
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/zebra_default.css">

		<style>
			@page {
				size: A4;
				margin: 10px;
				margin-top:120px;
				padding-left: 100px;
			}
			@media print {
				html, body {
					height: 210mm;
					width: 297mm;
				}
				#content-container{margin-left:150px;}
			}

			table.Anwesenheitsliste{table-layout:fixed;font: 25px Arial sans-serif;/*width:100%;*/}
			table.Anwesenheitsliste col{overflow:hidden;}
			table.Anwesenheitsliste tr{height:30px;}
			table.resultsDiv td{padding:2px 5px;border:1px solid black;height:18px;}

			.fach{font-size:18px;font-weight:bold;}
			.nr{width:30px;}
			.kndNr{width:60px;}
			.vorname, .nachname{width:300px;}
			.dates{width:40px;}
			.name_val{font-size:28px;}
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
								Monat
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
								<input type="text" id="month" name="month" class="zebra_datepicker_my" value="<?= $sArr[':month'] ?>"/>
							</td>
						</tr>
					</table>
				</form>
			</div>

			<div id="content-container">
				<?php //var_dump($res);die;
				if(!empty($res) AND isset($res)){
					foreach($res as $r){
						$z = 0; //children pro list
						$days_in_list = 8;

						$days_arr = [];
						$termine = "";

						foreach ($r['termine'] as $termin) {

							$days_arr[] = $termin['wochentag'];
							$termine .= Fltr::indxToWeekday($termin['wochentag']);
							$termine .= " ". Fltr::sqlTimeToStr($termin['anfang']);
							$termine .= " - ". Fltr::sqlTimeToStr($termin['ende']);
							$termine .= ", ";
						}
						$termine = rtrim($termine, ', ');

						$plan = DateTools::getDatesOfWeekdaysInMonthYear($days_arr, '%a.<br>%d.%m.%y');
					?>
						<table class='resultsDiv Anwesenheitsliste'>
							<tr>
								<td colspan="4" class="fach"><?=$r['vorname']?> <?=$r['name']?> : <?=$r['kurName']?> [<?=$termine?>]</td>
								<?php
								for($d=0; $d<$days_in_list; $d++){
									echo "<td class='dates' rowspan='2'></td>";
								}
							?>
							</tr>
							<tr>
								<td class="nr">#</td><td class="kndNr">Nr.</td><td class="vorname">Vorname</td><td class="nachname">Nachname</td>
							</tr>
							<?php
							foreach($r['kids'] as $n){
								$z++;
							?>
								<tr>
									<td><?=$z?></td><td><?=$n['kundenNummer']?></td><td class="name_val"><?=$n['vorname']?></td><td class="name_val"><?=$n['name']?></td>
							<?php
								for($d=0; $d<$days_in_list; $d++){
									echo "<td ></td>";
								}
							?>
								</tr>
							<?php
							}
							/*placeholder for new children*/
							for(; $z<12; ++$z){
								echo "<tr>";
								for($d=0; $d<$days_in_list+4; $d++){
									echo "<td></td>";
								}
								echo "</tr>";
							}
							?>
						</table>
					<?php
					}
				}
				?>
			</div>
		</div>
		<!-- END OF CONTENT -->
<!--JavaScript -->
<script>
//Monatspicker
	$(".zebra_datepicker_my").Zebra_DatePicker({
		format: 'Ym',		//  note that becase there's no day in the format
		offset:	[0,200],	//  users will not be able to select a day!
	});
	/*
	$('#markAlleKurse').click(function(){
		check = $('#markAlleKurse').is(':checked');
		$('.markedKurs').prop('checked', check);
	});
	*/
</script>
	</body>
</html>