<?php
namespace Statistik;
use PDO as PDO;

use MVC\DBFactory as DBFactory;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;

class PayDay {
	public function showCashbox() {
		if(!empty($_GET['date']))
		{
			$date = $_GET['date'];
			$month = "";
			$res = $this->getDayDate($date);
		}
		elseif(!empty($_GET['month'])){
			$date = "";
			$month = $_GET['month'];
			$res = $this->getMonthDate($month);
		}
		else{
			$date = "";
			$month = "";
			$res = NULL;
		}

		include_once BASIS_DIR.'/Templates/Statistik/PayDay.tmpl.php';
		return;
	}

	private function getDayDate($date) {

		if(Fltr::isDate($date))
		{
			$dbh = DBFactory::getDBH();
			if(!$dbh)
			{
				return FALSE;
			}

			$d = Fltr::strToSqlDate($date);
			//2015-05-19
			$q = "SELECT r.*, SUM(rndBetrag) as 'summe', m.vorname as 'mtbVorname', m.name as 'mtbName',"
				." k.vorname as 'kndVorname', k.name as 'kndName', pd.payment_id"
				." FROM rechnungen as r LEFT JOIN rechnungsdaten as rd USING(rnId) LEFT JOIN mitarbeiter as m USING(mtId)"
				." LEFT JOIN kunden as k USING(kndId) LEFT JOIN payment_data as pd USING(kndId)"
				." WHERE DATE(r.rnErstelltAm)=:d"
				." GROUP BY r.rnId";

			try
			{
				$sth = $dbh->prepare($q);
				$sth->execute(array(':d' => $d));
				$rs = $sth->fetchAll(PDO::FETCH_ASSOC);

				return $rs;

			} catch (Exception $ex) {
				//print $ex;
				return FALSE;
			}
		}
		else{
			return NULL;
		}
	}

	private function getMonthDate($date) {

		if(preg_match("/\d\d\.\d\d\d\d/", $date))
		{
			$dbh = DBFactory::getDBH();
			if(!$dbh)
			{
				return FALSE;
			}

			$d = Fltr::strToSqlDate("01.".$date);
			//2015-05-19
			$q = "SELECT r.*, SUM(rndBetrag) as 'summe', m.vorname as 'mtbVorname', m.name as 'mtbName',"
				." k.vorname as 'kndVorname', k.name as 'kndName', pd.payment_id"
				." FROM rechnungen as r LEFT JOIN rechnungsdaten as rd USING(rnId) LEFT JOIN mitarbeiter as m USING(mtId)"
				." LEFT JOIN kunden as k USING(kndId) LEFT JOIN payment_data as pd USING(kndId)"
				." WHERE EXTRACT(YEAR_MONTH FROM r.rnErstelltAm) = EXTRACT(YEAR_MONTH FROM :d)"
				." GROUP BY r.rnId"
				." ORDER BY r.rnErstelltAm";

			try
			{
				$sth = $dbh->prepare($q);
				$sth->execute(array(':d' => $d));
				$rs = $sth->fetchAll(PDO::FETCH_ASSOC);

				return $rs;

			} catch (Exception $ex) {
				//print $ex;
				return FALSE;
			}
		}
		else{
			return NULL;
		}
	}
}
