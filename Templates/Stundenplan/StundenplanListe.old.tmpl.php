<?php
require_once BASIS_DIR.'/Tools/TmplTools.php';
use Tools\TmplTools as TmplTls;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
use DateTime as DateTime;
use DateInterval as DateInterval;
?>
<html>
	<head>
		<title>SWIFF: Stundenplan.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />
		
		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>
		
		<script src="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.css">
		
		<style>
			#editTermin
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
			
			#stnPlFlex
			{
				width:1200px;
				overflow:hidden;
			}
			#stnPlFlex .headerRaum{width:120px;background:#292929;color:white;font-weight:bold;float:left;}
			#stnPlFlex .headerWeekday{background:#575757;color:white;font-weight:bold;clear:both;}
			.timeLine{width:120px;float:left;height:300px;}
			.time{height:180px;border-top:1px solid black;border-bottom:1px solid black;z-index:0;}
			.lessons{width:118px;height:178px;float:left;position:absolute;font:12px arial, sans-serif;z-index:10;border:1px solid black;}
			
			#stnPlTable td, stnPlTable th{padding:0px;text-align:left;vertical-align:top;width:120px;}
			
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
<!-- search-panel -->
			<form method="GET" action="">
				<table>
					<tr>
						<th>Tag</th>
						<th>Lehrer</th>
						<th>Raum</th>
						<th>Kurs</th>
						<td rowspan="2"><input class="search" type="submit" value="" /></td>
					</tr>
					<tr>
						<td><?php TmplTls::getWeekdaySelector("search_wochentag", "search_wochentag", $sArr[':wochentag']); ?></td>
						<td><?php TmplTls::getLehrerSelector("search_lehrId", "search_lehrId", $sArr[':lehrId']); ?></td>
						<td><?php TmplTls::getRaumSelector("search_raum", "search_raum", $sArr[':raum']); ?></td>
						<td><?php TmplTls::getKursSelector("search_kurs", "search_kurs", $sArr[':kurName']); ?></td>
					</tr>
				</table>
			</form>
<!-- flexibler Stundenplan -->
				<?php
				$kAnfHour = new DateTime($res[0]['anfang']);
				
				$curTime = "";
				$endTime = "";
				$curDay = 0;
				$maxRaum = 10;
				
				$oneLesson = DateInterval::createFromDateString('45 min'); // 1 Lesson = 45 minut
				$oneHour = DateInterval::createFromDateString('1 hour'); // 1 hour
				$endOfHour = new DateTime('10:00');
				
				$startTime = new DateTime('09:00');
				$endTime = new DateTime('20:00');
				
				$countRes = count($res);
				?>
				<table id="stnPlTable" >
					<tr style="background:#292929;color:white;">
						<th>
							Zeit/Raum
						</th>
						<?php
							for($i=2; $i <= $maxRaum; $i++)
							{
								echo "<th>$i Raum</th>";
							}
						?>
					</tr>
				<?php
				$i = 0;
				while( $i < $countRes )
				{
					
					if( $curDay !== $res[$i]['wochentag'])
					{
						echo "<tr style='background:#575757;color:white;font-weight:bold;' ><td colspan='$maxRaum'>"
							.Fltr::indxToWeekday($res[$i]['wochentag'])."</td></tr>";
						$curDay = (int)$res[$i]['wochentag'];
						
					}
					
					for( $ct = $startTime, $et = $endOfHour; $ct <= $endTime && $i < $countRes; $ct->add($oneHour), $et->add($oneHour))
					{
						echo "<tr class='time' >";
						echo "<td >".$ct->format('H:i')."</td>";
						
						for($n=2; $n<=$maxRaum; $n++)
						{
							
							if( isset($res[$i]) )
							{
								if($n === (int)$res[$i]['raum'] AND $curDay === $res[$i]['wochentag'])
									//AND $kAnfHour >= $ct AND $kAnfHour < $et)
								{
									$kAnfHour = new DateTime($res[$i]['anfang']);
									$top = $ct->diff($kAnfHour)->i * 3;
									$tl = (int)$res[$i]['kurLength'] * 3; //Unterrichtsdauer
									echo "<td>";

									echo "<div class='lessons' style='background:rgba(0,250,0,0.7);margin-top:".$top."px;height:".$tl."px;' >";
										echo "<button class='editItemButton editTerminButton' stnPlId='".$res[$i]['stnPlId']."' anf='".$res[$i]['anfang']."'" 
												." end='".$res[$i]['ende']."' raum='".$res[$i]['raum']."' wTag='".$res[$i]['wochentag']."' >"
												."</button>";

										echo "kurLength=".$res[$i]['kurLength']."<br>";
										echo "anfang=".$res[$i]['anfang']."<br>";
										echo "ende=".$res[$i]['ende']."<br>";

										echo $res[$i]['kurName']."<br>";
										echo $res[$i]['vorname']." ".$res[$i]['name'];

									echo "</div>";

									echo "</td>";

									$i++;
									//$kAnfHour = isset($res[$i]) ? new DateTime($res[$i]['anfang']) : NULL;
								}
								else{
									echo "<td></td>";
								}
							}else{
								echo "<td></td>";
							}
						}
						
						echo "</tr>";
					}
				}
				echo "</table>";
			?>
<!-- end stnPlFlex -->

			<div><!-- Liste -->
				<table id="stnPlListe">
					<tr>
						<th>Info</th>
						<th>Tag</th>
						<th>Zeit</th>
						<th>Raum</th>
						<th>Kurs</th>
						<th>Lehrer</th>
						<th>Alter</th>
						<th>Klasse</th>
						<th>Preis</th>
						<th>Besetzt</th>
					</tr>
					<?php
					foreach($res as $r)
					{
						$alter = $r['kurMinAlter'];
						$alter .= $r['kurMinAlter'] < $r['kurMaxAlter'] ? " bis ".$r['kurMaxAlter'] : '';
						
						$klasse = $r['kurMinKlasse'];
						$klasse .= $r['kurMinKlasse'] < $r['kurMaxKlasse'] ? " bis ".$r['kurMaxKlasse'] : "";
					?>
					<tr>
						<td><a href="<?=BASIS_URL?>/admin/kursInfo/<?=$r['kurId']?>">Info</a></td>
						<td><?=Fltr::indxToWeekday($r['wochentag'])?></td>
						<td><?=$r['anfang']?> - <?=$r['ende']?></td>
						<td><?=$r['raum']?></td>
						<td><?=$r['kurName']?></td>
						<td><?=$r['vorname']?> <?=$r['name']?></td>
						<td><?=$alter?></td>
						<td><?=$klasse?></td>
						<td><?=$r['kurPreis']?></td>
						<td><?=$r['countKnd']?>/<?=$r['maxKnd']?></td>
					</tr>
					<?php
					}
					?>
				</table>
			</div>
			
		</div><!-- Main Content Ende -->
		<!-- Form für neuen Termin -->
		<div id="editTermin">
			<button class="deleteButton" id="deleteButton_Termin" stnPlId="" ></button>
			<form id="editTerminForm" method="post" action="<?=$_SERVER['REQUEST_URI']?>">
				<input type="hidden" name="stnPlId" value="" id="editTerminForm_stnPlId"/>
				<table>
					<tr>
						<th>
							Raum
						</th>
						<td>
							<input name="raum" type="text" id="editTerminForm_raum"/>
						</td>
					</tr>
					<tr>
						<th>
							Tag
						</th>
						<td>
							<?php TmplTls::getWeekdaySelector("wochentag", "editTerminForm_wochentag"); ?>
						</td>
					</tr>
					<tr>
						<th>
							Zeit<br>(format: hh:mm)
						</th>
						<td>
							von <input name="anfang" type="time" class='timepicker'id="editTerminForm_anfang" />
							bis <input name="ende" type="time" class='timepicker' id="editTerminForm_ende" />
						</td>
					</tr>
					<tr>
						<td colspan="2" class="buttonsOkCancelDiv">
							<input type='submit' value='' class="submit" />
							<button id="closeEditTermin" class="cancel" >&nbsp;</button>
						</td>
					</tr>
				</table>
			</form>
		</div>

<script>
//edit Termin
$('.editTerminButton').click(function(){
	$('#deleteButton_Termin').attr('stnPlId', $(this).attr('stnPlId'));
	
	$('#editTerminForm_stnPlId').val($(this).attr('stnPlId'));
	$('#editTerminForm_raum').val($(this).attr('raum'));
	$('#editTerminForm_wochentag').val($(this).attr('wTag'));
	$('#editTerminForm_anfang').val($(this).attr('anf'));
	$('#editTerminForm_ende').val($(this).attr('end'));
	
	$('#editTermin').slideDown(1000);
});

$('#closeEditTermin').click(function(e){
	e.preventDefault();
	$('#editTermin').slideUp(1000);
});

$('#editTerminForm').submit(function (e){
	e.preventDefault();
	var postData = $(this).serializeArray();

	$.ajax({
		url:'<?=BASIS_URL?>/admin/ajaxUpdateTermin',
		type:'POST',
		data:postData,
		dataType:'JSON',
		success:function(response){
			if(response.status === 'ok')
			{
				alert(response.status);
				location.reload();
			}
			else
			{
				alert("Fehler: "+response.status);
			}
		},
		error:function(errorThrown){
			meldung.html(errorThrown);
		}
	});
});

//delete Termin
$('#deleteButton_Termin').click(function(){
	if(confirm('Wollen Sie wirklich diesen Termin löschen?')){
		stnId = $(this).attr('stnPlId');
		$.ajax({
			url:'<?=BASIS_URL?>/admin/ajaxDeleteTermin',
			type:'POST',
			data:{stnPlId:stnId},
			dataType:'JSON',
			success:function(response){
				if(response.status === 'ok')
				{
					alert(response.status);
					location.reload();
				}
				else
				{
					alert("Fehler: "+response.status);
				}
			},
			error:function(errorThrown){
				meldung.html(errorThrown);
			}
		});
	}
	else{

	}
});
</script>
	</body>
</html>