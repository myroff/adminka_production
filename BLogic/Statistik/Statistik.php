<?php
namespace Statistik;
use PDO as PDO;
require_once BASIS_DIR.'/MVC/DBFactory.php';
use MVC\DBFactory as DBFactory;

class Statistik {
	public function getStat()
	{
		$kres = $this->getKundenDaten();
		$pres = $this->getPopulationStat();
		$ares = $this->getAgeStat();
		
		include_once BASIS_DIR.'/Templates/Statistik/Statistik.tmpl.php';
		return;
	}
	
	private function getKundenDaten()
	{
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}
		
		$q = "SELECT 'countKunde' as key1, count(t.kndId) as value1
FROM 
(SELECT kndId FROM kundehatkurse WHERE EXTRACT(YEAR_MONTH FROM NOW()) BETWEEN EXTRACT(YEAR_MONTH FROM von) AND EXTRACT(YEAR_MONTH FROM bis) GROUP BY kndId ) as t
UNION 
(SELECT 'countKurs', count(eintrId) as value1 FROM kundehatkurse WHERE NOW() BETWEEN von AND bis)
UNION 
(SELECT 'countHerren', count(*) as value1 FROM kunden WHERE anrede LIKE 'Herr')
UNION 
(SELECT 'countFrauen', count(*) as value1 FROM kunden WHERE anrede LIKE 'Frau')
UNION 
(SELECT 'countAlle', count(*) as value1 FROM kunden)";
		
		$res = array();
		
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute();
			$res = $sth->fetchAll(PDO::FETCH_KEY_PAIR);
			/*
			$sth = $dbh->prepare($qKur);
			$sth->execute();
			$res[] = $sth->fetch(PDO::FETCH_KEY_PAIR);
			*/
			return $res;
			
		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
	}
	
	private function getPopulationStat()
	{
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}
		
		$q = "(SELECT stadt, count(*) as population FROM kunden GROUP BY stadt ORDER BY population DESC)"
			. " UNION "
			. " (SELECT 'Gesammt', count(*) FROM kunden)";
		$res = array();
		
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute();
			$res = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			return $res;
			
		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
	}
	
	private function getAgeStat()
	{
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}
		
		$q = "SELECT TIMESTAMPDIFF(YEAR,geburtsdatum,CURDATE()) AS age, count(kndId) as number"
			." FROM kunden"
			//." WHERE kundenNummer > 0"
			." GROUP BY age"
			." ORDER BY age";
		$res = array();
		
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute();
			$res = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			return $res;
			
		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
	}
	
}
