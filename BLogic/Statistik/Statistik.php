<?php
namespace Statistik;
use PDO as PDO;

use MVC\DBFactory as DBFactory;

class Statistik {
	public function getStat()
	{
		$sArr = array();
		$sArr[':season']    = empty($_GET['s_season'])  ? '' : $_GET['s_season'];

		$kres = $this->getKundenDaten($sArr);
		$pres = $this->getPopulationStat($sArr);
		$ares = $this->getAgeStat($sArr);

		include_once BASIS_DIR.'/Templates/Statistik/Statistik.tmpl.php';
		return;
	}

	private function getKundenDaten($sArr)
	{
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}

		$condition = [':season' => '%'];

		if($sArr[':season'])
		{
			$condition = [':season' => $sArr[':season']];
		}

		$q = "SELECT 'countKunde' as key1, count(t.kndId) as value1
FROM
(SELECT kndId FROM kundehatkurse WHERE season_id LIKE :season GROUP BY kndId ) as t
UNION
(SELECT 'countKurs', count(eintrId) as value1 FROM kundehatkurse WHERE season_id LIKE :season)
UNION
(SELECT 'countHerren', count(DISTINCT(k.kndId)) as value1 FROM kunden as k JOIN kundehatkurse as khk USING(kndId) WHERE anrede LIKE 'Herr' AND season_id LIKE :season)
UNION
(SELECT 'countFrauen', count(DISTINCT(k.kndId)) as value1 FROM kunden as k JOIN kundehatkurse as khk USING(kndId) WHERE anrede LIKE 'Frau' AND season_id LIKE :season)
UNION
(SELECT 'countAlle', count(*) as value1 FROM kunden)";
/*
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
*/
		$res = array();

		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute($condition);
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

	private function getPopulationStat($sArr)
	{
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}

		$condition = [':season' => '%'];

		if($sArr[':season'])
		{
			$condition = [':season' => $sArr[':season']];
		}

		$q = "(SELECT stadt, count(*) as population FROM kunden WHERE kndID IN (SELECT kndId FROM kundehatkurse WHERE season_id LIKE :season GROUP BY kndId)"
				." GROUP BY stadt ORDER BY population DESC)"
			. " UNION "
			. " (SELECT 'Gesammt', count(*) FROM kunden WHERE kndId IN (SELECT kndId FROM kundehatkurse WHERE season_id LIKE :season GROUP BY kndId))";

		$res = array();

		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute($condition);
			$res = $sth->fetchAll(PDO::FETCH_ASSOC);

			return $res;

		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
	}

	private function getAgeStat($sArr)
	{
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}

		$condition = [':season' => '%'];

		if($sArr[':season'])
		{
			$condition = [':season' => $sArr[':season']];
		}

		$q = "SELECT TIMESTAMPDIFF(YEAR,geburtsdatum,CURDATE()) AS age, count(kndId) as number"
			." FROM kunden"
			." WHERE kndId IN (SELECT kndId FROM kundehatkurse WHERE season_id LIKE :season GROUP BY kndId)"
			." GROUP BY age"
			." ORDER BY age ASC";
		$res = array();

		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute($condition);
			$res = $sth->fetchAll(PDO::FETCH_ASSOC);

			return $res;

		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
	}

}
