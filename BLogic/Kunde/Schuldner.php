<?php
namespace Kunde;
use PDO as PDO;
use DateTime as DateTime;
use DateInterval as DateInterval;

class Schuldner
{
	public function showSchuldner()
	{
		$sArr = array();
		
		$sArr['startMnt'] = empty($_POST['startMnt']) ? '2015-08' : trim($_POST['startMnt']);
		$sArr['endMnt'] = empty($_POST['endMnt']) ? date('Y-m') : trim($_POST['endMnt']);//'2014-10'
		$sArr['withLst'] = isset($_POST['withLst']) ? true : false;
		
		$res = $this->searchDates($sArr);
		
		include_once BASIS_DIR.'/Templates/Kunde/SchuldnerListe.tmpl.php';
		return;
	}
	
	private function searchDates($searchArr)
	{
		require_once BASIS_DIR.'/MVC/DBFactory.php';
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}
		
		//delete empty entries
		$searchArr = array_filter($searchArr);
		
		$startDate = new DateTime($searchArr['startMnt']);
		$endDate = new DateTime($searchArr['endMnt']);
		
		$res = array();
		$where = "";
		
		if(isset($searchArr['withLst']) AND $searchArr['withLst'] === true)
		{
			
		}
		else
		{
			$where .= " z.isCash = 1 AND ";
		}
		
		//$where = substr($where, 0, -4);
		
		$q = "SELECT k.kundenNummer, k.kndId, khk.kurId, k.anrede, k.vorname, k.name, GROUP_CONCAT(ku.kurName SEPARATOR ';') as kurse"
			." ,k.geburtsdatum, k.handy, k.telefon, k.email, k.strasse, k.strNr, k.plz, k.stadt, z.isCash"
			." FROM kundehatkurse as khk LEFT JOIN kurse as ku USING(kurId) LEFT JOIN kunden as k USING(kndId) 
				LEFT JOIN zahlungsdaten as z USING(kndId)
				WHERE EXTRACT(YEAR_MONTH FROM :curMonth) BETWEEN EXTRACT(YEAR_MONTH FROM khk.von) AND EXTRACT(YEAR_MONTH FROM khk.bis) "
			." AND ".$where
			." (k.kndId,kurId) NOT IN 
				(
					SELECT r1.kndId, rd1.kurId 
					FROM rechnungsdaten as rd1 LEFT JOIN rechnungen as r1 USING(rnId)
					WHERE EXTRACT(YEAR_MONTH FROM r1.rnMonat) = EXTRACT(YEAR_MONTH FROM :curMonth)
				)
				GROUP BY k.kndId
				ORDER BY k.kundenNummer DESC";
		
		$oneMonth = new DateInterval('P1M');
		
		try
		{
			for( ;  $startDate <= $endDate ; $startDate->add( $oneMonth ) )
			{
				$curDate = $startDate;
				//echo $curDate->format('Y-m-d');
				$sth = $dbh->prepare($q);
				$sth->execute(array(':curMonth' => $curDate->format('Y-m-d') ));
				
				//$rr = $sth->fetchAll(PDO::FETCH_ASSOC);
				$res[$curDate->format('Y-m-d')] = $sth->fetchAll(PDO::FETCH_ASSOC);
			}
			//while( $startDate->add(new DateInterval('P1M')) <= $endDate );
			
			return $res;
			
		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
	}
}
