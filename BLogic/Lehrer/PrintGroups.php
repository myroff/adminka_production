<?php
namespace Lehrer;
use PDO as PDO;

class PrintGroups {
	public function startPage(){
		
		$sArr = array();
		$sArr[':lehrId'] = empty($_GET['s_lehrId']) ? '' : $_GET['s_lehrId'];
		$sArr[':wochentag'] = empty($_GET['wochentag']) ? '' : $_GET['wochentag'];
	//delete empty entries
		
		$res = $this->searchDates($sArr);
		
		include_once BASIS_DIR.'/Templates/Lehrer/PrintGroups.tmpl.php';
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
		$where .= isset($searchArr[':wochentag']) ?  " AND stdn.wochentag = :wochentag " : "";
		
		//$where = substr($where, 0, -4);
		
		$q = "SELECT ku.kurId, l.lehrId, k.anrede, k.vorname, k.name, TIMESTAMPDIFF(YEAR,k.geburtsdatum,CURDATE()) as 'alter', stdn.anfang, stdn.ende, stdn.wochentag,"
			." stdn.raum, ku.kurName, l.name as lName, l.vorname as lVorname"
			//." FROM kunden as k JOIN kundehatkurse as khk USING(kndId) JOIN kurse as ku USING(kurId) LEFT JOIN lehrer as l USING(lehrId) LEFT JOIN stundenplan as stdn USING(kurId)"
			." FROM kurse as ku LEFT JOIN lehrer as l USING(lehrId) LEFT JOIN stundenplan as stdn USING(kurId) JOIN kundehatkurse as khk USING(kurId) LEFT JOIN kunden as k USING(kndId)"
			." WHERE khk.bis >= CURDATE() ".$where
			//." "//GROUP BY khk.kndId
			." ORDER BY stdn.wochentag, stdn.anfang, stdn.raum, k.vorname, k.name";
		
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
