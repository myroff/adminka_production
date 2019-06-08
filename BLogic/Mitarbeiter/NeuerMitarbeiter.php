<?php
namespace Mitarbeiter;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
use PDO as PDO;

class NeuerMitarbeiter 
{
	public function showForm()
	{
		include_once BASIS_DIR.'/Templates/Mitarbeiter/MitarbeiterNeu.tmpl.php';
		return;
	}
	
	public function saveNewMitarbeiter()
	{
		$fehler = "";
		$fehlerInput = array();
		$dataPost = array();
		$output = array();
		$testData = array();
		//Anrede
		if(!empty($_POST['anrede']))
		{
			$_POST['anrede'] = trim($_POST['anrede']);
			$_POST['anrede'] = preg_replace("/\s+/", " ", $_POST['anrede']);
			if(Fltr::isRowString($_POST['anrede']))
			{
				$dataPost[':anrede'] = $_POST['anrede'];
				$testData[':anrede'] = $_POST['anrede'];
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
				$dataPost[':vorname'] = $_POST['vorname'];
				$testData[':vorname'] = $_POST['vorname'];
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
				$dataPost[':name'] = $_POST['name'];
				$testData[':name'] = $_POST['name'];
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
				$dataPost[':geburtsdatum'] = Fltr::strToSqlDate($_POST['geburtsdatum']);
				$testData[':geburtsdatum'] = Fltr::strToSqlDate($_POST['geburtsdatum']);
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
				$dataPost[':strasse'] = $_POST['strasse'];
				$testData[':strasse'] = $_POST['strasse'];
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
				$dataPost[':strNr'] = $_POST['haus'];
				$testData[':strNr'] = $_POST['haus'];
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
			if(Fltr::isRowString($_POST['stadt']))
			{
				$dataPost[':stadt'] = $_POST['stadt'];
				$testData[':stadt'] = $_POST['stadt'];
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
				$dataPost[':plz'] = $_POST['plz'];
				$testData[':plz'] = $_POST['plz'];
			}
			else
			{
				$fehler .= "PLZ besteht aus genau 5 ziffern. Bspl.: 40210.<br>";
				$fehlerInput[] = 'plz';
			}
		}
		else
		{
			$fehler .= "PLZ fehlt.<br>";
			$fehlerInput[] = 'plz';
		}
		
		//Geburtsdatum
		if(!empty($_POST['eingestelltAm']))
		{
			$_POST['eingestelltAm'] = str_replace(" ", "", $_POST['eingestelltAm']);
			if(Fltr::isDate($_POST['eingestelltAm']))
			{
				$dataPost[':eingestelltAm'] = Fltr::strToSqlDate($_POST['eingestelltAm']);
			}
			else
			{
				$fehler .= "Einstellungdatum ist falsch eingegeben.<br>";
				$fehlerInput[] = 'eingestelltAm';
			}
		}
		else 
		{
			$fehler .= "Einstellungsdatum fehlt.<br>";
			$fehlerInput[] = 'eingestelltAm';
		}
		
		if(empty($fehler))
		{	
			require_once BASIS_DIR.'/MVC/DBFactory.php';
			$dbh = \MVC\DBFactory::getDBH();
			
			if(!$dbh)
			{
				$output = array('info' => "no connection to db (dbh).");
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
			$tbl .= "erstelltAm,";
			$vl .= "NOW(),";

			//get Current Admin Id
			require_once BASIS_DIR.'/Tools/User.php';
			$curAdminId = \Tools\User::getCurrentUserId();

			$tbl .= "erstelltVom";
			$vl .= "'".$curAdminId."'";

			//$tbl = substr($tbl, 0, -1);
			//$vl = substr($vl, 0, -1);
			$q = "INSERT INTO mitarbeiter (".$tbl.") VALUES(".$vl.")";
			
			$tq = "SELECT count(*) as 'count' FROM mitarbeiter WHERE anrede=:anrede AND vorname=:vorname AND name=:name AND geburtsdatum=:geburtsdatum AND strasse=:strasse AND strNr=:strNr AND stadt=:stadt AND plz=:plz";
			
			try
			{
				$sthTest = $dbh->prepare($tq);
				$sthTest->execute($testData);
				$resTest = $sthTest->fetch(PDO::FETCH_ASSOC, 1);
				
				if($resTest['count'] > 0)
				{
					$output = array('info' => "Der Mitarbeiter ist schon eingetragen.", 'data' => $dataPost);
				}
				else
				{
					$sth = $dbh->prepare($q);
					$res = $sth->execute($dataPost);

					if($res>0)
					{
						$output = array('info' => "[DB] Neuer Kunde wurde erfolgreich hinzugefügt.", 'data' => $dataPost);
					}
					else
					{
						$output = array('info' => "[DB] Neuer Kunde konnte nicht hinzugefügt werden.", 'data' => $dataPost);
					}
				}
			}
			catch (Exception $ex) {
				$output = array('info' => $ex, 'data' => $dataPost);
			}
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
