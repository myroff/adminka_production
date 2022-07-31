<?php
namespace Kurse;

use PDO as PDO;
#require_once BASIS_DIR.'/Tools/TmplTools.php';
use Tools\TmplTools as TmplTls;
#require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;

class KursSelector
{
	static public function getKursSelector($selectorName="", $selectorId="", $size="", $update_url="", $asString=FALSE)
	{
		$sArr = array();
		$sArr[':kurName']   = empty($_POST['kurName'])   ? '' : $_POST['kurName'];
		$sArr[':kurAlter']  = empty($_POST['kurAlter'])  ? '' : $_POST['kurAlter'];
		$sArr[':kurKlasse'] = empty($_POST['kurKlasse']) ? '' : $_POST['kurKlasse'];
		$sArr[':wochentag'] = empty($_POST['wochentag']) ? '' : $_POST['wochentag'];
		$sArr[':wochentag'] = empty($_POST['wochentag']) ? '' : $_POST['wochentag'];
		$sArr[':season_id']	= empty($_POST['search_season'])? self::getCurrentSeasonId() : $_POST['search_season'];

		$res = self::searchDates($sArr);

		$name = empty($selectorName) ? "" : "name=$selectorName";
		$id = empty($selectorId) ? "" : "id=$selectorId";
		$size = empty($size) ? "" : "size=$size";

		$SearchPanel_formId = $selectorName."_searchPanel";

		if($asString)
		{
			ob_start();
		}
?>
<div id="searchPanel">
	<form method="POST" id="<?=$SearchPanel_formId?>"><!--id="u_kurSearch"-->
		<table>
			<tr>
				<th>
					Saison
				</th>
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
				<td>

				</td>
			</tr>
			<tr>
				<td>
					<?php echo TmplTls::getSeasonsSelector("search_season", $id."_s_season_id", $sArr[':season_id'], "Saisons", 0); ?>
				</td>
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
				<td>
					<button type="submit" class="btn waves-light black" ><i class="material-icons white-text">search</i></button>
				</td>
			</tr>
		</table>
	</form>
</div>
<div>
	<select <?=$name?> <?=$id?> <?=$size?>  class="browser-default" style="height: 200px;">
	<?php
		foreach($res as $r)
		{
			$alter = $r['kurMinAlter'];
			$alter .= $r['kurMinAlter'] < $r['kurMaxAlter'] ? " bis ".$r['kurMaxAlter'] : '';
			$alter .= empty($alter) ? '' : " Jahre.";

			$klasse = $r['kurMinKlasse'];
			$klasse .= $r['kurMinKlasse'] < $r['kurMaxKlasse'] ? " bis ".$r['kurMaxKlasse'] : "";
			$klasse .= empty($klasse) ? '' : " Klasse.";

			$background = "";

			if ( (int)$r['kunden_in_kurs'] >= (int)$r['maxKnd']) {
				$background = "background-color: red;";
			}
			elseif ((int)$r['kunden_in_kurs'] >= ($r['maxKnd'] - 2 ) && (int)$r['maxKnd'] > 3) {
				$background = "background-color: yellow;";
			}

			//echo"<option value='".$r['kurId']."'>".$r['kurName'].": ".$r['vorname']." ".$r['name'].": ".$r['kurPreis']."€. ".$alter." ".$klasse."</option>";

			echo"<option value='".$r['kurId']."' season_id='".$r['season_id']."' style='$background'>"
					.$r['kurName'].": ".$r['vorname']." ".$r['name'].": ".$r['kurPreis']."€. ".$alter." ".$klasse."<br>"
					. " ".$r['season_name']
					. " Termine: <br>"
					. Fltr::printSqlTermin($r['termin'], ";", "->", "<br>")."<br>"
					." (".$r['kunden_in_kurs']."/".$r['maxKnd'].")"
				. "</option>";
		}
	?>
	</select>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
	document.getElementById('<?=$SearchPanel_formId?>').onsubmit = function(e){
		e.preventDefault();
		selector = document.getElementById('<?=$selectorId?>');
		data = new FormData(this);
		request = new XMLHttpRequest();
		request.open("POST", "<?=BASIS_URL?><?=$update_url?>", true);
		request.onreadystatechange = function(){
			if (request.readyState === XMLHttpRequest.DONE && request.status === 200){
				selector.innerHTML = request.responseText;/* request.responseText; */
			}
		};
		request.send(data);
	};
});
/*
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
*/
</script>
<?php
		if($asString)
		{
			$out = ob_get_contents();

			ob_end_clean();

			return $out;
		}

		return;
	}

	public function updateKursSelector()
	{
		$sArr = array();
		$sArr[':season_id']	= empty($_POST['search_season'])? '' : $_POST['search_season'];
		$sArr[':kurName']	= empty($_POST['kurName'])		? '' : $_POST['kurName'];
		$sArr[':kurAlter']	= empty($_POST['kurAlter'])		? '' : $_POST['kurAlter'];
		$sArr[':kurKlasse']	= empty($_POST['kurKlasse'])	? '' : $_POST['kurKlasse'];
		$sArr[':wochentag']	= empty($_POST['wochentag'])	? '' : $_POST['wochentag'];

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

				$background = "";

				if ( (int)$r['kunden_in_kurs'] >= (int)$r['maxKnd']) {
					$background = "background-color: red;";
				}
				elseif ((int)$r['kunden_in_kurs'] >= ($r['maxKnd'] - 2 ) && (int)$r['maxKnd'] > 3) {
					$background = "background-color: yellow;";
				}

				//echo "<option value='".$r['kurId']."'>".$r['kurName'].": ".$r['vorname']." ".$r['name'].": ".$r['kurPreis']."€. ".$alter." ".$klasse."</option>";
				echo "<option value='".$r['kurId']."' season_id='".$r['season_id']."' style='$background'>"
						.$r['kurName'].": ".$r['vorname']." ".$r['name'].": ".$r['kurPreis']."€. ".$alter." ".$klasse."<br>"
						. " ".$r['season_name']
						. " Termine: <br>"
						. Fltr::printSqlTermin($r['termin'], ";", "->", "; <br>")."<br>"
						." (".$r['kunden_in_kurs']."/".$r['maxKnd'].")"
					. "</option>";
			}

			$output = ob_get_contents();
			ob_end_clean();
		}

		header("Content-type: text/html");
		exit($output);
	}

	private static function searchDates($searchArr)
	{
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}

		$where = "";
		$seasonalWhere = "";
		//delete empty entries
		$searchArr = array_filter($searchArr);
		$seasonalSearch = [];
		$searchInfo = [];

		$q = "SELECT k.*, l.vorname, l.name, se.season_id, se.season_name"
			. ", group_concat('{\"wochentag\":\"',wochentag,'\",\"time\":\"', TIME_FORMAT(anfang, '%H:%i'),' - ', TIME_FORMAT(ende, '%H:%i'),'\"}' SEPARATOR',') as termin"
			. ", khk.kunden_in_kurs"
			. " FROM kurse as k LEFT JOIN stundenplan as st USING(kurId) LEFT JOIN lehrer as l USING(lehrId)"
			. " LEFT JOIN seasons as se USING(season_id)"
			. " LEFT JOIN (SELECT kurId, COUNT(kurId) as kunden_in_kurs FROM kundehatkurse WHERE season_id=:season_id GROUP BY kurId) as khk on k.kurId = khk.kurId"
			;

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
				$seasonalWhere .= " :kurAlter BETWEEN kurMinAlter AND kurMaxAlter AND";
				$seasonalSearch[':kurAlter'] = $searchArr[':kurAlter'];
			}
			if(isset($searchArr[':kurKlasse']))
			{
				$where .= " :kurKlasse BETWEEN kurMinKlasse AND kurMaxKlasse AND";
				$seasonalWhere .= " :kurKlasse BETWEEN kurMinKlasse AND kurMaxKlasse AND";
				$seasonalSearch[':kurKlasse'] = $searchArr[':kurKlasse'];
			}
			if(isset($searchArr[':wochentag']))
			{
				$where .= " wochentag=:wochentag AND";
			}
			if(isset($searchArr[':season_id']))
			{
				$where .= " season_id=:season_id AND";
				$seasonalWhere .= " season_id=:season_id AND";
				$seasonalSearch[':season_id'] = $searchArr[':season_id'];
			}

			$where = substr($where, 0, -4);
			$seasonalWhere = substr($seasonalWhere, 0, -4);

			$matchedKurId = [];

			if(!empty($where)) {
				// check course_to_seasons if season_id is set and there is some else requirements
				$filterKurId = '';
				$seasonalKurId = [];

				if (isset($seasonalSearch[':season_id']) and count($seasonalSearch) > 1) {
					$qSeasonalKurId = "SELECT kurId FROM course_to_seasons WHERE ".$seasonalWhere;
					$sth = $dbh->prepare($qSeasonalKurId);
					$sth->execute($seasonalSearch);
					$seasonalKurId = $sth->fetchAll(PDO::FETCH_COLUMN);
				}

				//filter
				$qDefaultKurId = "
 SELECT k.kurId
 FROM kurse as k
 JOIN stundenplan as std USING(kurId)
 WHERE ".$where;
/*
				if(isset($searchArr[':season_id'])) {
					$qDefaultKurId .= " AND k.kurId NOT IN (SELECT kurId FROM course_to_seasons WHERE season_id = :season_id)";
				}
*/
				$sth = $dbh->prepare($qDefaultKurId);
				$sth->execute($searchArr);
				$matchedKurId = $sth->fetchAll(PDO::FETCH_COLUMN);

				$matchedKurId = array_merge($matchedKurId, $seasonalKurId);
			}

			if(!empty($matchedKurId)) {

				$q .= " WHERE k.kurId IN('".implode("','", $matchedKurId)."')";

				if(isset($searchArr[':season_id'])) {
					$q .= " AND st.season_id=:season_id";
					$searchInfo[':season_id'] = $searchArr[':season_id'];
				}
			}
		}

		$q .= " GROUP BY k.kurId";
#var_dump($where);die();
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute($searchInfo);
			$rs = $sth->fetchAll(PDO::FETCH_ASSOC);

			$stdModel = new \Stundenplan\StundenplanModel();
			$rs = $stdModel->updateSeasonalData($rs);

			return $rs;

		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
	}

	static public function getCurrentSeasonId()
	{
		$dbh = \MVC\DBFactory::getDBH();
		$q   = "SELECT season_id FROM seasons WHERE is_active = 1";
		$sth = $dbh->prepare($q);
		$sth->execute();
		$res = $sth->fetch(PDO::FETCH_ASSOC);
		return $res['season_id'] ?: 0;
	}
}
