<?php
namespace Lehrer;
use PDO as PDO;

use MVC\DBFactory as DBFactory;

class Verdienst
{
	public function showList()
	{
		$sArr = array();
		$sArr[':vorname'] = empty($_POST['vorname']) ? '' : $_POST['vorname'];
		$sArr[':name'] = empty($_POST['name']) ? '' : $_POST['name'];
		$sArr[':startMnt'] = empty($_POST['startMnt']) ? '' : trim($_POST['startMnt']);
		$sArr[':endMnt'] = empty($_POST['endMnt']) ? '' : trim($_POST['endMnt']);

		$res = $this->searchDates($sArr);

		include_once BASIS_DIR.'/Templates/Lehrer/LehrerVerdienstListe.tmpl.php';
		return;
	}

	private function searchDates($searchArr)
	{
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}

		$where = "";

		//delete empty entries
		$searchArr = array_filter($searchArr);

		if(isset($searchArr[':name']))
		{
			$where .= " name LIKE :name AND";
			$searchArr[':name'] .= '%';
		}

		if(isset($searchArr[':vorname']))
		{
			$where .= " vorname LIKE :vorname AND";
			$searchArr[':vorname'] .= '%';
		}

		if(isset($searchArr[':startMnt']) AND isset($searchArr[':endMnt']))
		{
			$where .= " EXTRACT(YEAR_MONTH FROM rnMonat) BETWEEN EXTRACT(YEAR_MONTH FROM :startMnt) AND EXTRACT(YEAR_MONTH FROM :endMnt) AND";
			$searchArr[':startMnt'] .= '-10';
			$searchArr[':endMnt'] .= '-10';
		}
		elseif(isset($searchArr[':startMnt']) OR isset($searchArr[':endMnt']))
		{
			$where .= " EXTRACT(YEAR_MONTH FROM rnMonat) =";
			$where .= isset($searchArr[':startMnt']) ? " EXTRACT(YEAR_MONTH FROM :startMnt) " : " EXTRACT(YEAR_MONTH FROM :endMnt) ";
			isset($searchArr[':startMnt']) ? $searchArr[':startMnt'] .= '-10' : $searchArr[':endMnt'] .= '-10';
			$where .= " AND";
		}

		$where = substr($where, 0, -4);

		$q = "SELECT r.rnMonat, l.lehrId, l.anrede, l.vorname, l.name, SUM(rndBetrag) as summe"
			." FROM lehrer as l JOIN kurse USING(lehrId) JOIN rechnungsdaten as rd USING(kurId) LEFT JOIN rechnungen as r USING(rnId)";
		$q .= empty($where) ? '' : " WHERE " . $where;
		$q .= " GROUP BY l.lehrId, r.rnMonat"
			." ORDER BY r.rnMonat DESC, l.vorname, l.name";

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
	}//private function searchDates($searchArr)

	public function showLehrersChildren()
	{
		$postData = array();
		$fehler = "";

		if(isset($_POST['lehrId']) AND !empty($_POST['lehrId']))
		{
			$postData['lehrId'] = $_POST['lehrId'];
		}
		else{
			$fehler .= "lehrId fehlt\n";
		}

		if(isset($_POST['rnMonat']) AND !empty($_POST['rnMonat']))
		{
			$postData['rnMonat'] = date('Y-m-d', strtotime($_POST['rnMonat'])) ;
		}
		else{
			$fehler .= "rnMonat fehlt\n";
		}

		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			$fehler .= "dbFactory failed.\n";
		}
	//fehler
		if(!empty($fehler))
		{
			$output = array('status' => "error", "message" => $fehler);
			header("Content-type: application/json");
			exit(json_encode($output));
		}

		$q =
"SELECT ku.kurId, ku.kurName, rn.*, rnd.*, knd.vorname, knd.name, knd.kndId, knd.kundenNummer
FROM lehrer as l JOIN kurse as ku USING(lehrId) LEFT JOIN rechnungsdaten as rnd USING(kurId) JOIN rechnungen as rn USING(rnId) LEFT JOIN kunden as knd USING(kndId)
WHERE l.lehrId=:lehrId AND EXTRACT(YEAR_MONTH FROM rn.rnMonat) = EXTRACT(YEAR_MONTH FROM :rnMonat)
ORDER BY knd.name";
		$rs = array();

		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute($postData);
			$rs = $sth->fetchAll(PDO::FETCH_ASSOC);
		}
		catch (Exception $ex) {
			$output = array('status' => "error", "message" => $ex);
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		$count = count($rs);
		$out = "Stunden-Schüler = $count";
		$out .= "<table class='standardTable'><tr><th>kndNr</th><th>Name</th><th>Vorname</th><th>Kurs</th><th>Betrag</th><th>Datum</th></tr>";
		$summe = 0;
		foreach($rs as $r)
		{
			$out .= "<tr>";
			$out .= "<td>".$r['kundenNummer'];"</td>";
			$out .= "<td>".$r['name']."</td>";
			$out .= "<td>".$r['vorname']."</td>";
			$out .= "<td>".$r['kurName']."</td>";
			$betrag = is_null($r['rndBetrag']) ? '-' : $r['rndBetrag'];
			$out .= "<td>$betrag</td>";
			$out .= "<td>".$r['rnErstelltAm']."</td>";
			$out .= "</tr>";
			//$out .= ""
			$summe += (double) $r['rndBetrag'];
		}
		$out .= "</table>";
		$out .= "Summe = $summe";

		$output = array('status' => "ok", "message" => $out);
		header("Content-type: application/json");
		exit(json_encode($output));
	}//public function showLehrersChildren()
}
/*
date('i', strtotime($res[$i]['anfang']))
$out .= "<td>".date('d.m.Y', strtotime($r['von']))."-".date('d.m.Y', strtotime($r['bis']))."</td>";
SELECT k.kndId, k.name, k.vorname, rd.rndBetrag, krs.kurName, khk.von, khk.bis
FROM kunden as k LEFT JOIN rechnungen as rn USING(kndId) JOIN rechnungsdaten as rd USING(rnId) LEFT JOIN kurse as krs USING(kurId) LEFT JOIN kundehatkurse as khk USING(kurId)
WHERE EXTRACT(YEAR_MONTH FROM rn.rnMonat) = EXTRACT(YEAR_MONTH FROM '2015-08-14') AND lehrId = '2'

SELECT knd.vorname, knd.name, rn.rnMonat, rnd.rndBetrag, ku.kurName, rn.rnErstelltAm, khk.eintrId
FROM lehrer as l LEFT JOIN kurse as ku USING(lehrId) LEFT JOIN kundehatkurse as khk USING(kurId) LEFT JOIN kunden as knd USING(kndId) LEFT JOIN rechnungen as rn USING(kndId) LEFT JOIN rechnungsdaten as rnd USING(rnId)
WHERE EXTRACT(YEAR_MONTH FROM rn.rnMonat) = EXTRACT(YEAR_MONTH FROM '2015-08-14') AND l.lehrId = '2'

SELECT l.name, ku.kurName, knd.vorname, knd.name
FROM lehrer as l JOIN kurse as ku USING(lehrId) JOIN kundehatkurse as khk USING(kurId) JOIN kunden as knd USING(kndId)
WHERE l.lehrId='2'

SELECT ku.kurId, knd.kndId, knd.vorname, knd.name, ku.kurName, rn.rnErstelltAm
FROM lehrer as l JOIN kurse as ku USING(lehrId) JOIN kundehatkurse as khk USING(kurId) JOIN kunden as knd USING(kndId)
LEFT JOIN rechnungen as rn USING(kndId)
LEFT JOIN rechnungsdaten as rnd USING(rnId,kurId)
WHERE l.lehrId='2' AND EXTRACT(YEAR_MONTH FROM rn.rnMonat) = EXTRACT(YEAR_MONTH FROM '2015-08-14')
ORDER BY kndId
 */