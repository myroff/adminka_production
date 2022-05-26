<?php
namespace Mitarbeiter;
use PDO as PDO;

class Mitarbeiter
{
	public function showList()
	{
		$sArr = array();
		$sArr[':vorname'] = empty($_POST['vorname']) ? '' : $_POST['vorname'];
		$sArr[':name'] = empty($_POST['name']) ? '' : $_POST['name'];

		$res = $this->searchDates($sArr);

		include_once BASIS_DIR.'/Templates/Mitarbeiter/MitarbeiterListe.tmpl.php';
		return;
	}

	private function searchDates($searchArr)
	{

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
		echo "where = " . $where . "<br>";

		$q = "SELECT * FROM mitarbeiter";
		$q .= empty($where) ? '' : " WHERE " . $where;



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
