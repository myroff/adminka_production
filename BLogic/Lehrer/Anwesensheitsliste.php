<?php
namespace Lehrer;
use PDO as PDO;
require_once BASIS_DIR.'/BLogic/Kurse/Seasons.php';
use Kurse\Seasons as Seasons;

class Anwesensheitsliste {
	public function getList(){
		error_reporting(E_ALL); ini_set('display_errors', '1');

		$sArr = array();
		$sArr[':season'] = empty($_GET['s_season']) ? '' : $_GET['s_season'];
		$sArr[':lehrId'] = empty($_GET['s_lehrId']) ? '' : $_GET['s_lehrId'];
		$sArr[':month'] = empty($_GET['month'])   ? '' : $_GET['month'];

		$res = $this->searchDates($sArr);
		//$res = array("test"=>"test");

		include_once BASIS_DIR.'/Templates/Lehrer/Anwesenheitsliste.tmpl.php';
		return;
	}

	private function searchDates($searchArr){
		$searchArr = array_filter($searchArr);
		if(empty($searchArr) || count($searchArr) < 3){
			return false;
		}


		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}
		//$where .= isset($searchArr[':wochentag']) ?  " AND stdn.wochentag = :wochentag " : "";
		$whereKids = empty($searchArr[':month']) ?	'NOW() BETWEEN khk.von AND khk.bis' :
													$searchArr[':month'].' BETWEEN EXTRACT(YEAR_MONTH FROM khk.von) AND EXTRACT(YEAR_MONTH FROM khk.bis)'
													;
		//$where = substr($where, 0, -4);

		$qKids = "SELECT k.kndId, k.kundenNummer, k.vorname, k.name"
			//." FROM kunden as k JOIN kundehatkurse as khk USING(kndId) JOIN kurse as ku USING(kurId) LEFT JOIN lehrer as l USING(lehrId) LEFT JOIN stundenplan as stdn USING(kurId)"
			." FROM kundehatkurse as khk LEFT JOIN kunden as k USING(kndId)"
			." WHERE khk.kurId=:kurId AND ".$whereKids
			//." "//GROUP BY khk.kndId
			." ORDER BY k.name, k.vorname";
#die($qKids);
		$qGroups = "SELECT l.lehrId, l.vorname, l.name, ku.kurId, ku.kurName, GROUP_CONCAT(stdn.wochentag SEPARATOR ',') as days"
				." FROM kurse as ku LEFT JOIN lehrer as l USING(lehrId) LEFT JOIN stundenplan as stdn USING(kurId)"
				." WHERE l.lehrId=:lehrId AND stdn.season_id = :season"
				." GROUP BY ku.kurId";

		$rs = array();

		try{
			$sth = $dbh->prepare($qGroups);
			$sth->execute( array(":lehrId"=>$searchArr[':lehrId'], ":season"=>$searchArr[':season']) );//
			$rs = $sth->fetchAll(PDO::FETCH_ASSOC);

			$sth2 = $dbh->prepare($qKids);

			for($i=0; $i<count($rs); $i++) {
				$sth2->execute(array( ":kurId"=>$rs[$i]['kurId']) );
				$rs[$i]['kids'] = $sth2->fetchAll(PDO::FETCH_ASSOC);
			}


		} catch (Exception $ex) {
			print $ex;
			return FALSE;
		}

		return $rs;
	}
}