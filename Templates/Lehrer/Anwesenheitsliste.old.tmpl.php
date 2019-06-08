<?php
require_once BASIS_DIR.'/Tools/TmplTools.php';
use Tools\TmplTools as TmplTls;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
require_once BASIS_DIR.'/Tools/DateTools.php';
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
			}
			@media print {
				html, body {
					width: 210mm;
					height: 297mm;
				}
			}
			
			table.Anwesenheitsliste{table-layout:fixed;/*width:100%;*/}
			table.Anwesenheitsliste col{overflow:hidden;}
			
			table.resultsDiv td.dates{width:20px;font-size:8px;transform: rotate(-90deg);overflow:hidden;}
			table.resultsDiv td.nr{width:10px;}
			table.resultsDiv td{padding:1px;border:1px solid black;height:18px;}
			
			.fach{font-size:12px;}
			
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
			<div id="searchPanel" class="dont-print">
				<form id="searchFrom" method="GET">
					<table>
						<tr>
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
								<?php TmplTls::getLehrerSelector("s_lehrId", "s_lehrId", $sArr[':lehrId']); ?>
							</td>
							<td>
								<input type="text" id="month" name="month" class="zebra_datepicker_my"/>
							</td>
						</tr>
					</table>
				</form>
			</div>
			
			<div >
				<?php
				if(!empty($res) AND isset($res)){
					foreach($res as $r){
						$z = 0;
						$days_arr = explode(',', $r['days']);
						$str_days = "";
						foreach ($days_arr as $d) {
							$str_days .= Fltr::indxToWeekday($d).", ";
						}
						$str_days = substr($str_days, 0, -2);
						
						$plan = DateTools::getDatesOfWeekdaysInMonthYear($days_arr, '%a.<br>%d.%m.%y');
					?>
						<table class='resultsDiv Anwesenheitsliste'>
							<col width="20"/><col width="25"/><col width="150"/><col width="150"/>
							<?php
								for($i=0; $i<count($plan); $i++){
									echo "<col width=\"20\"/>";
								}
							?>
							<tr>
								<td colspan="4" class="fach"><?=$r['vorname']?> <?=$r['name']?> : <?=$r['kurName']?> [<?=$str_days?>]</td>
								<?php
								foreach ($plan as $date){
									echo "<td class='dates' rowspan='2'>$date</td>";
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
									<td><?=$z?></td><td><?=$n['kndId']?></td><td><?=$n['vorname']?></td><td><?=$n['name']?></td>
							<?php
								for($i=0; $i<count($plan); $i++){
									echo "<td ></td>";
								}
							?>
								</tr>
							<?php
							}
							for(; $z<30; ++$z){
								echo "<tr>";
								for($n=0; $n<count($plan)+4; $n++){
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
		format: 'm',		//  note that becase there's no day in the format
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