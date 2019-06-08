<?php
namespace Kunde;
use PDO as PDO;
require_once BASIS_DIR.'/MVC/DBFactory.php';
use MVC\DBFactory as DBFactory;

class KundeLoeschen
{
	public function ajaxDeleteKunde()
	{
		if(empty($_POST['kndId']) OR !isset($_POST['kndId']))
		{
			$output['status'] = "error";
			$output['message'] = "Es wurde kein 'kndId' übergeben.";
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		else
		{
			$kndId = $_POST['kndId'];
		}
		
		$qTestKurse = "SELECT ku.kurName, khk.von, khk.bis, khk.khkKomm
FROM kundehatkurse as khk LEFT JOIN kurse as ku USING(kurId)
WHERE khk.kndId=:kndId";
		
		$qTestRechnungen = "SELECT r.*, rd.*, k.kurName, knd.vorname, knd.name
FROM rechnungen as r JOIN rechnungsdaten as rd USING(rnId) LEFT JOIN kurse as k USING(kurId) LEFT JOIN kunden as knd USING(kndId)
WHERE r.kndId=:kndId";
		
		$qDelete = "DELETE FROM kunden WHERE kndId=:kndId";
		
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
			
			$sth = $dbh->prepare($qTestKurse);
			$sth->execute(array(":kndId" => $kndId));
			$testKurse = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			$sth = $dbh->prepare($qTestRechnungen);
			$sth->execute(array(":kndId" => $kndId));
			$testRechnungen = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			if(!empty($testKurse))
			{
				$output['status'] = "error";
				$mess .= "Entfernen dieses Kundes ist nicht möglich. Der nimmt noch an einigen Kursen teil.<br>";
				$mess .= "<table>";
				
				foreach ($testKurse as $k)
				{
					$mess .= "<tr>";
					$mess .= "<td>".$k['kurName']." ".date("d.m.Y", strtotime($k['von']))."-".date("d.m.Y", strtotime($k['von']))."</td>";
					$mess .= "</tr>";
					if(!empty($k['khkKomm']))
					{
						$mess .= "<tr>";
						$mess .= "<td>".$k['khkKomm']."</td>";
						$mess .= "</tr>";
					}
				}
				
				$mess .= "</table>";
				
			}
			
			if(!empty($testRechnungen))
			{
				$output['status'] = "error";
				$mess .= "Entfernen dieses Kundes ist nicht möglich. Einige Rechnungen hängen noch daran.<br>";
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
			
			if(empty($testKurse) AND empty($testRechnungen))
			{
				$sth = $dbh->prepare($qDelete);
				$res = $sth->execute(array(":kndId" => $kndId));
				if($res>0)
				{
					$output['status'] = "ok";
					$output['message'] = "Der Kunde wurde erfolgreich entfernt.";
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
