<div>
<?=$tln['vorname']?> <?=$tln['name']?><br>
<?=$_POST['rnMonat']?>
</div>
<div style="float:left;width:200px;">
	<table class="quitTable">
		<tr>
			<th>Kurs</th><th>Betrag</th>
		</tr>
<?php
foreach($res as $r)
{
	echo '<tr>';
	echo '<td>' . $r['kurName'] . '</td>';
	echo '<td>' . $kurId[$r['kurId']] . ' €</td>';
	echo '</tr>';
}
?>
		<tr>
			<td><b>Summe:</b></td><td><b><?=$summe?> €</b></td>
		</tr>
	</table>
	<div style="margin:10px 0px"><?=$rnKomm?></div>
	<div>
		Bezahlt am <?=date('d.m.Y H:i')?><br>
		an <?=$curMtb['vorname']?> <?=$curMtb['name']?>
	</div>
</div>