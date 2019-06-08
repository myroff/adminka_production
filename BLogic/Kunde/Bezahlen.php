<?php
namespace Kunde;
use PDO as PDO;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
require_once BASIS_DIR.'/Tools/User.php';
use Tools\User as User;
require_once BASIS_DIR.'/BLogic/Rechnung/Rechnung2Pdf.php';
use Rechnung\Rechnung2Pdf as Rechnung2Pdf;

class Bezahlen {
	public function showKundeById($kId)
	{
		require_once BASIS_DIR.'/MVC/DBFactory.php';
        $dbh = \MVC\DBFactory::getDBH();
        if(!$dbh)
        {
            echo "no Connection to DB";
            return FALSE;
        }
		
		$q = "SELECT k.*, zd.*, GROUP_CONCAT(empf.vorname,' ', empf.name) as empfohlenDurch"
			. " FROM kunden as k LEFT JOIN zahlungsdaten as zd USING(kndId)"
			. " LEFT JOIN kunden as empf ON empf.kndId=k.empfohlenId"
			. " WHERE k.kndId=:kndId";
		
		$qk = "SELECT khk.*, k.*, l.name as 'lehrName', l.vorname as 'lehrVorname'"
				.", group_concat('{\"wochentag\":\"',st.wochentag,'\",\"time\":\"', TIME_FORMAT(st.anfang, '%H:%i'),' - ', TIME_FORMAT(st.ende, '%H:%i'),'\"}' SEPARATOR',') as termin"
			. " FROM kundehatkurse as khk LEFT JOIN kurse as k USING(kurId) LEFT JOIN lehrer as l USING(lehrId)"
			. " LEFT JOIN stundenplan as st USING(kurId)"
			. " WHERE khk.kndId=:kndId"
			. " GROUP BY khk.eintrId"
			. " ORDER By khk.eintrId DESC";
		
		$qr = "SELECT r.*, SUM(rndBetrag) as summe, group_concat(k.kurName SEPARATOR '; ') as kurse"
			." FROM rechnungen as r LEFT JOIN rechnungsdaten as rnd USING(rnId)"
			." LEFT JOIN kurse as k USING (kurId)"
			." WHERE kndId=:kndId"
			." GROUP BY r.rnId"
			." ORDER BY r.rnMonat DESC, r.rnId DESC";
		
		$qEmpf = "SELECT kndId, kundenNummer, vorname, name FROM kunden WHERE empfohlenId=:kndId";
		
		$kndAr = array(":kndId" => $kId);
		
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute($kndAr);
			$res = $sth->fetch(PDO::FETCH_ASSOC, 1);
			
			$sth = $dbh->prepare($qk);
			$sth->execute($kndAr);
			$ures = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			$sth = $dbh->prepare($qr);
			$sth->execute($kndAr);
			$rres = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			$sth = $dbh->prepare($qEmpf);
			$sth->execute($kndAr);
			$empfRes = $sth->fetchAll(PDO::FETCH_ASSOC);
		}
		catch (Exception $ex) {
			print $ex;
			return FALSE;
		}
		
		include_once BASIS_DIR .'/Templates/Kunde/KundeBezahlenById.tmpl.php';
		return;
	}
	
	public function ajaxGetFormular()
	{
		$kndId = "";
		$fehler = "";
		$output = array();
		
		if(!isset($_POST['kndId']) OR empty($_POST['kndId']))
		{
			$fehler .= "kndId fehlt.\n";
		}
		else
		{
			$kndId = Fltr::deleteSpace($_POST['kndId']);
			if(!Fltr::isInt($_POST['kndId']))
			{
				$fehler .= "kndId ist kein Integer";
			}
		}
		require_once BASIS_DIR.'/MVC/DBFactory.php';
        $dbh = \MVC\DBFactory::getDBH();
        if(!$dbh)
        {
            echo "no Connection to DB";
            return FALSE;
        }
		$eIds = '';
		foreach ($_POST['eIds'] as $e)
		{
			$eIds .= Fltr::deleteSpace($e);
			$eIds .= ',';
		}
		$eIds = substr($eIds, 0, -1);
		$qe = "SELECT * FROM kundehatkurse as khk LEFt JOIN kurse as k USING(kurId) WHERE kndId=$kndId AND eintrId IN ($eIds)";
		$res = array();
		
		try
		{
			$sth = $dbh->query($qe);
			$res = $sth->fetchAll(PDO::FETCH_ASSOC);
			
		} catch (Exception $ex) {
			$output['status'] = 'pdo: error';
			$output['message'] = $ex;
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$kurse = "<table>";
		$kurse .= "<th>Kurs</th><th>Preis</th><th>SonderPreis</th><th>Betrag</th>";
		foreach($res as $r)
		{
			$kurse .= "<tr>";
			//Name
			$kurse .= "<td>".$r['kurName']."</td>";
			//Preis
			$kurse .= "<td>".$r['kurPreis'];
			$kurse .= $r['kurIsStdPreis'] > 0 ? ' pro Stunde' : ' pro Monat';
			$kurse .= "</td>";
			//SonderPreis
			$kurse .= "<td>";
			if(!empty($r['sonderPreis']))
			{
				$kurse .= $r['sonderPreis'];
				$kurse .= $r['kurIsStdPreis'] > 0 ? ' pro Stunde' : ' pro Monat';
			}
			$kurse .= "</td>";
			//Betrag
			$v = $r['sonderPreis'] ? $r['sonderPreis'] : $r['kurPreis'];
			$kurse .= "<td><input class='inputPreis' type='text' name='kurId[".$r['kurId']."]' value='$v' /></td>";
			$kurse .= "</tr>";
		}
		$kurse .= "</table>";
		
		$output['status'] = 'ok';
		$output['kurseTable'] = $kurse;
		header("Content-type: application/json");
		exit(json_encode($output));
	}
	
	public function ajaxConfirmRechnung()
	{
		$fehler = "";
		$output = array();
		$kndId; $rnMonat; $rnKomm;
		$kurId = array();
		
		if(!isset($_POST['kndId']) OR empty($_POST['kndId']))
		{
			$fehler .= "kndId fehlt.\n";
		}
		else
		{
			$kndId = Fltr::deleteSpace($_POST['kndId']);
			if(!Fltr::isInt($_POST['kndId']))
			{
				$fehler .= "kndId ist kein Integer.\n";
			}
		}
		
		if(!isset($_POST['rnMonat']) OR empty($_POST['rnMonat']))
		{
			$fehler .= "Monat und Jahr fehlen.\n";
		}
		else
		{
			$rnMonat = Fltr::deleteSpace($_POST['rnMonat']);
			if(preg_match("/\d\d\.\d\d\d\d/", $_POST['rnMonat']))
			{
				$rnMonat = "10.".$_POST['rnMonat'];
				if(Fltr::isDate($rnMonat))
				{
					$rnMonat = Fltr::strToSqlDate($rnMonat);
				}
				else
				{
					$fehler .= "Das Datum existiert nicht: $rnMonat\n";
				}
			}
			else
			{
				$fehler .= "Zahlungsmonat sit falsch eingegeben. (z.B. 09.2020)\n";
			}
		}
		
		if(isset($_POST['rnKomm']) OR !empty($_POST['rnKomm']))
		{
			$rnKomm = Fltr::filterStr($_POST['rnKomm']);
		}
	//Summe der Beträgen wird zugeordnet
		if(!isset($_POST['kurId']) OR empty($_POST['kurId']))
		{
			$fehler .= "Kurs-IDs fehlen.\n";
		}
		else
		{
			foreach($_POST['kurId'] as $k=>$v)
			{
				$tk = Fltr::deleteSpace($k);
				$tv = Fltr::deleteSpace($v);
				if(Fltr::isInt($tk) AND Fltr::isPrice($tv))
				{
					$kurId[$tk] = $tv;
				}
				else
				{
					$fehler .= "Falscher KursID oder Betrag: $tk => $tv\n";
				}
			}
		}
		
		if(!empty($fehler))
		{
			$output['status'] = 'Fehler.';
			$output['info'] = $fehler;
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		require_once BASIS_DIR.'/MVC/DBFactory.php';
        $dbh = \MVC\DBFactory::getDBH();
        if(!$dbh)
        {
            echo "no Connection to DB";
            return FALSE;
        }
		
		$res = array();
		$tln = array();
		$kurIds = "";
		$summe = 0;
		foreach($kurId as $k=>$v)
		{
			$v = str_replace(',', '.', $v);
			$kurIds .= "$k,";
			$summe += (double)$v;
		}
		$kurIds = substr($kurIds, 0, -1);
		
		$q = "SELECT * FROM kurse WHERE kurId IN($kurIds)";
		
		$q2 = "SELECT * FROM kunden WHERE kndId=$kndId";
		
		try
		{
			$sth = $dbh->query($q);
			$res = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			$sth = $dbh->query($q2);
			$tln = $sth->fetch(PDO::FETCH_ASSOC, 1);
			
		} catch (Exception $ex){
			print($ex);
		}
		
		$curMtb = User::getCurrentUserInfo();
		
		include_once BASIS_DIR .'/Templates/Formulare/ConfirmQuittung.01.tmpl.php';
		return;
	}
	
	public function ajaxSaveRechnung()
	{
		$fehler = "";
		$output = array();
		$kndId; $rnMonat; $rnKomm;
		$kurId = array();
		
		if(!isset($_POST['kndId']) OR empty($_POST['kndId']))
		{
			$fehler .= "kndId fehlt.\n";
		}
		else
		{
			$kndId = Fltr::deleteSpace($_POST['kndId']);
			if(!Fltr::isInt($_POST['kndId']))
			{
				$fehler .= "kndId ist kein Integer.\n";
			}
		}
		
		if(!isset($_POST['rnMonat']) OR empty($_POST['rnMonat']))
		{
			$fehler .= "Monat und Jahr fehlen.\n";
		}
		else
		{
			$rnMonat = Fltr::deleteSpace($_POST['rnMonat']);
			if(preg_match("/\d\d\.\d\d\d\d/", $_POST['rnMonat']))
			{
				$rnMonat = "10.".$_POST['rnMonat'];
				if(Fltr::isDate($rnMonat))
				{
					$rnMonat = Fltr::strToSqlDate($rnMonat);
				}
				else
				{
					$fehler .= "Das Datum existiert nicht: $rnMonat\n";
				}
			}
			else
			{
				$fehler .= "Zahlungsmonat sit falsch eingegeben. (z.B. 09.2020)\n";
			}
		}
		
		if(isset($_POST['rnKomm']) OR !empty($_POST['rnKomm']))
		{
			$rnKomm = Fltr::filterStr($_POST['rnKomm']);
		}
		
		if(!isset($_POST['kurId']) OR empty($_POST['kurId']))
		{
			$fehler .= "Kurs-IDs fehlen.\n";
		}
		else
		{
			foreach($_POST['kurId'] as $k=>$v)
			{
				$tk = Fltr::deleteSpace($k);
				$tv = str_replace(',', '.', Fltr::deleteSpace($v) );
				if(Fltr::isInt($tk) AND Fltr::isPrice($tv))
				{
					$kurId[$tk] = $tv;
				}
				else
				{
					$fehler .= "Falscher KursID oder Betrag: $tk => $tv\n";
				}
			}
		}
		
		$mtId = User::getCurrentUserId();
		if(!$mtId)
		{
			$fehler .= "Aktueller mitarbeiter konnte nicht festgestellt werden (mtId).";
		}
		
		if(!empty($fehler))
		{
			$output['status'] = 'Fehler.';
			$output['info'] = $fehler;
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		require_once BASIS_DIR.'/MVC/DBFactory.php';
        $dbh = \MVC\DBFactory::getDBH();
        if(!$dbh)
        {
            echo "no Connection to DB";
            return FALSE;
        }
		
		$res = array();
		$tln = array();
		$kurIds = "";
		$summe = 0;
		
		foreach($kurId as $k=>$v)
		{
			$kurIds .= "$k,";
			$summe += (int)$v;
		}
		$kurIds = substr($kurIds, 0, -1);
		
		$q = "INSERT INTO rechnungen (kndId, rnMonat, `rnKomm`, `mtId`, `rnErstelltAm`)"
				. " VALUES ('$kndId', '$rnMonat', '$rnKomm', '$mtId',NOW())";
		
		$q2 = "INSERT INTO rechnungsdaten (rnId, kurId, rndBetrag) VALUES (:rnId, :kurId, :rnBetrag)";
		$rnId;
		try
		{#die($q);
			$dbh->beginTransaction();
			$res = $dbh->exec($q);

			if($res>0)
			{
				
				$rnId = $dbh->lastInsertId();
				$sth = $dbh->prepare($q2);
				$sth->bindValue(':rnId', $rnId);
				
				foreach($kurId as $k=>$v)
				{
					$sth->bindValue(':kurId', $k);
					$sth->bindValue(':rnBetrag', $v);
					$sth->execute();
				}
				
				$dbh->commit();
				
				$output['status'] = "ok";
				$output['info'] = "[DB] Neue Rechnung wurde erfolgreich gespeichert.";
//send pdfUrl
				$pdfUrl = Rechnung2Pdf::saveRechnungToPdf($rnId);//$rnId
				$output['pdfUrl'] = $pdfUrl;				
			}
			else
			{
				$output['status'] = "Fehler.";
				$output['info'] = "[DB] Neue Rechnung konnte nicht gespeichert werden. Rechnungsdaten werden nicht in DB hingefügt.";
				$dbh->rollBack();
			}
			
		} catch (Exception $ex) {
			
			$output['status'] = "Fehler.";
			$output['info'] = $ex;
			header("Content-type: application/json");
			exit(json_encode($output));
		}

		header("Content-type: application/json");
		exit(json_encode($output));
	}
}