<?php
namespace Tools;
use PDO as PDO;

class PaidMonthes
{
	static public function getTable($kId)
	{
		$sMonth = 8; $sYear = 2015;
		$eMonth = 7; $eYear = 2016;

		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			echo '[PaidMonthes] Verbindung mit dem Datenbank ist fehlgeschlagen.';
		}

		$qrn = "SELECT r.rnId, MONTH(r.rnMonat) as month, YEAR(r.rnMonat) as year, r.rnKomm , SUM(rd.rndBetrag) as summe"
			.", GROUP_CONCAT(k.kurName,': ', rd.rndBetrag, '€' SEPARATOR '<br>') as kurse"
			." FROM rechnungen as r LEFT JOIN rechnungsdaten as rd USING(rnId) LEFT JOIN kurse as k USING(kurId)"
			." WHERE kndId=:kndId"
			." GROUP BY rnId";

		$qt = "SELECT MIN(von) as startDate, MAX(bis) as endDate, MONTH(MIN(von)) as anfangMonat, YEAR(MIN(von)) as anfangJahr, MONTH(MAX(bis)) as endMonat, YEAR(MAX(bis)) as endJahr, MAX(bis) as ende"
			. " FROM kundehatkurse WHERE kndId=:kndId GROUP BY kndId";

		try
		{
		//Rechnungen
			$sth = $dbh->prepare($qrn);
			$sth->execute(array(":kndId" => $kId));
			$rnres = $sth->fetchAll(PDO::FETCH_ASSOC);

			$sth = $dbh->prepare($qt);
			$sth->execute(array(":kndId" => $kId));
			$tr = $sth->fetch(PDO::FETCH_ASSOC,1);

		} catch (Exception $ex) {
			print $ex;
			return FALSE;
		}

		$contractBegin = strtotime($tr['startDate']);
		$ersteAnmeldung = date('d.m.Y', $contractBegin);
		$contractBegin = date('Y-m-d', $contractBegin);

		$contractEnd = strtotime($tr['endDate']);
		$anmeldungEndet = date('d.m.Y', $contractEnd);
		$contractEnd = date('Y-m-d', $contractEnd);

		//current month and year
		$curMnt = (int)date('m');
		$curY = (int)date('Y');

		echo "<h2>Bezahlungen</h2>";
		echo "<p>Erste Anmeldung: $ersteAnmeldung<br>Anmeldung endet: $anmeldungEndet</p>";

		echo "<table id='mntListe' style=''>";

		for($y=$sYear; $y<=$eYear; $y++)
		{
			echo "<tr><th colspan='12'>$y</th></tr>";

			echo "<tr>";

			for($m=1; $m<13; $m++)
			{
				$color = "";
				$content = "";
				$style = "";
				if($y >= (int)$tr['anfangJahr'] AND $y <= (int)$eYear)
				{
					$curMonth = strtotime("10.$m.$y");
					$curMonth = date('Y-m-d', $curMonth);

					if( ($m === (int)$tr['anfangMonat'] AND $y === (int)$tr['anfangJahr'])
						OR ($m === (int)$tr['endMonat'] AND $y === (int)$tr['endJahr'])
						OR ($curMonth > $contractBegin AND $curMonth < $contractEnd) )
					{
						$style = "background:red;";
						foreach($rnres as $r)
						{
							if($m === (int)$r['month'] AND $y === (int)$r['year'])
							{
								$style = "background:green;";
								$content .= "<p>".$r['kurse']."<hr>Summe: ".$r['summe']."€</p><p>".$r['rnKomm']."</p>";
							}
						}
					}

				}
				else
				{

				}
				if($m === $curMnt AND $y === $curY)
				{
					$style .= "border:3px solid #FFFF00;";
				}

				echo "<td style='$style' >$m $content</td>";
			}
			echo "</tr>";
		}

		echo "</table>";
	}

	public static function hasDebt($kId)
	{
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return false;
		}

		$qt = "SELECT MIN(von) as startDate, MAX(bis) as endDate, MONTH(MIN(von)) as anfangMonat, YEAR(MIN(von)) as anfangJahr, MONTH(MAX(bis)) as endMonat, YEAR(MAX(bis)) as endJahr, MAX(bis) as ende"
			. " FROM kundehatkurse WHERE kndId=:kndId GROUP BY kndId";
		$qk = "SELECT * FROM kundehatkurse WHERE kndId=:kndId";
		$qq = "SELECT rd.*, r.kndId, k.kurName, r.rnMonat, r.rnKomm
FROM rechnungsdaten as rd LEFT JOIN rechnungen as r USING(rnId) LEFT JOIN kurse as k USING(kurId)
WHERE kndId=151";
		try
		{
		//Anfang und Ende der Anmeldung
			$sth = $dbh->prepare($qt);
			$sth->execute(array(":kndId" => $kId));
			$tr = $sth->fetch(PDO::FETCH_ASSOC,1);

		} catch (Exception $ex) {
			print $ex;
			return FALSE;
		}

		$startMnt = (int)$tr['anfangMonat'];
		$startJhr = (int)$tr['anfangJahr'];

		$curMnt = (int)date('m');
		$curY = (int)date('Y');


	}

}
