<?php
namespace Stundenplan;
use PDO as PDO;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;

class NewTermin 
{
	public function addNewTermin()
	{
		$fehler = "";
		$fehlerInput = array();
		$dataPost = array();
		$output = array();
		$testData = array();
		
	//seasonId
		if(!empty($_POST['season_id']))
		{
			$_POST['season_id'] = str_replace("/\s/", "", $_POST['season_id']);
			if(Fltr::isInt($_POST['season_id']))
			{
				$dataPost[':season_id'] = $_POST['season_id'];
				$testData[':season_id'] = $_POST['season_id'];
			}
			else
			{
				$fehler .= "SeasonID für Unterricht soll ein Integer sein.<br>";
				$fehlerInput[] = 'kurId';
			}
		}
		else 
		{
			$fehler .= "Saison-ID für Unterricht fehlt.<br>";
			$fehlerInput[] = 'season_id';
		}
	//kurId - ID des Kurses
		if(!empty($_POST['kurId']))
		{
			$_POST['kurId'] = str_replace("/\s/", "", $_POST['kurId']);
			if(Fltr::isInt($_POST['kurId']))
			{
				$dataPost[':kurId'] = $_POST['kurId'];
			}
			else
			{
				$fehler .= "ID für Unterricht soll ein Integer sein.<br>";
				$fehlerInput[] = 'kurId';
			}
		}
		else 
		{
			$fehler .= "ID für Unterricht fehlt.<br>";
			$fehlerInput[] = 'kurId';
		}
	//wochentag
		if(!empty($_POST['wochentag']))
		{
			$_POST['wochentag'] = str_replace("/\s/", "", $_POST['wochentag']);
			if(Fltr::isInt($_POST['wochentag']) && $_POST['wochentag']<8 && $_POST['wochentag']>0)
			{
				$dataPost[':wochentag'] = $_POST['wochentag'];
				$testData[':wochentag'] = $_POST['wochentag'];
			}
			else
			{
				$fehler .= "Wochentag soll eine Ganzzahl von 1 bis 7 seien.<br>";
			}
		}
		else
		{
			$fehler .= "Tag der Woche fehlt (1 bis 7).<br>";
			$fehlerInput[] = 'wochentag';
		}
		
		if(!empty($_POST['anfang']))
		{
			$_POST['anfang'] = str_replace("/\s/", "", $_POST['anfang']);
			if(Fltr::isStrTime($_POST['anfang']))
			{
				$dataPost[':anfang'] = $_POST['anfang'].":00";
				$testData[':anfang'] = $_POST['anfang'].":00";
			}
			else
			{
				$fehler .= "Anfangszeit ist falsch eingegeben. Bspl.: 15:45.<br>";
				$fehlerInput[] = 'anfang';
			}
		}
		else 
		{
			$fehler .= "Anfangszeit fehlt.<br>";
			$fehlerInput[] = 'anfang';
		}
		
		if(!empty($_POST['ende']))
		{
			$_POST['ende'] = str_replace("/\s/", "", $_POST['ende']);
			if(Fltr::isStrTime($_POST['ende']))
			{
				$dataPost[':ende'] = $_POST['ende'].":00";
				$testData[':ende'] = $_POST['ende'].":00";
			}
			else
			{
				$fehler .= "Endzeit ist falsch eingegeben. Bspl.: 15:45.<br>";
				$fehlerInput[] = 'ende';
			}
		}
		else 
		{
			$fehler .= "Anfangszeit fehlt.<br>";
			$fehlerInput[] = 'ende';
		}
		
		if(!empty($_POST['raum']))
		{
			$_POST['raum'] = Fltr::deleteSpace($_POST['raum']);
			if(Fltr::isText($_POST['raum']))
			{
				$dataPost[':raum'] = $_POST['raum'];
				$testData[':raum'] = $_POST['raum'];
			}
			else
			{
				$fehler .= "Der Raum des Unterrichts ist falsch eingegeben.<br>Erlaubt sind Ziffern 0 bis 9, Buchstaben, Leerzeichen, Symbolen (. , : ; ! ? ' * = # / \ \" - + _).<br>";
				$fehlerInput[] = 'raum';
			}
		}
		else 
		{
			$fehler .= "Der Raum des Unterrichts fehlt.<br>Erlaubt sind Ziffern 0 bis 9, Buchstaben, Leerzeichen, Symbolen (. , : ; ! ? ' * = # / \ \" - + _).<br>";
			$fehlerInput[] = 'raum';
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
			
			$q = "INSERT INTO stundenplan (".$tbl.") VALUES(".$vl.")";
			$tq = "SELECT count(*) as 'count' FROM stundenplan WHERE raum=:raum AND wochentag=:wochentag AND season_id = :season_id"
					. " AND ( (:anfang > anfang AND :anfang < ende) OR (:ende > anfang AND :ende < ende) OR (:anfang = anfang AND :ende = ende) )";
			
			try
			{
				$sthTest = $dbh->prepare($tq);
				$sthTest->execute($testData);
				$resTest = $sthTest->fetch(PDO::FETCH_ASSOC, 1);
				
				if($resTest['count'] > 0)
				{
					$output = array('info' => "Die Zeit oder der Raum ist schon besetz.<br>q = $tq", 'data' => $dataPost);
				}
				else
				{
					$sth = $dbh->prepare($q);
					$res = $sth->execute($dataPost);

					if($res>0)
					{
						$output = array('info' => "[DB] Neuer Termin wurde erfolgreich hinzugefügt.", 'data' => '');
					}
					else
					{
						$output = array('info' => "[DB] Neuer Termin konnte nicht hinzugefügt werden.", 'data' => '');
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
