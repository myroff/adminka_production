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
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style-print.css" type="text/css" media="print" />

		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>

		<script src="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.css">

		<style>
			#editTermin, #KursInfoTable
			{
				display:none;
				border:2px solid #a1a1a1;
				padding:20px;
				background:#dddddd;
				width:450px;
				border-radius:20px;

				position:fixed;
				top:100px;
				left:30%;
				z-index:100;
			}
			#stnPlFlex
			{
				width:1200px;
				overflow:hidden;
			}

			.timeLine{width:120px;float:left;}
			.time{height:180px;border-top:1px solid black;border-bottom:1px solid black;z-index:0;}
			.lessons{width:120px;height:180px;float:left;position:relative;font:12px arial, sans-serif;}
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
			<div id="meldung">
				<?php
				if(isset($meldung))
				{
					echo $meldung;
				}
				?>
			</div>
<!-- search-panel -->
			<div id="search-panel">
				<form method="GET" action="">
					<table>
						<tr>
							<th>Tag</th>
							<th>Lehrer</th>
							<th>Raum</th>
							<th>Kurs</th>
							<th>Klasse</th>
							<th>Alter</th>
							<td rowspan="2"><input class="search" type="submit" value="" /></td>
						</tr>
						<tr>
							<td><?php echo TmplTls::getSeasonsSelector("season_id", "updateTerminForm_season_id"); ?></td>
							<td><?php echo TmplTls::getWeekdaySelector("search_wochentag", "search_wochentag", $sArr[':wochentag']); ?></td>
							<td><?php echo TmplTls::getLehrerSelector("search_lehrId", "search_lehrId", $sArr[':lehrId']); ?></td>
							<td><?php TmplTls::getRaumSelector("search_raum", "search_raum", $sArr[':raum']); ?></td>
							<td><?php TmplTls::getKursSelector("search_kurs", "search_kurs", $sArr[':kurName']); ?></td>
							<td><?php TmplTls::getKlasseSelector("search_klasse", "search_klasse", $sArr[':klasse']); ?></td>
							<td><?php TmplTls::getAlterSelector("search_alter", "search_alter", $sArr[':alter']); ?></td>
						</tr>
					</table>
				</form>
			</div>

			<?php

				$curTime = "";
				$endTime = "";
				$curDay = "";
				$maxRaum = count($raum)+1;

				$oneLesson = DateInterval::createFromDateString('45 min'); // 1 Lesson = 45 minut
				$oneHour = DateInterval::createFromDateString('1 hour'); // 1 hour

				$startTime = new DateTime('09:00');
				$endOfHour = new DateTime('10:00');
				$endTime = new DateTime('20:00');

			?>

			<!--<div id="">-->
				<button class="printButton" printDiv="stnPlnTbl" >Print</button>
				<table id="stnPlnTbl">
					<tr style="background:#292929;color:white;">
						<th>
							Zeit/Raum
						</th>
						<?php
							//for($i=2; $i <= $maxRaum; $i++)
							foreach($raum as $i)
							{
								echo "<th>".$i['raum']." Raum</th>";
							}
						?>
					</tr>
					<?php

					$curTime = "";
					$endTime = isset($res[0]['ende']) ? strtotime($res[0]['ende']) : 0;
					$curDay = 0;
					$oneLesson = DateInterval::createFromDateString('45 min'); // 1 Lesson = 45 minut
					$curTime = isset($res[0]['anfang']) ? strtotime($res[0]['anfang']) : 0;
					$curHour = "-1";
					$resLength = count($res);
					$pixelsProMin = 4; //60 minuten -> 240 px
					if(!$res) echo "Zu Ihren Angaben haben wir leider keine passende Kurse.";
					for($i=0; $i<$resLength; )
					{
						//$curHourTime = strtotime($res[$i]['anfang']);
						//$curHour = intval( date('G', strtotime($res[$i]['anfang'])) );


						if( $curDay !== intval($res[$i]['wochentag']) )
						{

				//if curTime != endTime
							$endHour = intval( date('G', $endTime) );
							++$curHour;
							if($curHour < $endHour && $i > 0){
								if(date('i', $endTime) == '00'){
									--$endHour;
								}
								for( ;$curHour <= $endHour; ++$curHour)
								{
									echo "<tr>";
									echo "<td class='timeTR'>".$curHour.":00</td>";
						//placeholder für Räume
									foreach($raum as $r)
									{
										echo "<td></td>";
									}
									echo "</tr>";
								}
							}

							$curHour = intval( date('G', strtotime($res[$i]['anfang']) ) );
							$curDay = intval($res[$i]['wochentag']);
							echo "<tr ><th colspan='$maxRaum' class='headerWeekday'>".Fltr::getWeekdayFromInt($res[$i]['wochentag'])."</th></tr>";

					//reset endTime
							$endTime = strtotime($res[$i]['ende']);
						}
						else{
							++$curHour;

						}
						$curHourTime = strtotime($curHour.':00');
						echo "<tr >";

						echo "<td class='timeTR'>".$curHour.":00</td>"; //

						//for($r=2; $r<11; ++$r)
						foreach ($raum as $r)
						{
							echo "<td>";

							if(isset($res[$i]))
							{
								while( $r['raum'] == $res[$i]['raum'] AND $curHour == date('G', strtotime($res[$i]['anfang'])) AND isset($res[$i])
										AND $curDay === intval($res[$i]['wochentag']) )
								{
							//späteste endTime gesucht
									$tmpEndTime = strtotime($res[$i]['ende']);
									if( $endTime < $tmpEndTime ){
										$endTime = $tmpEndTime;
									}
									$anfangTime = strtotime($res[$i]['anfang']);

									$style = "";
									$class = "";

									if($res[$i]['countKnd'] > 0 AND $res[$i]['countKnd'] < $res[$i]['maxKnd'])
									{
										$class .= "belegt_mit_freien_plaetzen";
									}
									elseif($res[$i]['countKnd'] >= $res[$i]['maxKnd'])
									{
										$class .= " belegt_voll";
									}
									else{

									}
									$unterrichtsDauer = (strtotime($res[$i]['ende']) - strtotime($res[$i]['anfang']) ) / 60 ;
						//Dauer in pixel -2px für border top & bottom
									$style .= "height:".($unterrichtsDauer * $pixelsProMin)."px;";
						//abstand von Beginn der Stunde
									$style .= "top:".( (($anfangTime - $curHourTime)/ 60 * $pixelsProMin) - 2)."px;";
									echo "<div class='cursItem $class' style='$style'>";
									echo "Dauer: ".$unterrichtsDauer." Min.<br>";
									if( '00' != date('i', strtotime($res[$i]['anfang'])) OR '45' != date('i', strtotime($res[$i]['ende'])) ){
										echo date('G:i', strtotime($res[$i]['anfang']))."-".date('G:i', strtotime($res[$i]['ende']))."<br>";
									}
									echo "<button class='info' kurId='".$res[$i]['kurId']."' ></button>";
									echo "<button class='editItemButton editTerminButton' stnPlId='".$res[$i]['stnPlId']."' anf='".$res[$i]['anfang']."' end='".$res[$i]['ende']."'"
											. " raum='".$res[$i]['raum']."' wTag='".$res[$i]['wochentag']."'>"
											. "</button>";

									echo $res[$i]['kurName']."<br>"
										.$res[$i]['vorname']." ".$res[$i]['name']."<br>";

									if(!empty($res[$i]['kurMinAlter']) AND !empty($res[$i]['kurMaxAlter'])
										AND ($res[$i]['kurMinAlter'] != $res[$i]['kurMaxAlter']) )
									{
										echo "Alter: ".$res[$i]['kurMinAlter']."-".$res[$i]['kurMaxAlter']."<br>";
									}
									elseif(!empty($res[$i]['kurMinAlter']))
									{
										echo "Alter: ".$res[$i]['kurMinAlter']."<br>";
									}
									if(!empty($res[$i]['kurMinKlasse']) AND !empty($res[$i]['kurMaxKlasse'])
										AND ($res[$i]['kurMinKlasse'] != $res[$i]['kurMaxKlasse']) )
									{
										echo "Klasse: ".$res[$i]['kurMinKlasse']."-".$res[$i]['kurMaxKlasse']."<br>";
									}
									elseif(!empty($res[$i]['kurMinKlasse']))
									{
										echo "Klasse: ".$res[$i]['kurMinKlasse']."<br>";
									}

									echo "<span class='dont-print' >"
											.$res[$i]['countKnd']."/".$res[$i]['maxKnd']
										."</span>";

									echo "</div>";
									++$i;
									if( !isset($res[$i]) ){
										break;
									}

								}
							}
							/*
							else{
								echo "<td>";
							}
							*/
							echo "</td>";
						}

						echo "</tr>";
					}
					?>
				</table>
			<!--</div>-->
			<div><!-- Liste -->
				<table >
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
						<td><button class="info" kurId="<?=$r['kurId']?>" ></button></td>
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

	<!-- Kursinfo -->
		<div id="KursInfoTable">
			<div id="KursInfoTable_Info"></div>
				 <div class="buttonsOkCancelDiv" >
					<button id="closeKursInfoTable" class="cancel" >&nbsp;</button>
				</div>
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

//get info
$('.info').click(function(){
	kurId = $(this).attr('kurId');
	$.ajax({
		url:'<?=BASIS_URL?>/admin/ajaxStnPlKurInfo/'+kurId,
		//type:'POST',
		//data:postData,
		dataType:'HTML',
		success:function(response){
			$('#KursInfoTable_Info').html(response);
			$('#KursInfoTable').slideDown();
		},
		error:function(errorThrown){
			meldung.html(errorThrown);
		}
	});
});

$('#closeKursInfoTable').click(function(){
	$('#KursInfoTable').slideUp();
});

$('.printButton').click(function(){
	div = $(this).attr('printDiv');

	w=window.open();
	//doc = document.implementation.createHTMLDocument("Test Print");
	//doc.innerHTML = $('#'+div).html();
	//w.document.write(doc);

	w.document.write('<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" />');
	w.document.write($('#'+div).html());
	//w.print();
	//w.close();
});
/*
 * function PrintElem(elem)
    {
        Popup($(elem).html());
    }

    function Popup(data)
    {
        var mywindow = window.open('', 'my div', 'height=400,width=600');
        mywindow.document.write('<html><head><title>my div</title>');
        //optional stylesheet
		//mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');

        mywindow.print();
        mywindow.close();

        return true;
    }
 */
</script>
	</body>
</html>