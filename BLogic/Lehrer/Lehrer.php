<?php
namespace Lehrer;
use PDO as PDO;

class Lehrer
{
	public function showList()
	{
		$sArr = array();
		$sArr[':vorname'] = empty($_POST['vorname']) ? '' : $_POST['vorname'];
		$sArr[':name'] = empty($_POST['name']) ? '' : $_POST['name'];
		
		$res = $this->searchDates($sArr);
		
		include_once BASIS_DIR.'/Templates/Lehrer/LehrerListe.tmpl.php';
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
		
		foreach ($searchArr as $key => $value)
		{
			$where .= substr($key, 1) . " LIKE ";
			$where .= $key;
			$searchArr[$key] .= "%";
			$where .= " AND ";
		}
		
		$where = substr($where, 0, -5);
		
		$q = "SELECT * FROM lehrer";
		$q .= empty($where) ? '' : " WHERE " . $where;
		$q .= " GROUP BY name, vorname";
                
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
