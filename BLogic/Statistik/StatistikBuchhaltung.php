<?php
namespace Statistik;
use PDO as PDO;

use MVC\DBFactory as DBFactory;

class StatistikBuchhaltung {
	public function getStat()
	{
		$rres = $this->getRechnungSumme();

		include_once BASIS_DIR.'/Templates/Statistik/StatistikBuchhaltung.tmpl.php';
		return;
	}

	private function getRechnungSumme()
	{
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}

		$q = "SELECT rnMonat, SUM(rndBetrag) as summe, SUM(kurPreis) as summeKurPreis,"
			." SUM(IF(pd.payment_id=1, rndBetrag, 0)) as barSumme,"
			." SUM(IF(pd.payment_id=0, rndBetrag, 0)) as lastschriftSumme"
			." FROM rechnungen LEFT JOIN rechnungsdaten USING(rnId)  LEFT JOIN kurse USING(kurId)"
			." LEFT JOIN payment_data as pd USING(kndId)"
			." GROUP BY YEAR(rnMonat), MONTH(rnMonat)"
			." ORDER BY YEAR(rnMonat) DESC, MONTH(rnMonat) DESC";

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
