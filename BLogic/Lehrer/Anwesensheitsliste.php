<?php
namespace Lehrer;
use PDO as PDO;

class Anwesensheitsliste {
	public function getList(){
		error_reporting(E_ALL); ini_set('display_errors', '1');
		
		$sArr = array();
		$sArr[':lehrId'] = empty($_GET['s_lehrId']) ? '' : $_GET['s_lehrId'];
		$sArr[':monath'] = empty($_GET['monath']) ? '' : $_GET['monath'];
		
		$res = $this->searchDates($sArr);
		//$res = array("test"=>"test");
		
		include_once BASIS_DIR.'/Templates/Lehrer/Anwesenheitsliste.tmpl.php';
		return;
	}
	
	private function searchDates($searchArr){
		$searchArr = array_filter($searchArr);
		if(empty($searchArr)){
			return false;
		}
		
		require_once BASIS_DIR.'/MVC/DBFactory.php';
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}
		$where ="";
		$where .= isset($searchArr[':lehrId']) ?  " AND l.lehrId = :lehrId " : "";
		//$where .= isset($searchArr[':wochentag']) ?  " AND stdn.wochentag = :wochentag " : "";
		
		//$where = substr($where, 0, -4);
		
		$qKids = "SELECT k.kndId, k.kundenNummer, k.vorname, k.name"
			//." FROM kunden as k JOIN kundehatkurse as khk USING(kndId) JOIN kurse as ku USING(kurId) LEFT JOIN lehrer as l USING(lehrId) LEFT JOIN stundenplan as stdn USING(kurId)"
			." FROM kundehatkurse as khk LEFT JOIN kunden as k USING(kndId)"
			." WHERE khk.kurId=:kurId AND NOW() BETWEEN khk.von AND khk.bis"
			//." "//GROUP BY khk.kndId
			." ORDER BY k.name, k.vorname";
		 
		$qGroups = "SELECT l.lehrId, l.vorname, l.name, ku.kurId, ku.kurName, GROUP_CONCAT(stdn.wochentag SEPARATOR ',') as days"
				." FROM kurse as ku LEFT JOIN lehrer as l USING(lehrId) LEFT JOIN stundenplan as stdn USING(kurId)"
				." WHERE l.lehrId=:lehrId"
				." GROUP BY ku.kurId";
		
		$rs = array();
		
		try{
			$sth = $dbh->prepare($qGroups);
			$sth->execute( array(":lehrId"=>$searchArr[':lehrId']) );// 
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