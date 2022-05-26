<?php
namespace Kunde;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
use PDO as PDO;

require_once BASIS_DIR.'/Tools/TmplTools.php';
use Tools\TmplTools as TmplTls;

require_once BASIS_DIR.'/BLogic/Payment/PaymentApi.php';
use Payment\PaymentApi as PaymentApi;

//twig
require_once BASIS_DIR . '/Vendor/autoload.php';

class neueKunde
{
    public function showForm()
    {
		/*
		$bezahlMethoden = array("bar" => "Bar",
			"lastschrift" => "Lastschrift",
			"bamf" => "BAMF", "zuzahler" => "Zuzahler",
			"selbstzahler" => "Selbstzahler", "ueberweisung" => "Überweisung"
			);
		*/
		$bezahlMethoden = PaymentApi::getSelectorData();

		$anreden = ["Frau" => "Frau", "Herr" => "Herr"];

		$vars		= [];
		$vars['zahlenMitSelector'] = TmplTls::printMaterializeSelector($bezahlMethoden, "zahlenMit", "zahlenMit", "", "Bezahlen mit", 1);
		$vars['anredeSelector'] = TmplTls::printMaterializeSelector($anreden, "anrede", "anrede", "", "Anrede", 1);

		$options	= []; #array('cache' => TWIG_CACHE_DIR);
		$loader		= new \Twig_Loader_Filesystem(TWIG_TEMPLATE_DIR);
		$twig		= new \Twig_Environment($loader, $options);
		$twigTmpl	= $twig->load('/Kunde/KundeNeu.twig');
		echo $twigTmpl->render($vars);
		#include_once BASIS_DIR.'/Templates/KundeNeu.tmpl.php';
		return;
    }

    public function saveNewKunde()
    {
		$fehler			= "";
		$fehlerInput	= array();
		$dataPost		= array();
		$dataBezahlung	= array();
		$output			= array();
		$checkKunde		= array();

		//Kunden-Nummer
		if(!empty($_POST['kundenNummer']))
		{
			$dataPost[':kundenNummer'] = Fltr::filterStr($_POST['kundenNummer']);
		}

		//Anrede
		if(!empty($_POST['anrede']))
		{
			$_POST['anrede'] = trim($_POST['anrede']);
				$_POST['anrede'] = preg_replace("/\s+/", " ", $_POST['anrede']);

				if(Fltr::isRowString($_POST['anrede']))
				{
					$dataPost[':anrede']	= $_POST['anrede'];
					$checkKunde[':anrede']	= $_POST['anrede'];
				}
				else
				{
					$fehler .= "Ziffern bei Anrede sind unzulässig.<br>";
					$fehlerInput[] = 'anrede';
				}
		}
		else
		{
			$fehler .= "Anrede fehlt.<br>";
			$fehlerInput[] = 'anrede';
		}

		//Vorname
		if(!empty($_POST['vorname']))
		{
			$_POST['vorname'] = trim($_POST['vorname']);
			$_POST['vorname'] = preg_replace("/\s+/", " ", $_POST['vorname']);

			if(Fltr::isRowString($_POST['vorname']))
			{
				$dataPost[':vorname']	= $_POST['vorname'];
				$checkKunde[':vorname']	= $_POST['vorname'];
			}
			else
			{
				$fehler .= "Beim Vornamen sind nur die Buchstaben erlaubt (klein und gross).<br>";
				$fehlerInput[] = 'vorname';
			}
		}
		else
		{
			$fehler .= "Vorname fehlt.<br>";
			$fehlerInput[] = 'vorname';
		}

		//Name
		if(!empty($_POST['name']))
		{
			$_POST['name'] = trim($_POST['name']);
			$_POST['name'] = preg_replace("/\s+/", " ", $_POST['name']);

			if(Fltr::isRowString($_POST['name']))
			{
				$dataPost[':name']		= $_POST['name'];
				$checkKunde[':name']	= $_POST['name'];
			}
			else
			{
				$fehler .= "Beim Namen sind nur die Buchstaben erlaubt (klein und gross).<br>";
				$fehlerInput[] = 'name';
			}
		}
		else
		{
			$fehler .= "Name fehlt.<br>";
			$fehlerInput[] = 'name';
		}

	//Geburtsdatum
		if(!empty($_POST['geburtsdatum']))
		{
			$_POST['geburtsdatum'] = str_replace(" ", "", $_POST['geburtsdatum']);
			if(Fltr::isDate($_POST['geburtsdatum']))
			{
				$dataPost[':geburtsdatum']		= Fltr::strToSqlDate($_POST['geburtsdatum']);
				$checkKunde[':geburtsdatum']	= $dataPost[':geburtsdatum'];
			}
			else
			{
				$fehler .= "Geburtsdatum ist falsch eingegeben.<br>";
				$fehlerInput[] = 'geburtsdatum';
			}
		}
		else
		{
			$fehler .= "Geburtsdatum fehlt.<br>";
			$fehlerInput[] = 'geburtsdatum';
		}

	//Telefon
		if(!empty($_POST['telefon']))
		{
			$_POST['telefon'] = str_replace(" ", "", $_POST['telefon']);
			if(Fltr::isTelefone($_POST['telefon']))
			{
				$dataPost[':telefon'] = $_POST['telefon'];
			}
			else
			{
				$fehler .= "Telefon ist falsch eingegeben. Beispiel: +49 211 123 456 789<br>";
				$fehlerInput[] = 'telefon';
			}
		}

	//Handy
		if(!empty($_POST['handy']))
		{
			$_POST['handy'] = str_replace(" ", "", $_POST['handy']);
			if(Fltr::isTelefone($_POST['handy']))
			{
				$dataPost[':handy'] = $_POST['handy'];
			}
			else
			{
				$fehler .= "Handy ist falsch eingegeben. Beispiel: +49 151 123 456 789<br>";
				$fehlerInput[] = 'handy';
			}
		}

	//Email
		if(!empty($_POST['email']))
		{
			$_POST['email'] = str_replace(" ", "", $_POST['email']);
			if(Fltr::isEmail($_POST['email']))
			{
				$dataPost[':email'] = $_POST['email'];
			}
			else
			{
				$fehler .= "Email ist falsch eingegeben.<br>";
				$fehlerInput[] = 'email';
			}
		}

	//Strasse
		if(!empty($_POST['strasse']))
		{
			$_POST['strasse'] = trim($_POST['strasse']);
			$_POST['strasse'] = preg_replace("/\s+/", " ", $_POST['strasse']);

			if(Fltr::isStrasse($_POST['strasse']))
			{
				$dataPost[':strasse']	= $_POST['strasse'];
				$checkKunde[':strasse'] = $_POST['strasse'];
			}
			else
			{
				$fehler .= "Beim Strassennamen sind keine Ziffern erlaubt. Bspl.: Musterstr. Allee.<br>";
				$fehlerInput[] = 'strasse';
			}
		}
		else
		{
			$fehler .= "Strasse fehlt.<br>";
			$fehlerInput[] = 'strasse';
		}

	//Hausnummer
		if(!empty($_POST['haus']))
		{
			$_POST['haus'] = str_replace(" ", "", $_POST['haus']);
			if(Fltr::isHausNr($_POST['haus']))
			{
				$dataPost[':strNr']		= $_POST['haus'];
				$checkKunde[':strNr']	= $_POST['haus'];
			}
			else
			{
				$fehler .= "Beim Hausnummer sind nur Ziffern und Buchstaben erlaubt. Bspl.: 123a.<br>";
				$fehlerInput[] = 'haus';
			}
		}
		else
		{
			$fehler .= "Hausnummer fehlt.<br>";
			$fehlerInput[] = 'haus';
		}

	//Stadt
		if(!empty($_POST['stadt']))
		{
			$_POST['stadt'] = trim($_POST['stadt']);
			$_POST['stadt'] = preg_replace("/\s+/", " ", $_POST['stadt']);

			if(Fltr::isText($_POST['stadt']))
			{
				$dataPost[':stadt']		= $_POST['stadt'];
				$checkKunde[':stadt']	= $_POST['stadt'];
			}
			else
			{
				$fehler .= "Bei der Stadt sind nur die Buchstaben erlaubt. Bspl.: Düsseldorf.<br>";
				$fehlerInput[] = 'stadt';
			}
		}
		else
		{
			$fehler .= "Stadt fehlt.<br>";
			$fehlerInput[] = 'stadt';
		}

	//PLZ
		if(!empty($_POST['plz']))
		{
			$_POST['plz'] = str_replace(" ", "", $_POST['plz']);
			if(Fltr::isPlz($_POST['plz']))
			{
				$dataPost[':plz']	= $_POST['plz'];
				$checkKunde[':plz'] = $_POST['plz'];
			}
			else
			{
				$fehler .= "PLZ besteht aus genau 5 ziffern. Bspl.: 40210.<br>";
				$fehlerInput[] = 'plz';
			}
		}
		/*
		else
		{
			$fehler .= "PLZ fehlt.<br>";
			$fehlerInput[] = 'plz';
		}
		*/
		//Muttersprache
		if(!empty($_POST['muttersprache']))
		{
			$_POST['muttersprache'] = Fltr::filterStr($_POST['muttersprache']);
			$_POST['muttersprache'] = Fltr::deleteSpace($_POST['muttersprache']);
			if(Fltr::isText($_POST['muttersprache']))
			{
				$dataPost[':muttersprache'] = $_POST['muttersprache'];
			}
			else
			{
				$fehler .= "Muttersprache ist falsch eingegeben. Erlaubt sind nur die Buchstaben<br>";
				$fehlerInput[] = 'mutterspache';
			}
		}

		//Geburtsland
		if(!empty($_POST['geburtsland']))
		{
			$_POST['geburtsland'] = Fltr::filterStr($_POST['geburtsland']);
			if(Fltr::isRowString($_POST['geburtsland']))
			{
				$dataPost[':geburtsland'] = $_POST['geburtsland'];
			}
			else
			{
				$fehler .= "Geburtsland ist falsch eingegeben. Erlaubt sind nur die Buchstaben<br>";
				$fehlerInput[] = 'geburtsland';
			}
		}

	//istFotoErlaubt
		if(!empty($_POST['istFotoErlaubt']))
		{
			$_POST['istFotoErlaubt'] = Fltr::deleteSpace($_POST['istFotoErlaubt']);
			if(Fltr::isRowString($_POST['istFotoErlaubt']))
			{
				$dataPost[':istFotoErlaubt'] = $_POST['istFotoErlaubt'] === 'ja' ? 1 : 0;
			}
			else
			{
				$fehler .= "'ist Foto erlaubt' ist falsch eingegeben. Erlaubt sind nur die Buchstaben<br>";
				$fehlerInput[] = 'istFotoErlaubt';
			}
		}

	//Geburtsland
		if(!empty($_POST['empfohlenId']))
		{
			$_POST['empfohlenId'] = Fltr::filterStr($_POST['empfohlenId']);
			if(Fltr::isInt($_POST['empfohlenId']))
			{
				$dataPost[':empfohlenId'] = $_POST['empfohlenId'];
			}
			else
			{
				$fehler .= "empfohlenId ist falsch eingegeben. Erlaubt sind nur die Ziffern<br>";
				$fehlerInput[] = 'empfohlenId';
			}
		}

//Zahlungsadaten
		//Bezahlen mit Bar oder Lastschrift
		if(!empty($_POST['zahlenMit']) || $_POST['zahlenMit'] === '0')
		{
			$dataBezahlung[':payment_id'] = (int)$_POST['zahlenMit'];
		}
                else
		{
			$fehler .= "'Zahlen Mit' fehlt.<br>";
			$fehlerInput[] = 'zahlenMit';
		}

	//Kontoinhaber
		if(!empty($_POST['kontoinhaber']))
		{
			$_POST['kontoinhaber'] = trim($_POST['kontoinhaber']);
			$_POST['kontoinhaber'] = preg_replace("/\s+/", " ", $_POST['kontoinhaber']);

			if(Fltr::isText($_POST['kontoinhaber']))
			{
				$dataBezahlung[':kontoinhaber'] = $_POST['kontoinhaber'];
			}
			else
			{
				$fehler .= "Kontoinhaber ist falsch eingegeben. Erlaubt sind nur die Buchstaben und Leerzeichen.<br>";
				$fehlerInput[] = 'kontoinhaber';
			}
		}

	//zdStrasse
		if(!empty($_POST['zdStrasse']))
		{
			$_POST['zdStrasse'] = trim($_POST['zdStrasse']);
			$_POST['zdStrasse'] = preg_replace("/\s+/", " ", $_POST['zdStrasse']);

			if(Fltr::isStrasse($_POST['zdStrasse']))
			{
				$dataBezahlung[':zdStrasse'] = $_POST['zdStrasse'];
			}
			else
			{
				$fehler .= "Strasse bei Kontodaten ist falsch eingegeben. Erlaubt sind nur die Buchstaben und Leerzeichen.<br>";
				$fehlerInput[] = 'zdStrasse';
			}
		}

	//zdHausnummer
		if(!empty($_POST['zdHausnummer']))
		{
			$_POST['zdHausnummer'] = preg_replace("/\s/", "", $_POST['zdHausnummer']);

			if(Fltr::isHausNr($_POST['zdHausnummer']))
			{
				$dataBezahlung[':zdHausnummer'] = $_POST['zdHausnummer'];
			}
			else
			{
				$fehler .= "Hausnummer bei Kontodaten ist falsch eingegeben. Erlaubt sind nur die Ziffern und Buchstaben ohne Leerzeichen.<br>";
				$fehlerInput[] = 'zdHausnummer';
			}
		}

	//zdPlz
		if(!empty($_POST['zdPlz']))
		{
			$_POST['zdPlz'] = preg_replace("/\s/", "", $_POST['zdPlz']);

			if(Fltr::isPlz($_POST['zdPlz']))
			{
				$dataBezahlung[':zdPlz'] = $_POST['zdPlz'];
			}
			else
			{
				$fehler .= "PLZ bei Kontodaten ist falsch eingegeben. Erlaubt sind nur die 5 Ziffern.<br>";
				$fehlerInput[] = 'zdPlz';
			}
		}

	//zdPlz
		if(!empty($_POST['zdOrt']))
		{
			$_POST['zdOrt'] = trim($_POST['zdOrt']);
			$_POST['zdOrt'] = preg_replace("/\s+/", " ", $_POST['zdOrt']);

			if(Fltr::isRowString($_POST['zdOrt']))
			{
				$dataBezahlung[':zdOrt'] = $_POST['zdOrt'];
			}
			else
			{
				$fehler .= "Ort bei Kontodaten ist falsch eingegeben. Erlaubt sind nur die Buchstaben und Leerzeichen.<br>";
				$fehlerInput[] = 'zdOrt';
			}
		}

	//zdBankname
		if(!empty($_POST['zdBankname']))
		{
			$_POST['zdBankname'] = trim($_POST['zdBankname']);
			$_POST['zdBankname'] = preg_replace("/\s+/", " ", $_POST['zdBankname']);

			if(Fltr::isText($_POST['zdBankname']))
			{
				$dataBezahlung[':bankname'] = $_POST['zdBankname'];
			}
			else
			{
				$fehler .= "Bankname bei Kontodaten ist falsch eingegeben. Erlaubt sind nur die Buchstaben und Leerzeichen.<br>";
				$fehlerInput[] = 'zdBankname';
			}
		}

	//zdBankname
		if(!empty($_POST['zdIban']))
		{
			$_POST['zdIban'] = preg_replace("/\s/", "", $_POST['zdIban']);

			if(Fltr::isWordsAndNumbers($_POST['zdIban']))
			{
				$dataBezahlung[':iban'] = $_POST['zdIban'];
			}
			else
			{
				$fehler .= "IBAN bei Kontodaten ist falsch eingegeben. Erlaubt sind nur die Buchstaben und Leerzeichen.<br>";
				$fehlerInput[] = 'zdIban';
			}
		}

	//zdBankname
		if(!empty($_POST['zdBic']))
		{
			$_POST['zdBic'] = preg_replace("/\s/", "", $_POST['zdBic']);

			if(Fltr::isWordsAndNumbers($_POST['zdBic']))
			{
				$dataBezahlung[':bic'] = $_POST['zdBic'];
			}
			else
			{
				$fehler .= "BIC bei Kontodaten ist falsch eingegeben. Erlaubt sind nur die Buchstaben und Leerzeichen.<br>";
				$fehlerInput[] = 'zdBic';
			}
		}

		if(empty($fehler))
		{

			$dbh = \MVC\DBFactory::getDBH();

			if(!$dbh)
			{
				$output = array('info' => "no connection to db (dbh).");
				header("Content-type: application/json");
				exit(json_encode($output));
			}

			$tbl = "";
			$vl = "";
			$zd_tbl = "";
			$zd_vl = "";

			foreach ($dataPost as $key=>$val)
			{
				$tbl .= substr($key, 1).",";
				$vl .= $key.",";
			}
			$tbl .= "erstelltAm,";
			$vl .= "NOW(),";

			//get Current Admin Id
			require_once BASIS_DIR.'/Tools/User.php';
			$curAdminId = \Tools\User::getCurrentUserId();
			$tbl .= "erstelltVom";
			$vl .= "'".$curAdminId."'";
//Zahlungsdaten vorbereiten

			foreach ($dataBezahlung as $k=>$v)
			{
				$zd_tbl .= substr($k, 1).",";
				$zd_vl .= $k.",";
			}
			$zd_tbl .= "kndId";
			$zd_vl .= ":kndId";
			//$zd_tbl = substr($zd_tbl, 0, -1);
			//$zd_vl = substr($zd_vl, 0, -1);

			//check if client exists
			$isInserted = $this->isClientInserted($checkKunde);
			if($isInserted)
			{
				$output = array('info' => "[DB] Der Kunde ist schon eingetragen. Prüfe die Vor-, Nachnahmen, und Adresse.");
				header("Content-type: application/json");
				exit(json_encode($output));
			}

			$q = "INSERT INTO kunden (".$tbl.") VALUES(".$vl.")";
			$zdq = "INSERT INTO payment_data (".$zd_tbl.") VALUES(".$zd_vl.")";
			$info = "q=".$q."<br>zdq=".$zdq;
			$info .= "<br>dataPost=";
			$info .= print_r($dataPost,true);
			$info .= "<br>dataBezahlung=";

			try
			{
				$dbh->beginTransaction();
				$sth = $dbh->prepare($q);
				$res = $sth->execute($dataPost);


				if($res>0)
				{
				    $dataBezahlung[':kndId'] = $dbh->lastInsertId();
				    $sth = $dbh->prepare($zdq);
				    $res = $sth->execute($dataBezahlung);
				    $dbh->commit();
				    $output = array('info' => "[DB] Neuer Kunde wurde erfolgreich hinzugefügt.");
				}
				else
				{
				    $output = array('info' => "[DB] Neuer Kunde konnte nicht hinzugefügt werden. Evtl., der Kunde ist schon eingetragen.");
				    $dbh->rollBack();
				}

			}
			catch (Exception $ex) {
			    $output = array('info' => $ex, 'data' => $dataPost);
			    $dbh->rollBack();
			}
		}
		else
		{
		    $output = array('fehler' => $fehler,'fehlerInput' => $fehlerInput);
		}

		header("Content-type: application/json");
		exit(json_encode($output));
	}

	/* check if the client is already inserted in db.
	 * @param array $clientsDates	- ['anrede', 'vorname', 'name', 'geburtsdatum'
	 *								, 'strasse', 'strNr', 'stadt', 'plz']
	 */
	public function isClientInserted($clientsDates){
		$qKunde = "SELECT * FROM kunden WHERE anrede LIKE :anrede"
				." AND vorname LIKE :vorname AND name LIKE :name"
				." AND geburtsdatum = :geburtsdatum"
				." AND strasse LIKE :strasse AND strNr LIKE :strNr"
				." AND stadt LIKE :stadt AND plz LIKE :plz";

		$q = "SELECT EXISTS ($qKunde) as kundeExists";

		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return NULL;
		}
		$sth = $dbh->prepare($q);
		$sth->execute($clientsDates);
		$rs = $sth->fetch(PDO::FETCH_ASSOC);
		return (bool) $rs['kundeExists'];
	}
}