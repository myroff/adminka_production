<?php
namespace Kurse;
use PDO as PDO;

class Kurse
{
	public function showList()
	{
		$sArr = array();
		$sArr[':kurName'] = empty($_POST['kurName']) ? '' : $_POST['kurName'];
		$sArr[':kurAlter'] = empty($_POST['kurAlter']) ? '' : $_POST['kurAlter'];
		$sArr[':kurKlasse'] = empty($_POST['kurKlasse']) ? '' : $_POST['kurKlasse'];
		
		$res = $this->searchDates($sArr);
		
		include_once BASIS_DIR.'/Templates/Kurse/KurseListe.tmpl.php';
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
		
		$where = "";
		
		//delete empty entries
		$searchArr = array_filter($searchArr);
		$q = "SELECT k.*, l.vorname, l.name, st.raum, st.wochentag,"
			. " TIME_FORMAT(anfang, '%H:%i') as anfang, TIME_FORMAT(ende, '%H:%i') as ende, count(khk.kndId) as anzTeilnehmer,"
			. " group_concat(knd.vorname,' ',knd.name SEPARATOR ',<br>\n') as tlnm_liste"
			. " FROM kurse as k LEFT JOIN stundenplan as st USING(kurId) LEFT JOIN lehrer as l USING(lehrId)"
			. " LEFT JOIN kundehatkurse as khk USING(kurId) LEFT JOIN kunden as knd USING(kndId)";

		if(!empty($searchArr))
		{
			if(isset($searchArr[':kurName']))
			{
				$where .= " kurName LIKE :kurName AND";
				$searchArr[':kurName'] .= "%";
			}
			if(isset($searchArr[':kurAlter']))
			{
				$where .= " :kurAlter BETWEEN kurMinAlter AND kurMaxAlter AND";
			}
			if(isset($searchArr[':kurKlasse']))
			{
				$where .= " :kurKlasse BETWEEN kurMinKlasse AND kurMaxKlasse AND";
			}
			
			$where = substr($where, 0, -4);
			$q .= empty($where) ? '' : " WHERE " . $where;
		}
		
		$q .= " GROUP BY k.kurId"
			. " ORDER BY wochentag, anfang, raum";
		
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
