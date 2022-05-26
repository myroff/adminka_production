<?php
namespace Kunde;
use PDO as PDO;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
require_once BASIS_DIR.'/Tools/User.php';
use Tools\User as User;

class UntBezahlen {
	public function bezahlen()
	{
		$fehler = "";
		$fehlerInput = array();
		$dataPost = array();
		$output = array();
	//kndId
		if(!isset($_POST['kndId']) OR empty($_POST['kndId']))
		{
			$fehler .= "KundenID fehlt";
		}
		else
		{
			if(Fltr::isInt($_POST['kndId']))
			{
				$dataPost[':kndId'] = $_POST['kndId'];
			}
			else
			{
				$fehler .= "KundenID ist KEIN INT-Wert";
			}
		}
	//untId
		if(!isset($_POST['untId']) OR empty($_POST['untId']))
		{
			$fehler .= "UnterrichtID fehlt";
		}
		else
		{
			if(Fltr::isInt($_POST['untId']))
			{
				$dataPost[':untId'] = $_POST['untId'];
			}
			else
			{
				$fehler .= "UnterrichtID ist KEIN INT-Wert";
			}
		}
	//eintrId
		if(!isset($_POST['eintrId']) OR empty($_POST['eintrId']))
		{
			$fehler .= "EintragsID fehlt";
		}
		else
		{
			if(Fltr::isInt($_POST['eintrId']))
			{
				$dataPost[':eintrId'] = $_POST['eintrId'];
			}
			else
			{
				$fehler .= "EintragsID ist KEIN INT-Wert";
			}
		}
	//bzMonatJahr
		if(!isset($_POST['bzMonatJahr']) OR empty($_POST['bzMonatJahr']))
		{
			$fehler .= "Zahlungsmonat fehlt";
		}
		else
		{
			if(preg_match("/^\d\d\.\d\d\d\d$/", $_POST['bzMonatJahr']))
			{
				$dataPost[':bzMonatJahr'] = Fltr::strToSqlDate("01.".$_POST['bzMonatJahr']);
			}
			else
			{
				$fehler .= "Zahlungsmonat: falsher format.";
			}
		}
	//bzSumme
		if(!isset($_POST['bzSumme']) OR empty($_POST['bzSumme']))
		{
			$fehler .= "Zahlungsbetrag fehlt";
		}
		else
		{
			$_POST['bzSumme'] = str_replace(" ", "", $_POST['bzSumme']);
			$_POST['bzSumme'] = str_replace(",", ".", $_POST['bzSumme']);
			if(Fltr::isFloat($_POST['bzSumme']))
			{
				$dataPost[':bzSumme'] = $_POST['bzSumme'];
			}
			else
			{
				$fehler .= "Zahlungsbetrag ist KEIN Kommazahl. ".$_POST['bzSumme'];
			}
		}
	//bzIstBezalt
		if(!isset($_POST['bzIstBezahlt']) OR empty($_POST['bzIstBezahlt']))
		{
			$fehler .= "Bezahlungsstatus fehlt";
		}
		else
		{
			if(Fltr::isInt($_POST['bzIstBezahlt']))
			{
				$dataPost[':bzIstBezahlt'] = $_POST['bzIstBezahlt'];
			}
			else
			{
				$fehler .= "Bezahlungsstatus ist KEIN INT-Wert. ".$_POST['bzIstBezahlt'];
			}
		}
	//bzKommentar
		if(!empty($_POST['bzKommentar']))
		{
			$_POST['bzKommentar'] = trim($_POST['bzKommentar']);
			$_POST['bzKommentar'] = str_replace("/\s+/", " ", $_POST['bzKommentar']);
			if(Fltr::isText($_POST['bzKommentar']))
			{
				$dataPost[':bzKommentar'] = $_POST['bzKommentar'];
			}
			else
			{
				$fehler .= "Kommentar der Bezahlung ist falsch eingegeben.<br>Erlaubt sind Ziffern 0 bis 9, Buchstaben, Leerzeichen, Symbolen (. , : ; ! ? ' * = # / \ \" - + _).<br>";
				$fehlerInput[] = 'bzKommentar';
			}
		}
	//MitarbeiterID
		$dataPost[':mtId'] = User::getCurrentUserId();
//Output
		if(empty($fehler))
		{

			$dbh = \MVC\DBFactory::getDBH();

			if(!$dbh)
			{
				$output = array('fehler' => "no connection to db (dbh).");
				header("Content-type: application/json");
				exit(json_encode($output));
			}

			$tbl = "";
			$vl = "";

			foreach ($dataPost as $key=>$val)
			{
				$tbl .= substr($key, 1).",";
				$vl .= $key.",";
			}
			$tbl = substr($tbl, 0, -1);
			$vl = substr($vl, 0, -1);

			$q = "INSERT INTO bezahlungen (".$tbl.") VALUES(".$vl.")";
			$tq = "SELECT count(*) as 'count' FROM bezahlungen WHERE kndId=:kndId AND untId=:untId AND eintrId=:eintrId";
			/*
			try
			{
				$sthTest = $dbh->prepare($tq);
				$sthTest->execute($testData);
				$resTest = $sthTest->fetch(PDO::FETCH_ASSOC, 1);

				if($resTest['count'] > 0)
				{
					$output = array('info' => "Der Unterricht ist schon eingetragen.<br>q = $q", 'data' => $dataPost);
				}
				else
				{
					$sth = $dbh->prepare($q);
					$res = $sth->execute($dataPost);

					if($res>0)
					{
						$output = array('info' => "[DB] Neue Bezahlung wurde erfolgreich hinzugefügt.", 'data' => $dataPost);
					}
					else
					{
						$output = array('info' => "[DB] Neuer Unterricht konnte nicht hinzugefügt werden.", 'data' => $dataPost);
					}
				}
			}
			catch (Exception $ex) {
				$output = array('info' => $ex, 'data' => $dataPost);
			}*/
			$data = print_r($dataPost, true);
			$output = array('info' => "[DB] Neue Bezahlung wurde erfolgreich hinzugefügt.<br>".$data);
		}
		else
		{
			$output = array('fehler' => $fehler,
						'fehlerInput' => $fehlerInput
					);
		}

		header("Content-type: application/json");
		exit(json_encode($output));
	}
}
