<?php
namespace Stundenplan;
use PDO as PDO;
require_once BASIS_DIR.'/MVC/DBFactory.php';
use MVC\DBFactory as DBFactory;

class Stundenplan {
	public function showStundeplan()
	{
		$sArr = array();
		$sArr[':wochentag'] = !empty($_GET['search_wochentag']) ? trim($_GET['search_wochentag']) : '';
		$sArr[':lehrId'] = !empty($_GET['search_lehrId']) ? trim($_GET['search_lehrId']) : '';
		$sArr[':raum'] = !empty($_GET['search_raum']) ? trim($_GET['search_raum']) : '';
		$sArr[':kurName'] = !empty($_GET['search_kurs']) ? trim($_GET['search_kurs']) : '';
		$sArr[':alter'] = !empty($_GET['search_alter']) ? trim($_GET['search_alter']) : '';
		$sArr[':klasse'] = !empty($_GET['search_klasse']) ? trim($_GET['search_klasse']) : '';
		
		$res = $this->searchDates($sArr);
		$raum = $this->getRaum();
		
		$this->loadTamplate($res, $raum, $sArr);
		return;
	}
	public function loadTamplate($res, $raum, $sArr){
		include_once  BASIS_DIR.'/Templates/Stundenplan/StundenplanListe.tmpl.php';
	}
	public function searchDates($searchArr)
	{
		require_once BASIS_DIR.'/MVC/DBFactory.php';
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}
		
		$where = " k.isKurInactive is NOT TRUE AND ";
		$having = "";
		
		//delete empty entries
		$searchArr = array_filter($searchArr);
		
		if(isset($searchArr[':raum']))
		{
			$having .= " stpl.raum = :raum AND";
		}
		if(isset($searchArr[':lehrId']))
		{
			$having .= " l.lehrId = :lehrId AND";
		}
		if(isset($searchArr[':wochentag']))
		{
			$where .= " stpl.wochentag = :wochentag AND";
		}
		if(isset($searchArr[':kurName']))
		{
			$where .= " k.kurName LIKE :kurName AND";
			$searchArr[':kurName'] .= '%';
		}
		if(isset($searchArr[':alter']))
		{
			$where .= "( :alter BETWEEN k.kurMinAlter AND k.kurMaxAlter) AND";
			$searchArr[':alter'] .= '%';
		}
		if(isset($searchArr[':klasse']))
		{
			$where .= "( :klasse BETWEEN k.kurMinKlasse AND k.kurMaxKlasse) AND";
			$searchArr[':klasse'] .= '%';
		}
		
		$having = substr($having, 0, -4);
		$where = substr($where, 0, -4);
		
		$q = "SELECT ( (TIME_TO_SEC(ende) - TIME_TO_SEC(anfang) )/60 ) as kurLength,"
				. " TIME_FORMAT(anfang, '%H:%i') as anfang, TIME_FORMAT(ende, '%H:%i') as ende, wochentag, raum,"
				. " k.*, l.name, l.vorname, l.lehrId, count(khk.kndId) as countKnd, k.maxKnd, stpl.stnPlId"
				. " FROM stundenplan as stpl"
				. " LEFT JOIN kurse as k USING(kurId)"
				. " LEFT JOIN lehrer as l USING(lehrId)"
				. " LEFT JOIN (SELECT * FROM kundehatkurse WHERE NOW() <= bis) as khk USING(kurId)";//BETWEEN von AND
		
		$q .= empty($where) ? '' : " WHERE ".$where;
		$q .= " GROUP By stpl.stnPlId ";
		
		$q .= empty($having) ? '' : " HAVING " . $having;
		$q .= " ORDER By stpl.wochentag, HOUR(stpl.anfang), cast(stpl.raum as unsigned) ASC";
		
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
	
	private function getRaum()
	{
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}
		
		$q = "SELECT DISTINCT(raum) FROM stundenplan"
			." ORDER BY CAST(raum AS UNSIGNED) ASC";
		
		try
		{
			
			$sth = $dbh->prepare($q);
			$sth->execute();
			$rs = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			return $rs;
			
		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
	}
}