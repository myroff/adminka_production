<?php
namespace Kurse;
use PDO as PDO;
require_once BASIS_DIR.'/Tools/TmplTools.php';
use Tools\TmplTools as TmplTls;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;

class KursSelector {
	static public function getKursSelector($selectorName="", $selectorId="", $size="", $update_url="")
	{
		$sArr = array();
		$sArr[':kurName'] = empty($_POST['kurName']) ? '' : $_POST['kurName'];
		$sArr[':kurAlter'] = empty($_POST['kurAlter']) ? '' : $_POST['kurAlter'];
		$sArr[':kurKlasse'] = empty($_POST['kurKlasse']) ? '' : $_POST['kurKlasse'];
		$sArr[':wochentag'] = empty($_POST['wochentag']) ? '' : $_POST['wochentag'];
		
		$res = self::searchDates($sArr);
		
		$name = empty($selectorName) ? "" : "name=$selectorName";
		$id = empty($selectorId) ? "" : "id=$selectorId";
		$size = empty($size) ? "" : "size=$size";
		
		$SearchPanel_formId = $selectorName."_searchPanel";
		
?>
<div id="searchPanel">
	<form method="POST" id="<?=$SearchPanel_formId?>"><!--id="u_kurSearch"-->
		<table>
			<tr>
				<th>
					Kursname
				</th>
				<th>
					Wochentag
				</th>
				<th>
					Alter
				</th>
				<th>
					Klasse
				</th>
				<td rowspan="2">
					<input type='submit' value='' class="search" style="">
				</td>
			</tr>
			<tr>
				<td>
					<input name="kurName" type="text" value="<?=$sArr[':kurName']?>" />
				</td>
				<td>
					<?php echo TmplTls::getWeekdaySelector('wochentag');?>
				</td>
				<td>
					<input name="kurAlter" type="text" value="<?=$sArr[':kurAlter']?>" />
				</td>
				<td>
					<input name="kurKlasse" type="text" value="<?=$sArr[':kurKlasse']?>" />
				</td>
			</tr>
		</table>
	</form>
</div>
<div>
	<select <?=$name?> <?=$id?> <?=$size?> >
	<?php
		foreach($res as $r)
		{
			$alter = $r['kurMinAlter'];
			$alter .= $r['kurMinAlter'] < $r['kurMaxAlter'] ? " bis ".$r['kurMaxAlter'] : '';
			$alter .= empty($alter) ? '' : " Jahre.";

			$klasse = $r['kurMinKlasse'];
			$klasse .= $r['kurMinKlasse'] < $r['kurMaxKlasse'] ? " bis ".$r['kurMaxKlasse'] : "";
			$klasse .= empty($klasse) ? '' : " Klasse.";
			
			//echo"<option value='".$r['kurId']."'>".$r['kurName'].": ".$r['vorname']." ".$r['name'].": ".$r['kurPreis']."€. ".$alter." ".$klasse."</option>";
			
			echo"<option value='".$r['kurId']."'>".$r['kurName'].": ".$r['vorname']." ".$r['name'].": ".$r['kurPreis']."€. ".$alter." ".$klasse."<br>"
					. " Termine: <br>"
					. Fltr::printSqlTermin($r['termin'], ";", "->", "<br>")
					. " </option>";
		}
	?>
	</select>
</div>
<script>
$('#<?=$SearchPanel_formId?>').submit(function (e){
	selector = $('#<?=$selectorId?>');
	e.preventDefault();
	var postData = $(this).serializeArray();

	$.ajax({
		url:'<?=BASIS_URL?><?=$update_url?>',
		type:'POST',
		data:postData,
		dataType:'html',
		success:function(response){
			selector.html(response);
		},
		error:function(errorThrown){
			selector.text(errorThrown);
		}
	});
});
</script>
<?php

		return;
	}
	
	public function updateKursSelector()
	{
		$sArr = array();
		$sArr[':kurName'] = empty($_POST['kurName']) ? '' : $_POST['kurName'];
		$sArr[':kurAlter'] = empty($_POST['kurAlter']) ? '' : $_POST['kurAlter'];
		$sArr[':kurKlasse'] = empty($_POST['kurKlasse']) ? '' : $_POST['kurKlasse'];
		$sArr[':wochentag'] = empty($_POST['wochentag']) ? '' : $_POST['wochentag'];
		
		$res = $this->searchDates($sArr);
		
		if(empty($res))
		{
			$output = "<option>keine passende Einträge gefunden</option>";
		}
		else
		{
			ob_start();
			foreach($res as $r)
			{
				$alter = $r['kurMinAlter'];
				$alter .= $r['kurMinAlter'] < $r['kurMaxAlter'] ? " bis ".$r['kurMaxAlter'] : '';
				$alter .= empty($alter) ? '' : " Jahre.";

				$klasse = $r['kurMinKlasse'];
				$klasse .= $r['kurMinKlasse'] < $r['kurMaxKlasse'] ? " bis ".$r['kurMaxKlasse'] : "";
				$klasse .= empty($klasse) ? '' : " Klasse.";

				//echo "<option value='".$r['kurId']."'>".$r['kurName'].": ".$r['vorname']." ".$r['name'].": ".$r['kurPreis']."€. ".$alter." ".$klasse."</option>";
				echo"<option value='".$r['kurId']."'>".$r['kurName'].": ".$r['vorname']." ".$r['name'].": ".$r['kurPreis']."€. ".$alter." ".$klasse."<br>"
					. " Termine: <br>"
					. Fltr::printSqlTermin($r['termin'], ";", "->", "; <br>")
					. " </option>";
			}

			$output = ob_get_contents();
			ob_end_clean();
		}

		header("Content-type: text/html");
		exit($output);
	}
	
	private function searchDates($searchArr)
	{
		require_once BASIS_DIR.'/MVC/DBFactory.php';
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}
		
		$where = "";
		
		//delete empty entries
		$searchArr = array_filter($searchArr);
		$q = "SELECT k.*, l.vorname, l.name,"
			. " group_concat('{\"wochentag\":\"',wochentag,'\",\"time\":\"', TIME_FORMAT(anfang, '%H:%i'),' - ', TIME_FORMAT(ende, '%H:%i'),'\"}' SEPARATOR',') as termin"
			. " FROM kurse as k LEFT JOIN stundenplan as st USING(kurId) LEFT JOIN lehrer as l USING(lehrId)";
		
		if(!empty($searchArr))
		{
			if(isset($searchArr[':kurName']))
			{
				$searchArr[':kurName'] .= '%';
				$where .= " kurName LIKE :kurName AND";
			}
			if(isset($searchArr[':kurAlter']))
			{
				$where .= " :kurAlter BETWEEN kurMinAlter AND kurMaxAlter AND";
			}
			if(isset($searchArr[':kurKlasse']))
			{
				$where .= " :kurKlasse BETWEEN kurMinKlasse AND kurMaxKlasse AND";
			}
			if(isset($searchArr[':wochentag']))
			{
				$where .= " wochentag=:wochentag AND";
			}
			
			$where = substr($where, 0, -4);
			$q .= empty($where) ? '' : " WHERE " . $where;
		}
		
		$q .= " GROUP BY k.kurId";
		
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute($searchArr);
			$rs = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			return $rs;
			
		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
	}
}
