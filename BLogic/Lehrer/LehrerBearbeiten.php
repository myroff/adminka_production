<?php
namespace Lehrer;
use PDO as PDO;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;

class LehrerBearbeiten
{
	public function showList()
	{
		$sArr = array();
		$sArr[':vorname'] = empty($_POST['vorname']) ? '' : $_POST['vorname'];
		$sArr[':name'] = empty($_POST['name']) ? '' : $_POST['name'];
		
		$res = $this->searchDates($sArr);
		
		include_once BASIS_DIR.'/Templates/Lehrer/LehrerBearbeitenListe.tmpl.php';
		return;
	}
	
	private function searchDates($searchArr)
	{
		require_once BASIS_DIR.'/MVC/DBFactory.php';
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}
		
		$where = "";
		
		//delete empty entries
		$searchArr = array_filter($searchArr);
		
		foreach ($searchArr as $key => $value)
		{
			$where .= substr($key, 1) . " LIKE ";
			$where .= $key;
			$searchArr[$key] .= "%";
			$where .= " AND ";
		}
		
		$where = substr($where, 0, -5);
		
		$q = "SELECT * FROM lehrer";
		$q .= empty($where) ? '' : " WHERE " . $where;


		
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
	
	public function showLehrerById($lId)
	{
		require_once BASIS_DIR.'/MVC/DBFactory.php';
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}
		
		if(!empty($_POST['updateItemTable_Form_Name']))
		{
			$itemName = trim($_POST['updateItemTable_Form_Name']);
		}
		
		if(!empty($_POST['updateItemTable_Form_Value']))
		{
			$itemValue = trim($_POST['updateItemTable_Form_Value']);
			$itemValue = preg_replace("/\s+/  ", " ", $itemValue);
		}
		
		if(isset($itemName) AND isset($itemValue))
		{
			$meldung = "itemName = $itemName<br>itemValue = $itemValue";
			$meldung .= "<br>" . self::updateItemInDB($lId,$itemName,$itemValue);
		}
		
		$q = "SELECT * FROM lehrer WHERE lehrId=:lId";
		$res = array();
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute(array(":lId" => $lId));
			$res = $sth->fetch(PDO::FETCH_ASSOC, 1);
		} catch (Exception $ex) {
			print $ex;
			return FALSE;
		}
		//set Geburtsdatum to string format
		$res['geburtsdatum'] = Fltr::sqlDateToStr($res['geburtsdatum']);
		$res['eingestelltAm'] = Fltr::sqlDateToStr($res['eingestelltAm']);
		$res['entlassenAm'] = Fltr::sqlDateToStr($res['entlassenAm']);
		
		include_once BASIS_DIR .'/Templates/Lehrer/LehrerBearbeitenById.tmpl.php';
		return;
	}
	
	private function updateItemInDB($lId, $itemName, $itemVal)
	{
		
		if(!$lId)
		{
			return "Es wurde keine kunden ID übermittelt.";
		}
		
		if(!$itemVal)
		{
			return "Es wurde keine kunden items Value übermittelt.";
		}
		
		// $in für $itemName
		$in = "";
		
		switch ($itemName)
		{
			case "Anrede":
				$in = "anrede";
				
				if(Fltr::isRowString($itemVal))
				{
					
				}
				else
				{
					return "Ziffern bei Anrede sind unzulässig.";
				}
				break;
				
			case "Vorname":
				$in = "vorname";
				
				if(Fltr::isRowString($itemVal))
				{
					
				}
				else
				{
					return "Beim Vornamen sind nur die Buchstaben erlaubt (klein und gross).";
				}
				break;
				
			case 'Name':
				$in = "name";
				
				if(Fltr::isRowString($itemVal))
				{
					
				}
				else
				{
					return "Beim Namen sind nur die Buchstaben erlaubt (klein und gross).";
				}
				break;
				
			case "Geburtsdatum(dd.mm.yyyy)":
				$in = "geburtsdatum";
				$itemVal = str_replace(" ", "", $itemVal);
				if(Fltr::isDate($itemVal))
				{
					$itemVal = Fltr::strToSqlDate($itemVal);
				}
				else
				{
					return "Geburtsdatum ist falsch eingegeben.<br>";
				}
				break;
			
			case "Eingestelltam":
				$in = "eingestelltAm";
				$itemVal = str_replace(" ", "", $itemVal);
				if(Fltr::isDate($itemVal))
				{
					$itemVal = Fltr::strToSqlDate($itemVal);
				}
				else
				{
					return "Einstellungsdatum ist falsch eingegeben.<br>";
				}
				break;
			
			case "Entlassenam":
				$in = "entlassenAm";
				$itemVal = str_replace(" ", "", $itemVal);
				if(Fltr::isDate($itemVal))
				{
					$itemVal = Fltr::strToSqlDate($itemVal);
				}
				else
				{
					return "Entlassungsdatum ist falsch eingegeben.<br>";
				}
				break;
				
			case 'Telefon':
				$in = "telefon";
				$itemVal = str_replace(" ", "", $itemVal);
				if(Fltr::isTelefone($itemVal))
				{
					
				}
				else
				{
					return "Telefon ist falsch eingegeben. Beispiel: +49 211 123 456 789.";
				}
				break;
			
			case 'Handy':
				$in = "handy";
				
				$itemVal = str_replace(" ", "", $itemVal);
				if(Fltr::isTelefone($itemVal))
				{
					
				}
				else
				{
					return "Handy ist falsch eingegeben. Beispiel: +49 151 123 456 789.";
				}
				break;
			
			case 'Email':
				$in = "email";
				
				$itemVal = str_replace(" ", "", $itemVal);
				if(Fltr::isEmail($itemVal))
				{
					
				}
				else
				{
					return "Email ist falsch eingegeben.<br>";
				}
				break;
			
			case 'Strasse':
				$in = "strasse";
				
				$itemVal = trim($itemVal);
				if(Fltr::isStrasse($itemVal))
				{
					
				}
				else
				{
					return "Beim Strassennamen sind keine Ziffern erlaubt. Bspl.: Musterstr. Allee.";
				}
				break;
			
			case 'Haus':
				$in = "strNr";
				
				$itemVal = str_replace(" ", "", $itemVal);
				if(Fltr::isHausNr($itemVal))
				{
					
				}
				else
				{
					return "Beim Hausnummer sind nur Ziffern und Buchstaben erlaubt. Bspl.: 123a.<br>";
				}
				break;
			
			case 'Stadt':
				$in = "stadt";
				
				if(Fltr::isRowString($itemVal))
				{
					
				}
				else
				{
					return "Bei der Stadt sind nur die Buchstaben erlaubt. Bspl.: Frankfurt am Main.<br>";
				}
				break;
			
			case 'PLZ':
				$in = "plz";
				
				$itemVal = str_replace(" ", "", $itemVal);
				if(Fltr::isPlz($itemVal))
				{
					
				}
				else
				{
					return "PLZ besteht aus genau 5 ziffern. Bspl.: 40210.";
				}
				break;
			
			case 'Fach':
				$in = "fach";
				
				if(Fltr::isText($itemVal))
				{
					
				}
				else
				{
					return "Fach(-er) ist(sind) falsch eingegeben.<br>Erlaubt sind Ziffern 0 bis 9, Buchstaben, Leerzeichen, Symbolen (. , : ; ! ? ' * = # / \ \" - + _).<br>";
				}
				break;
				
			default :
				return "kein passendes Item gefunden." . "<br>itemName=$itemName<br>itemValue=$itemVal";
				break;
		}
		
		$q = "UPDATE lehrer SET $in = :itemVal WHERE lehrId = $lId";
		
		require_once BASIS_DIR.'/MVC/DBFactory.php';
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return "kein DBH";
		}
		
		try
		{
			$sth = $dbh->prepare($q);
			$res = $sth->execute(array(":itemVal" => $itemVal));
		} catch (Exception $ex) {
			print $ex;
			return "Fehler beim Update.";
		}
		$r = ($res>0) ? "$itemName vurde erfolgreich geändert." : "$itemName konnte nicht geändert werden. Wahrscheinlich, Fehler im Datenbank.";
		return $r . "<br>itemName=$itemName<br>itemValue=$itemVal";
	}
}
