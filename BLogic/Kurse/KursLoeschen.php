<?php
namespace Kurse;
use PDO as PDO;

use MVC\DBFactory as DBFactory;

class KursLoeschen
{
	public function ajaxDeleteKurs()
	{
		if(empty($_POST['kurId']) OR !isset($_POST['kurId']))
		{
			$output['status'] = "error";
			$output['message'] = "Es wurde kein 'kurId' übergeben.";
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		else
		{
			$kurId = $_POST['kurId'];
		}

		$qTestKunden = "SELECT k.kndId, k.vorname, k.name FROM kunden as k LEFT JOIN kundehatkurse as khk USING(kndId) WHERE khk.kurId=:kurId";

		$qTestRechnungen = "SELECT r.*, rd.*, k.kurName, knd.vorname, knd.name
FROM rechnungen as r JOIN rechnungsdaten as rd USING(rnId) LEFT JOIN kurse as k USING(kurId) LEFT JOIN kunden as knd USING(kndId)
WHERE rd.kurId=:kurId";

		$qDelete = "DELETE FROM kurse WHERE kurId=:kurId;DELETE FROM stundenplan WHERE kurId=:kurId;";

		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			$output['status'] = "error";
			$output['message'] = "kein DBH.";
			header("Content-type: application/json");
			exit(json_encode($output));
		}

		try
		{
			$mess = "";

			$sth = $dbh->prepare($qTestKunden);
			$sth->execute(array(":kurId" => $kurId));
			$testKunden = $sth->fetchAll(PDO::FETCH_ASSOC);

			$sth = $dbh->prepare($qTestRechnungen);
			$sth->execute(array(":kurId" => $kurId));
			$testRechnungen = $sth->fetchAll(PDO::FETCH_ASSOC);

			if(!empty($testKunden))
			{
				$output['status'] = "error";
				$mess .= "Entfernen dieses Kurses ist nicht möglich. Einige Kunden nehmen noch daran teil.<br>";
				$mess .= "<table>";

				foreach ($testKunden as $k)
				{
					$mess .= "<tr>";
					$mess .= "<td>".$k['vorname']." ".$k['name']."</td>";
					$mess .= "</tr>";
				}

				$mess .= "</table>";

			}

			if(!empty($testRechnungen))
			{
				$output['status'] = "error";
				$mess .= "Entfernen dieses Kurses ist nicht möglich. Einige Rechnungen hängen noch daran.<br>";
				$mess .= "<table>";

				foreach ($testRechnungen as $r)
				{
					$mess .= "<tr>";
					$mess .= "<td>".$r['vorname']."</td><td>".$r['name']."</td>";
					$mess .= "<td>".$r['kurName']."</td>";
					$mess .= "<td>".date("H:i:s d.m.Y", strtotime($r['rnErstelltAm']))."</td>";
					$mess .= "<td>".$r['rndBetrag']." €</td>";
					$mess .= "<td>".$r['rnKomm']."</td>";
					$mess .= "</tr>";
				}

				$mess .= "</table>";
			}

			if(empty($testKunden) AND empty($testRechnungen))
			{
				$sth = $dbh->prepare($qDelete);
				$res = $sth->execute(array(":kurId" => $kurId));
				if($res>0)
				{
					$output['status'] = "ok";
					$output['message'] = "Der Kurs wurde erfolgreich entfernt.";
				}
				else
				{
					$output['status'] = "error";
					$output['message'] = "Ein Eintrag mit dem kurId wurde nicht gefunden.";
				}

				header("Content-type: application/json");
				exit(json_encode($output));
			}

			$output['status'] = "error";
			$output['message'] = $mess;
			header("Content-type: application/json");
			exit(json_encode($output));

		} catch (Exception $ex) {
			$output['status'] = "error";
			$output['message'] = "Fehler beim DB-Operationen.".$ex;
			header("Content-type: application/json");
			exit(json_encode($output));
		}
	}
}
