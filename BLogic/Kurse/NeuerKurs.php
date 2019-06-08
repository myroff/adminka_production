<?php
namespace Kurse;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
use PDO as PDO;

class NeuerKurs
{
	public function showForm()
	{
		include_once BASIS_DIR.'/Templates/Kurse/NeuerKurs.tmpl.php';
		return;
	}
	
	public function saveNewKurs()
	{
		$fehler = "";
		$fehlerInput = array();
		$dataPost = array();
		$output = array();
		$testData = array();
		
		//kurName - Name des Kurses
		if(!empty($_POST['kurName']))
		{
			$_POST['kurName'] = trim($_POST['kurName']);
			$_POST['kurName'] = str_replace("/\s+/", " ", $_POST['kurName']);
			if(Fltr::isText($_POST['kurName']))
			{
				$dataPost[':kurName'] = $_POST['kurName'];
				$testData[':kurName'] = $_POST['kurName'];
			}
			else
			{
				$fehler .= "Der Name des Kurses ist falsch eingegeben.<br>Erlaubt sind Ziffern 0 bis 9, Buchstaben, Leerzeichen, Symbolen (. , : ; ! ? ' * = # / \ \" - + _).<br>";
				$fehlerInput[] = 'kurName';
			}
		}
		else 
		{
			$fehler .= "Der Name des Kurses fehlt.<br>Erlaubt sind Ziffern 0 bis 9, Buchstaben, Leerzeichen, Symbolen (. , : ; ! ? ' * = # / \ \" - + _).<br>";
			$fehlerInput[] = 'kurName';
		}
		
		//maxKnd - maximale Anzahl der Teilnehmern in der Gruppe
		if(!empty($_POST['maxKnd']))
		{
			$_POST['maxKnd'] = Fltr::deleteSpace($_POST['maxKnd']);
			if(Fltr::isInt($_POST['maxKnd']))
			{
				$dataPost[':maxKnd'] = $_POST['maxKnd'];
			}
			else
			{
				$fehler .= "Maximale Anzahl der Kunden in der Gruppe soll ein Ganzzahl sein.<br>";
				$fehlerInput[] = 'maxKnd';
			}
		}
		
		//Lehrer
		if(!empty($_POST['lehrId']))
		{
			$_POST['lehrId'] = Fltr::deleteSpace($_POST['lehrId']);
			if(Fltr::isInt($_POST['lehrId']))
			{
				$dataPost[':lehrId'] = $_POST['lehrId'];
			}
			else
			{
				$fehler .= "ID des Lehrers ist falsch eingegeben. Erlaubt ist nur ein Ganzzahl.<br>";
				$fehlerInput[] = 'kurBeschreibung';
			}
		}
		
		//Beschreigung des Kurses
		if(!empty($_POST['kurBeschreibung']))
		{
			$_POST['kurBeschreibung'] = trim($_POST['kurBeschreibung']);
			$_POST['kurBeschreibung'] = str_replace("/\s+/", " ", $_POST['kurBeschreibung']);
			if(Fltr::isText($_POST['kurBeschreibung']))
			{
				$dataPost[':kurBeschreibung'] = $_POST['kurBeschreibung'];
			}
			else
			{
				$fehler .= "Berschreibung des Kurses ist falsch eingegeben.<br>Erlaubt sind Ziffern 0 bis 9, Buchstaben, Leerzeichen, Symbolen (. , : ; ! ? ' * €= # / \ \" - + _).<br>";
				$fehlerInput[] = 'kurBeschreibung';
			}
		}
		
		//Preis des Unterrichts
		if(!empty($_POST['kurPreis']))
		{
			$_POST['kurPreis'] = str_replace(" ", "", $_POST['kurPreis']);
			$_POST['kurPreis'] = str_replace(",", ".", $_POST['kurPreis']);
			if(Fltr::isFloat($_POST['kurPreis']) OR Fltr::isInt($_POST['kurPreis']))
			{
				$dataPost[':kurPreis'] = $_POST['kurPreis'];
			}
			else
			{
				$fehler .= "Preis des Kurses ist falsch eingegeben.<br>";
				$fehlerInput[] = 'kurPreis';
			}
		}
		
		//Preis-Typ: pro Monat oder Stunde
		if(!empty($_POST['kurIsStdPreis']))
		{
			$_POST['kurIsStdPreis'] = Fltr::deleteSpace($_POST['kurIsStdPreis']);
			if(Fltr::isRowString($_POST['kurIsStdPreis']))
			{
				$dataPost[':kurIsStdPreis'] = $_POST['kurIsStdPreis'] === 'proStunde' ? 1 : 0;
			}
			else
			{
				$fehler .= "Zahlungstyp ist falsch eingegeben. Erlaubt sind nur die Buchstaben<br>";
				$fehlerInput[] = 'kurIsStdPreis';
			}
		}
		
		//Altersgruppen
		//Jahren
		if(!empty($_POST['kurMinAlter']) OR !empty($_POST['kurMaxAlter']))
		{
			$_POST['kurMinAlter'] = str_replace(" ", "", $_POST['kurMinAlter']);
			$_POST['kurMaxAlter'] = str_replace(" ", "", $_POST['kurMaxAlter']);
			
//wenn beide grenzalter gesetz sind
			if(!empty($_POST['kurMinAlter']) AND !empty($_POST['kurMaxAlter']))
			{
				if(Fltr::isInt($_POST['kurMinAlter']))
				{
					$dataPost[':kurMinAlter'] = intval($_POST['kurMinAlter']);
				}
				else
				{
					$fehler .= "Jungstes Alter ist falsch eingegeben.<br>";
					$fehlerInput[] = 'kurMinAlter';
				}
				
				if(Fltr::isInt($_POST['kurMaxAlter']))
				{
					$dataPost[':kurMaxAlter'] = intval($_POST['kurMaxAlter']);
				}
				else
				{
					$fehler .= "Ältestes Alter ist falsch eingegeben.<br>";
					$fehlerInput[] = 'kurMaxAlter';
				}
			//wenn minimaler alter grosser als maximaler alter ist
				if($dataPost[':kurMinAlter'] > $dataPost[':kurMaxAlter'])
				{
					$fehler .= "Jungstes Alter ist <b>GROSSER</b> als ältestes Alter.<br>";
					$fehlerInput[] = 'kurMinAlter';
					$fehlerInput[] = 'kurMaxAlter';
				}
			}
//wenn nur anfangsalter gesetz ist
			elseif(!empty($_POST['kurMinAlter']) AND empty($_POST['kurMaxAlter']))
			{
				if(Fltr::isInt($_POST['kurMinAlter']))
				{
					$dataPost[':kurMinAlter'] = $_POST['kurMinAlter'];
					$dataPost[':kurMaxAlter'] = $_POST['kurMinAlter'];
				}
				else
				{
					$fehler .= "Jungstes Alter ist falsch eingegeben.<br>";
					$fehlerInput[] = 'kurMinAlter';
				}
			}
//wenn nur endalter gesetz ist
			elseif(empty($_POST['kurMinAlter']) AND !empty($_POST['kurMaxAlter']))
			{
				if(Fltr::isInt($_POST['kurMaxAlter']))
				{
					$dataPost[':kurMinAlter'] = $_POST['kurMaxAlter'];
					$dataPost[':kurMaxAlter'] = $_POST['kurMaxAlter'];
				}
				else
				{
					$fehler .= "Ältestes Alter ist falsch eingegeben.<br>";
					$fehlerInput[] = 'kurMaxAlter';
				}
			}
		}
		
	//Klassen
		if(!empty($_POST['kurMinKlasse']) OR !empty($_POST['kurMaxKlasse']))
		{
			$_POST['kurMinKlasse'] = str_replace(" ", "", $_POST['kurMinKlasse']);
			$_POST['kurMaxKlasse'] = str_replace(" ", "", $_POST['kurMaxKlasse']);
			
//wenn beide grenzklassen gesetz sind
			if(!empty($_POST['kurMinKlasse']) AND !empty($_POST['kurMaxKlasse']))
			{
				if(Fltr::isInt($_POST['kurMinKlasse']))
				{
					$dataPost[':kurMinKlasse'] = intval($_POST['kurMinKlasse']);
				}
				else
				{
					$fehler .= "Jungste Klasse ist falsch eingegeben.<br>";
					$fehlerInput[] = 'kurMinKlasse';
				}
				
				if(Fltr::isInt($_POST['kurMaxKlasse']))
				{
					$dataPost[':kurMaxKlasse'] = intval($_POST['kurMaxKlasse']);
				}
				else
				{
					$fehler .= "Älteste Klasse ist falsch eingegeben.<br>";
					$fehlerInput[] = 'kurMaxKlasse';
				}
			//wenn minimaler alter grosser als maximaler alter ist
				if($dataPost[':kurMinKlasse'] > $dataPost[':kurMaxKlasse'])
				{
					$fehler .= "Jungste Klasse ist <b>GROSSER</b> als älteste Klasse.<br>";
					$fehlerInput[] = 'kurMinKlasse';
					$fehlerInput[] = 'kurMaxKlasse';
				}
			}
//wenn nur anfangsalter gesetz ist
			elseif(!empty($_POST['kurMinKlasse']) AND empty($_POST['kurMaxKlasse']))
			{
				if(Fltr::isInt($_POST['kurMinKlasse']))
				{
					$dataPost[':kurMinKlasse'] = $_POST['kurMinKlasse'];
					$dataPost[':kurMaxKlasse'] = $_POST['kurMinKlasse'];
				}
				else
				{
					$fehler .= "Jungstes Klasse ist falsch eingegeben.<br>";
					$fehlerInput[] = 'kurMinKlasse';
				}
			}
//wenn nur endalter gesetz ist
			elseif(empty($_POST['kurMinKlasse']) AND !empty($_POST['kurMaxKlasse']))
			{
				if(Fltr::isInt($_POST['kurMaxKlasse']))
				{
					$dataPost[':kurMinKlasse'] = $_POST['kurMaxKlasse'];
					$dataPost[':kurMaxKlasse'] = $_POST['kurMaxKlasse'];
				}
				else
				{
					$fehler .= "Älteste Klasse ist falsch eingegeben.<br>";
					$fehlerInput[] = 'kurMaxKlasse';
				}
			}
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
			$tbl = substr($tbl, 0, -1);
			$vl = substr($vl, 0, -1);
			
			$q = "INSERT INTO kurse (".$tbl.") VALUES(".$vl.")";
			$tq = "SELECT count(*) as 'count' FROM kurse WHERE kurName=:kurName";
			
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
						$output = array('info' => "[DB] Neuer Unterricht wurde erfolgreich hinzugefügt.");
					}
					else
					{
						$output = array('info' => "[DB] Neuer Unterricht konnte nicht hinzugefügt werden.");//, 'data' => $dataPost
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