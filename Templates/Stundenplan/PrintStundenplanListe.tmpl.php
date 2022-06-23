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
		<title>SWIFF: Print Stundenplan.</title>
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
			/*
			?>
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
			*/
					$curTime = "";
					$endTime = isset($res[0]['ende']) ? strtotime($res[0]['ende']) : 0;
					$curDay = -1;
					$oneLesson = DateInterval::createFromDateString('45 min'); // 1 Lesson = 45 minut
					$curTime = isset($res[0]['anfang']) ? strtotime($res[0]['anfang']) : 0;
					$curHour = "-1";
					$resLength = count($res);
					$pixelsProMin = 4; //60 minuten -> 240 px
					if(!$res) echo "Zu Ihren Angaben haben wir leider keine passende Kurse.";
					for($i=0; $i<$resLength; )
					{
						if( $curDay !== intval($res[$i]['wochentag']) )
						{
							if($i>0){
								echo "</table><table class=\"stnPlnTbl-Print\">";
							}
							else{
								echo "<table class=\"stnPlnTbl-Print\">";
							}
							$curHour = intval( date('G', strtotime($res[$i]['anfang']) ) );
							$curDay = intval($res[$i]['wochentag']);
							echo "<tr ><th colspan='$maxRaum' class='headerWeekday'>".Fltr::getWeekdayFromInt($res[$i]['wochentag'])."</th></tr>";
					?>
						<tr >
							<th>
								Zeit/Raum
							</th>
							<?php
								//for($i=2; $i <= $maxRaum; $i++)
								foreach($raum as $n)
								{
									echo "<th>".$n['raum']." Raum</th>";
								}
							?>
						</tr>
						<?php

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
									//$style .= "height:".($unterrichtsDauer * $pixelsProMin)."px;";
						//abstand von Beginn der Stunde
									//$style .= "top:".( (($anfangTime - $curHourTime)/ 60 * $pixelsProMin) - 2)."px;";
									echo "<div class='cursItem $class' style='$style'>";
									echo $res[$i]['kurName']."<br>";

									if( '00' != date('i', strtotime($res[$i]['anfang'])) OR '45' != date('i', strtotime($res[$i]['ende'])) ){
										echo date('G:i', strtotime($res[$i]['anfang']))."-".date('G:i', strtotime($res[$i]['ende']))."<br>";
									}

									echo "Dauer: ".$unterrichtsDauer." Min.<br>";

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

		</div><!-- Main Content Ende -->

	</body>
</html>