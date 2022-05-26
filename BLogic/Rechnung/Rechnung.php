<?php
namespace Rechnung;
use PDO as PDO;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;

class Rechnung
{
	public function ajaxShowRechnung()
	{
		$fehler = "";
		$output = array();
		$dataPost = array();
		$rnId;

		if(!isset($_POST['rnId']) OR empty($_POST['rnId']))
		{
			$fehler .= "rnId fehlt.\n";
		}
		else
		{
			$rnId = Fltr::deleteSpace($_POST['rnId']);
			if(!Fltr::isInt($_POST['rnId']))
			{
				$fehler .= "rnId ist kein Integer.\n";
			}
		}

		if(!empty($fehler))
		{
			echo '<div>$fehler</div>';
			return false;
		}


        $dbh = \MVC\DBFactory::getDBH();
        if(!$dbh)
        {
            echo "no Connection to DB";
            return FALSE;
        }

		$q = "SELECT * FROM rechnungsdaten as r LEFT JOIN kurse as k USING(kurId) WHERE rnId=:rnId";
		$q2 = "SELECT r.*, SUM(rd.rndBetrag) as summe FROM rechnungen as r LEFT JOIN rechnungsdaten as rd USING(rnId) WHERE r.rnId=:rnId";
		$qm = "SELECT vorname, name FROM mitarbeiter WHERE mtId=:mtId";

		$rn = array();
		$rd =array();
		$mt = array();

		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute(array(':rnId' => $rnId));
			$rd = $sth->fetchAll(PDO::FETCH_ASSOC);

			$sth = $dbh->prepare($q2);
			$sth->execute(array(':rnId' => $rnId));
			$rn = $sth->fetch(PDO::FETCH_ASSOC, 1);

			$sth = $dbh->prepare($qm);
			$sth->execute(array(':mtId' => $rn['mtId']));
			$mt = $sth->fetch(PDO::FETCH_ASSOC, 1);

		} catch (Exception $ex){
			print($ex);
			return false;
		}

		include_once BASIS_DIR .'/Templates/Formulare/ZahlungsQuittung.01.tmpl.php';
		return;
	}

	public function ajaxEditKomm()
	{
		$fehler = "";
		$output = array();
		$dataPost = array();
		$rnId;

		if(!isset($_POST['rnId']) OR empty($_POST['rnId']))
		{
			$fehler .= "rnId fehlt.\n";
		}
		else
		{
			$rnId = Fltr::deleteSpace($_POST['rnId']);
			if(!Fltr::isInt($_POST['rnId']))
			{
				$fehler .= "rnId ist kein Integer.\n";
			}
			else{
				$dataPost[':rnId'] = $rnId;
			}
		}

		if(isset($_POST['rnKomm']))
		{
			if(empty($_POST['rnKomm']))
			{
				$dataPost[':rnKomm'] = NULL;
			}
			else{
				$dataPost[':rnKomm'] = Fltr::filterStr($_POST['rnKomm']);
			}
		}
		else
		{
			$fehler .= "Kommentar fehlt.\n";
		}

		if(!empty($fehler))
		{
			$output['status'] = 'Fehler.';
			$output['info'] = $fehler;
			header("Content-type: application/json");
			exit(json_encode($output));
		}


        $dbh = \MVC\DBFactory::getDBH();
        if(!$dbh)
        {
			$output['status'] = 'Fehler.';
			$output['info'] = "no Connection to DB (dbh).";
			header("Content-type: application/json");
			exit(json_encode($output));
        }

		$q = "UPDATE rechnungen SET rnKomm = :rnKomm WHERE rnId = :rnId";
		try
		{
			$sth = $dbh->prepare($q);
			$res = $sth->execute($dataPost);
		} catch (Exception $ex) {
			print $ex;
			return "Fehler beim Update.";
		}
		$r = ($res>0) ? "Kommentar wurde erfolgreich geändert." : "Kommentar konnte nicht geändert werden. Wahrscheinlich, Fehler im Datenbank.";

		$output['status'] = 'ok';
		$output['info'] = $r;
		header("Content-type: application/json");
		exit(json_encode($output));
	}

	public function ajaxDeleteRechnung()
	{
		$fehler = "";
		$output = array();
		$dataPost = array();
		$rnId;

		if(!isset($_POST['rnId']) OR empty($_POST['rnId']))
		{
			$fehler .= "rnId fehlt.\n";
		}
		else
		{
			$rnId = Fltr::deleteSpace($_POST['rnId']);
			if(!Fltr::isInt($_POST['rnId']))
			{
				$fehler .= "rnId ist kein Integer.\n";
			}
			else{
				$dataPost[':rnId'] = $rnId;
			}
		}

		if(!empty($fehler))
		{
			$output['status'] = $fehler;
			header("Content-type: application/json");
			exit(json_encode($output));
		}


		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			$output['status'] = "kein DBH";
			header("Content-type: application/json");
			exit(json_encode($output));
		}

		$qRd = "DELETE FROM rechnungsdaten WHERE rnId = :rnId;";
		$qRn = "DELETE FROM rechnungen WHERE rnId = :rnId;";
		$qS = "SELECT * FROM rechnungen WHERE rnId=:rnId;";

		try
		{
//delete PDF if exists
			$sth = $dbh->prepare($qS);
			$sth->execute($dataPost);
			$rn = $sth->fetch(PDO::FETCH_ASSOC,1);
			if( !empty($rn['rnPdfUrl']) ){
				//echo BASIS_RECHNUNG_DIR.$rn['rnPdfUrl'];
				unlink(BASIS_RECHNUNG_DIR.$rn['rnPdfUrl']);
			}

//delete record
			$sth = $dbh->prepare($qRd);
			$res = $sth->execute($dataPost);

			$sth = $dbh->prepare($qRn);
			$res = $sth->execute($dataPost);
		}
		catch (Exception $ex) {
			$output['status'] = $ex;
			header("Content-type: application/json");
			exit(json_encode($output));
		}

		$output['status'] = "ok";
		header("Content-type: application/json");
		exit(json_encode($output));
	}
}
