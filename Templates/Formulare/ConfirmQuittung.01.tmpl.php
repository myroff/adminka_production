<div>
<?=$tln['vorname']?> <?=$tln['name']?><br>
<?=$_POST['rnMonat']?>
</div>
<div style="float:left;width:350px;">
	<table class="quitTable">
		<tr>
			<th>Schuljahr</th><th>Kurs</th><th>Betrag</th>
		</tr>
<?php
foreach($res as $r)
{
	echo '<tr>';
	echo '<td>' . $r['season_name'] . '</td>';
	echo '<td>' . $r['kurName'] . '</td>';
	echo '<td>' . number_format($kurId[$r['eintrId']], 2, ',', ' ' ) . ' €</td>';
	echo '</tr>';
}
?>
		<tr>
			<td><b>Summe:</b></td><td><b><?= number_format($summe, 2, ',', ' ') ?> €</b></td>
		</tr>
	</table>
	<div style="margin:10px 0px"><?=$rnKomm?></div>
	<div>
		Bezahlt am <?=date('d.m.Y H:i')?><br>
		an <?=$curMtb['vorname']?> <?=$curMtb['name']?>
	</div>
</div>