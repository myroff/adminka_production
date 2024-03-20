<?php
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
?>
<div>
	für Monat <?=Fltr::sqlDateToMonatYear($rn['rnMonat'])?>
</div>
<div>
	<table class="quitTable">
		<tr>
			<th>Kurs</th><th>Betrag</th>
		</tr>
<?php
foreach($rd as $r)
{
	echo '<tr>';
	echo '<td>' . $r['kurName'] . '</td>';
	echo '<td>' . number_format($r['rndBetrag'], 2, ',', ' ') . ' €</td>';
	echo '</tr>';
}
?>
		<tr>
			<td><b>Summe:</b></td><td><b><?= number_format($rn['summe'], 2, ',', ' ') ?> €</b></td>
		</tr>
	</table>
	<div style="margin:10px 0px"><?=$rn['rnKomm']?></div>
	<div>
<?php
$dtime = new DateTime($rn['rnErstelltAm']);
$dt = $dtime->format("d.m.Y H:i:s");
?>
		Bezahlt am <?=$dt?><br>
		an <?=$mt['vorname']?> <?=$mt['name']?>
	</div>
</div>
