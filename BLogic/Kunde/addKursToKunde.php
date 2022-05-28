<?php
namespace Kunde;
use PDO as PDO;
use Tools\Filter as Fltr;

class addKursToKunde
{
	public function ajaxAddKurs()
	{
		$fehler = "";
		$dataPost = array();
		$testData = array();
		$output = array();

		if( !isset($_POST['kndId']) OR empty($_POST['kndId']) OR !Fltr::isInt(($_POST['kndId'])))
		{
			$fehler .= "Kunden Id fehlt oder ist kein Integer.";
		}
		else
		{
			$dataPost[':kndId'] = $_POST['kndId'];
			$testData[':kndId'] = $_POST['kndId'];
		}

		if( !isset($_POST['kurId']) OR empty($_POST['kurId']) OR !Fltr::isInt(($_POST['kurId'])))
		{
			$fehler .= "Kurs-Id fehlt oder ist kein Integer.";
		}
		else
		{
			$dataPost[':kurId'] = $_POST['kurId'];
			$testData[':kurId'] = $_POST['kurId'];
		}

		if( !isset($_POST['seasonId']) OR empty($_POST['seasonId']) OR !Fltr::isInt(($_POST['seasonId'])))
		{
			$fehler .= "Season-Id fehlt oder ist kein Integer.";
		}
		else
		{
			$dataPost[':season_id'] = $_POST['seasonId'];
			$testData[':season_id'] = $_POST['seasonId'];
		}

		if(!isset($_POST['von']) OR empty($_POST['von']))
		{
			$fehler .= "Anfangsmonat fehlt.";
		}
		else
		{
			if(Fltr::isDate($_POST['von']))//(preg_match("/^\d\d\.\d\d\d\d$/", $_POST['von']))
			{
				$dataPost[':von'] = Fltr::strToSqlDate($_POST['von']);
				$testData[':von'] = $dataPost[':von'];
			}
			elseif(Fltr::isSqlDate($_POST['von'])) {
				$dataPost[':von'] = $_POST['von'];
				$testData[':von'] = $dataPost[':von'];
			}
			else
			{
				$fehler .= "Anfangsmonat: falsher format.";
			}
		}

		if(!isset($_POST['bis']) OR empty($_POST['bis']))
		{
			$fehler .= "Endmonat fehlt.";
		}
		else
		{
			if(Fltr::isDate($_POST['bis']))
			{
				$dataPost[':bis'] = Fltr::strToSqlDate($_POST['bis']);
				$testData[':bis'] = $dataPost[':bis'];
			}
			elseif(Fltr::isSqlDate($_POST['bis'])) {
				$dataPost[':bis'] = $_POST['bis'];
				$testData[':bis'] = $dataPost[':bis'];
			}
			else
			{
				$fehler .= "Endmonat: falsher format.";
			}
		}

		if(isset($_POST['isSonderPreisSet']))
		{
			if(isset($_POST['sonderPreis']) )//AND !empty($_POST['sonderPreis'])
			{
				$_POST['sonderPreis'] = Fltr::deleteSpace($_POST['sonderPreis']);
				$_POST['sonderPreis'] = str_replace(',', '.', $_POST['sonderPreis']);
				if(Fltr::isPrice($_POST['sonderPreis']))
				{
					$dataPost[':sonderPreis'] = $_POST['sonderPreis'];
				}
				else
				{
					$fehler .= "Sonderpreis hat das falsches Format.";
				}
			}
			else{
				$fehler .= "Sonderpreis fehlt. SonderPrice = ".$_POST['sonderPreis'];
			}

			if(isset($_POST['khkIsStdPreis']) AND !empty($_POST['khkIsStdPreis']))
			{
				$dataPost[':khkIsStdPreis'] = $_POST['khkIsStdPreis'] === "proStunde" ? 1 : 0;
			}
			else{
				$fehler .= "Zahlungstype fehlt.";
			}
		}

		if(isset($_POST['khkKomm']) OR !empty($_POST['khkKomm']))
		{
			$dataPost[':khkKomm'] = Fltr::filterStr($_POST['khkKomm']);

		}

		if(empty($fehler))
		{
			$dbh = \MVC\DBFactory::getDBH();

			if(!$dbh)
			{
				$output = array('status' => 'error', 'info' => "no connection to db (dbh).");
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
			$curAdminId = \Tools\User::getCurrentUserId();

			$tbl .= "erstelltVom";
			$vl .= "'".$curAdminId."'";

			$q = "INSERT INTO kundehatkurse (".$tbl.") VALUES(".$vl.")";
			$tq = "SELECT count(*) as 'count' FROM kundehatkurse WHERE kndId=:kndId AND season_id=:season_id AND kurId=:kurId AND ( (:von BETWEEN von AND bis) OR (:bis BETWEEN von AND bis) )";

			try
			{
				$sthTest = $dbh->prepare($tq);
				$sthTest->execute($testData);
				$resTest = $sthTest->fetch(PDO::FETCH_ASSOC, 1);

				if($resTest['count'] > 0)
				{
					$output = array('status' => 'error', 'info' => "Der Kunde ist schon zu diesem Unterricht zu diesem Zeitraum angemeldet.");

					header("Content-type: application/json");
					exit(json_encode($output));
				}
				else
				{
					$sth = $dbh->prepare($q);
					$res = $sth->execute($dataPost);

					if($res>0)
					{
						$output = array('status' => 'ok', 'info' => "[DB] Neuer Unterricht wurde erfolgreich hinzugefügt.");
					}
					else
					{
						$output = array('status' => 'error', 'info' => "[DB] Neuer Unterrich konnte nicht hinzugefügt werden.");
					}
				}
			}
			catch (Exception $ex) {
				$output = array('status' => 'error','info' => $ex);
			}
		}
		else
		{
			$output = array('status' => 'error', 'info' => $fehler);
		}

		header("Content-type: application/json");
		exit(json_encode($output));
	}

	public function ajaxUpdateKurs()
	{
		$fehler = "";
		$dataPost = array();
		$output = array();

		if( !isset($_POST['eintrId']) OR empty($_POST['eintrId']) OR !Fltr::isInt(($_POST['eintrId'])))
		{
			$fehler .= "EintragId fehlt oder ist kein Integer.";
		}
		else
		{
			$dataPost[':eintrId'] = $_POST['eintrId'];
		}

		if( !isset($_POST['kndId']) OR empty($_POST['kndId']) OR !Fltr::isInt(($_POST['kndId'])))
		{
			$fehler .= "Kunden Id fehlt oder ist kein Integer.";
		}
		else
		{
			$dataPost[':kndId'] = $_POST['kndId'];
		}

		if( !isset($_POST['kurId']) OR empty($_POST['kurId']) OR !Fltr::isInt(($_POST['kurId'])))
		{
			$fehler .= "Unterricht Id fehlt oder ist kein Integer.";
		}
		else
		{
			$dataPost[':kurId'] = $_POST['kurId'];
		}

		if(isset($_POST['typeVal']) AND !empty($_POST['typeVal']) AND ($_POST['typeVal']==="von" OR $_POST['typeVal']==="bis") )
		{
			$dataPost[':typeVal'] = $_POST['typeVal'];
			if(!isset($_POST['dateVal']) OR empty($_POST['dateVal']))
			{
				$fehler .= "Betrag für Datum fehlt.";
			}
			else
			{
				if(Fltr::isDate($_POST['dateVal']))
				{
					$dataPost[':dateVal'] = Fltr::strToSqlDate($_POST['dateVal']);
				}
				else
				{
					$fehler .= "Datum (Tag.Monat.Jahr: dd.mm.jjjj): falsher format. ->".$_POST['dateVal'];
				}
			}
		}
		else
		{
			$fehler .= "Type des Datum fehlt oder ist falsch.";
		}

		if(empty($fehler))
		{
			$dbh = \MVC\DBFactory::getDBH();

			if(!$dbh)
			{
				$output = array('fehler' => "no connection to db (dbh).");
				header("Content-type: application/json");
				exit(json_encode($output));
			}

			$tq = "SELECT * FROM kundehatkurse WHERE eintrId=:eintrId";
			$q = "UPDATE kundehatkurse SET  ".$dataPost[':typeVal']."=:dateVal WHERE eintrId=:eintrId";

			try
			{
				$sthTest = $dbh->prepare($tq);
				$sthTest->execute(array(':eintrId' => $dataPost[':eintrId']));
				$resTest = $sthTest->fetch(PDO::FETCH_ASSOC, 1);

				$d1; $d2;
				if($dataPost[':typeVal'] === "von")
				{
					$d1 = new \DateTime($dataPost[':dateVal']);
					$d2 = new \DateTime($resTest['bis']);
				}
				else
				{
					$d1 = new \DateTime($resTest['von']);
					$d2 = new \DateTime($dataPost[':dateVal']);
				}

				if($d1 <= $d2)
				{
					$sth = $dbh->prepare($q);
					$sth->execute(array(':eintrId' => $dataPost[':eintrId'],
										':dateVal' => $dataPost[':dateVal']));
				}
				else
				{
					$output['fehler'] = "Anfangsdatum ist größer als Enddatum";
				}
			}
			catch (Exception $ex) {
				$output['fehler'] = $ex;
			}

			$output['info'] = json_encode($dataPost);
		}
		else
		{
			$output['fehler'] = $fehler;
		}

		header("Content-type: application/json");
		exit(json_encode($output));
	}
}
