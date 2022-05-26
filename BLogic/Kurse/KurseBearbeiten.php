<?php
namespace Kurse;
use PDO as PDO;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;

class KurseBearbeiten
{
	public function showList()
	{
		$sArr = array();
		$sArr[':kurName'] = empty($_POST['kurName']) ? '' : $_POST['kurName'];
		$sArr[':kurAlter'] = empty($_POST['kurAlter']) ? '' : $_POST['kurAlter'];
		$sArr[':kurKlasse'] = empty($_POST['kurKlasse']) ? '' : $_POST['kurKlasse'];

		$res = $this->searchDates($sArr);

		include_once BASIS_DIR.'/Templates/Kurse/KurseBearbeitenListe.tmpl.php';
		return;
	}

	private function searchDates($searchArr)
	{

		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}

		$where = "";

		//delete empty entries
		$searchArr = array_filter($searchArr);
		$q = "SELECT k.*, l.vorname, l.name, st.raum, st.wochentag, TIME_FORMAT(anfang, '%H:%i') as anfang, TIME_FORMAT(ende, '%H:%i') as ende"
				. " FROM kurse as k LEFT JOIN stundenplan as st USING(kurId) LEFT JOIN lehrer as l USING(lehrId) ";

		if(!empty($searchArr))
		{
			if(isset($searchArr[':kurName']))
			{
				$searchArr[':kurName'] .= '%';
				$where .= " kurName LIKE :kurName AND";
			}
			if(isset($searchArr[':kurAlter']))
			{
				$where .= " :kurAlter BETWEEN kurMinAlter AND kurMaxAlter AND";
			}
			if(isset($searchArr[':kurKlasse']))
			{
				$where .= " :kurKlasse BETWEEN kurMinKlasse AND kurMaxKlasse AND";
			}

			$where = substr($where, 0, -4);
			$q .= empty($where) ? '' : " WHERE " . $where;
		}

		$q .= " ORDER BY wochentag, anfang, raum ";

		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute($searchArr);
			$rs = $sth->fetchAll(PDO::FETCH_ASSOC);

			return $rs;

		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
	}

	public function showKursById($kurId)
	{
		$meldung;

		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}

		if(!empty($_POST['updateItemTable_Form_Name']))
		{
			$itemName = Fltr::filterStr($_POST['updateItemTable_Form_Name']);
		}

		if(isset($_POST['updateItemTable_Form_Value']))
		{
			$itemValue = Fltr::filterStr($_POST['updateItemTable_Form_Value']);
		}

		if(isset($itemName) AND isset($itemValue))
		{
			$meldung = self::updateItemInDB($kurId,$itemName,$itemValue);
		}

		$q = "SELECT k.*, l.name as 'lehrName', l.vorname as 'lehrVorname' FROM kurse as k LEFT JOIN lehrer as l USING(lehrId) WHERE kurId=:kurId";
		$t = "SELECT std.*, sea.season_name, sea.season_id FROM stundenplan as std LEFT JOIN seasons as sea USING(season_id) WHERE kurId = :kurId";
		$res = array();
		$trm = array();
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute(array(":kurId" => $kurId));
			$res = $sth->fetch(PDO::FETCH_ASSOC, 1);

			$sth = $dbh->prepare($t);
			$sth->execute(array(":kurId" => $kurId));
			$trm = $sth->fetchAll(PDO::FETCH_ASSOC);

		} catch (Exception $ex) {
			print $ex;
			return FALSE;
		}

		include_once BASIS_DIR.'/Templates/Kurse/KursBearbeitenById.tmpl.php';
		return;
	}

	private function updateItemInDB($kurId, $itemName, $itemVal)
	{

		if(!$kurId)
		{
			return "Es wurde keine kurerrichts ID übermittelt.";
		}

		// $in für $itemName
		$set = "";
		$dataPost = array();

		switch ($itemName)
		{
			case "Name":
				if(Fltr::isText($itemVal))
				{
					$set = " kurName = :kurName";
					$dataPost[':kurName'] = $itemVal;
				}
				else
				{
					return "Der Name des Unterrichts ist falsch eingegeben.<br>Erlaubt sind Ziffern 0 bis 9, Buchstaben, Leerzeichen, Symbolen (. , : ; ! ? ' * = # / \ \" - + _).";
				}
				break;

			case "Max.Kunden":
				if(Fltr::isInt($itemVal))
				{
					$set = " maxKnd = :maxKnd";
					$dataPost[':maxKnd'] = $itemVal;
				}
				else
				{
					return "Maximale Anzahl der Kunden in der Gruppe soll ein Ganzzahl sein";
				}
				break;

			case 'Lehrer':
				$set = " lehrId = :lehrId";
				$dataPost[':lehrId'] = empty($itemVal) ?  NULL : $itemVal;
				break;
			case "Beschreibung":
				if(Fltr::isText($itemVal) OR empty($itemVal))
				{
					$set = " kurBeschreibung = :kurBeschreibung";
					$dataPost[':kurBeschreibung'] = $itemVal;
				}
				else
				{
					return "Der Name des Unterrichts ist falsch eingegeben.<br>Erlaubt sind Ziffern 0 bis 9, Buchstaben, Leerzeichen, Symbolen (. , : ; ! ? ' * = # / \ \" - + _).";
				}
				break;

			case 'Preis':
				$itemVal = str_replace(" ", "", $itemVal);
				$itemVal = str_replace(",", ".", $itemVal);
				if(Fltr::isPrice($itemVal))
				{
					$set = " kurPreis = :kurPreis ";
					$dataPost[':kurPreis'] = $itemVal;
				}
				else
				{
					return "Preis des Kurses ist falsch eingegeben.";
				}
				break;

			case 'Zahlungstype':
				$itemVal = str_replace(" ", "", $itemVal);
				if(Fltr::isRowString($itemVal))
				{
					$set = " kurIsStdPreis = :kurIsStdPreis ";
					$dataPost[':kurIsStdPreis'] = $itemVal === 'proStunde' ? 1 : 0;
				}
				else
				{
					return "Zahlungstyp des Kurses ist falsch eingegeben.";
				}
				break;

			case "Alter":
				if( !isset($_POST['kurMinAlter']) AND !isset($_POST['kurMaxAlter']) )
				{
					return "Minimaler und Maximaler Alter fehlt.";
				}

				$_POST['kurMinAlter'] = str_replace(" ", "", $_POST['kurMinAlter']);
				$_POST['kurMaxAlter'] = str_replace(" ", "", $_POST['kurMaxAlter']);
				if( !empty($_POST['kurMinAlter']) AND empty($_POST['kurMaxAlter']) )
				{
					$_POST['kurMaxAlter'] = $_POST['kurMinAlter'];
				}
				elseif( empty($_POST['kurMinAlter']) AND !empty($_POST['kurMaxAlter']) )
				{
					$_POST['kurMinAlter'] = $_POST['kurMaxAlter'];
				}
				elseif(empty($_POST['kurMinAlter']) AND empty($_POST['kurMaxAlter']) )
				{
					$_POST['kurMinAlter'] = NULL;
					$_POST['kurMaxAlter'] = NULL;
				}

				$set = " kurMinAlter = :kurMinAlter , kurMaxAlter = :kurMaxAlter ";
				$dataPost[':kurMinAlter'] = $_POST['kurMinAlter'];
				$dataPost[':kurMaxAlter'] = $_POST['kurMaxAlter'];

				break;

			case "Klassen":
				if( !isset($_POST['kurMinKlasse']) AND !isset($_POST['kurMaxKlasse']) )
				{
					return "Minimaler und Maximaler Klassen fehlen.";
				}

				$_POST['kurMinKlasse'] = str_replace(" ", "", $_POST['kurMinKlasse']);
				$_POST['kurMaxKlasse'] = str_replace(" ", "", $_POST['kurMaxKlasse']);
				if( !empty($_POST['kurMinKlasse']) AND empty($_POST['kurMaxKlasse']) )
				{
					$_POST['kurMaxKlasse'] = $_POST['kurMinKlasse'];
				}
				elseif( empty($_POST['kurMinKlasse']) AND !empty($_POST['kurMaxKlasse']) )
				{
					$_POST['kurMinKlasse'] = $_POST['kurMaxKlasse'];
				}
				elseif(empty($_POST['kurMinKlasse']) AND empty($_POST['kurMaxKlasse']) )
				{
					$_POST['kurMinKlasse'] = NULL;
					$_POST['kurMaxKlasse'] = NULL;
				}

				$set = " kurMinKlasse = :kurMinKlasse , kurMaxKlasse = :kurMaxKlasse ";
				$dataPost[':kurMinKlasse'] = $_POST['kurMinKlasse'];
				$dataPost[':kurMaxKlasse'] = $_POST['kurMaxKlasse'];
				break;
			//die Frage beim User ist "Kurs aktiv?"
			case "isKurInactive":
				if($itemVal === "ja" OR $itemVal === "nein")
				{
					$set = " isKurInactive = :isKurInactive";
					$dataPost[':isKurInactive'] = ($itemVal === "ja") ? 1 : 0;
				}
				else
				{
					return "Antwort auf die Frage \"Kurs aktiv?\" kann entweder 'ja' oder 'nein'.";
				}
				break;

			case "KursAnfangsdatum":
				if(Fltr::isDate($itemVal))
				{
					$set = " date_start = :date_start";
					$dataPost[':date_start'] = Fltr::strToSqlDate($itemVal);
				}
				elseif(Fltr::isSqlDate($itemVal))
				{
					$set = " date_start = :date_start";
					$dataPost[':date_start'] = $itemVal;
				}
				else
				{
					return "Dateformat ist unbekannt: [$itemVal]";
				}
				break;
			case "KursEnddatum":
				if(Fltr::isDate($itemVal))
				{
					$set = " date_end = :date_end";
					$dataPost[':date_end'] = Fltr::strToSqlDate($itemVal);
				}
				elseif(Fltr::isSqlDate($itemVal))
				{
					$set = " date_end = :date_end";
					$dataPost[':date_end'] = $itemVal;
				}
				else
				{
					return "Dateformat ist unbekannt: [$itemVal]";
				}
				break;

			default :
				return "kein passendes Item gefunden." . "<br>itemName=$itemName<br>itemValue=$itemVal";
				break;
		}

		$q = "UPDATE kurse SET $set WHERE kurId = $kurId";


		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return "kein DBH";
		}

		try
		{
			$sth = $dbh->prepare($q);
			$res = $sth->execute($dataPost);
		} catch (Exception $ex) {
			print $ex;
			return "Fehler beim Update.";
		}
		$r = ($res>0) ? "$itemName wurde erfolgreich geändert." : "$itemName konnte nicht geändert werden. Wahrscheinlich, Fehler im Datenbank.";
		return $r;
	}
}
