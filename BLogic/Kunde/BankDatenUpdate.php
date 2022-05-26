<?php
namespace Kunde;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;

class BankDatenUpdate {
	public function ajaxUpdate()
	{
		$fehler = "";
		$dataPost = array();
		$output = array();
		$varName = "";
		$varVal = "";

		if(!isset($_POST['kndId']) OR empty($_POST['kndId']))
		{
			$output['status'] = "Es wurde keine kunden ID übermittelt.";
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		else
		{
			$kndId = Fltr::deleteSpace($_POST['kndId']);
		}

		if(!isset($_POST['updateBankDates_Form_Name']) OR empty($_POST['updateBankDates_Form_Name']))
		{
			$output['status'] = "Es wurde keine Variablen-Namen übermittelt.";
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		else
		{
			$dataPost[':varName'] = Fltr::deleteSpace($_POST['updateBankDates_Form_Name']);
		}

		if(!isset($_POST['updateBankDates_Form_Value']) OR empty($_POST['updateBankDates_Form_Value']) AND (int)$_POST['updateBankDates_Form_Value'] !== 0)
		{
			$output['status'] = "Es wurde keine Variablen-Wert übermittelt.";
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		else
		{
			$dataPost[':varWert'] = $_POST['updateBankDates_Form_Value'];
		}

		switch ($dataPost[':varName']):
			case 'payment_id':
				$varName = "payment_id";
				$varVal = Fltr::deleteSpace($dataPost[':varWert']);
				$varVal = Fltr::filterStr($varVal);
				/*
				switch ($dataPost[':varWert']) {
					case "lastschrift":
						$varVal = 0;
						break;
					case "bar":
						$varVal = 1;
						break;
					case "bamf":
						$varVal = 2;
						break;
					case "zuzahler":
						$varVal = 3;
						break;
					case "selbstzahler":
						$varVal = 4;
						break;
					case "ueberweisung":
						$varVal = 5;
						break;
					default:
						$fehler .= "Zahlungsart ist falsch eingegeben. Erlaubt sind nur 'bar' oder 'lastschrift'.<br>";
						break;
				}
				/*
				if($dataPost[':varWert'] === "bar" OR $dataPost[':varWert'] === "lastschrift")
				{
					$varVal = $dataPost[':varWert'] === "bar" ? 1 : 0;
				}
				else
				{
					$fehler .= "Zahlungsart ist falsch eingegeben. Erlaubt sind nur 'bar' oder 'lastschrift'.<br>";
				}
				*/
				break;

			case 'kontoinhaber':
				$varName = 'kontoinhaber';
				$varVal = Fltr::filterStr($dataPost[':varWert']);
				break;

			case 'bank':
				$varName = 'bankName';
				$varVal = Fltr::filterStr($dataPost[':varWert']);
				break;

			case 'iban':
				$varName = 'iban';
				$varVal = Fltr::deleteSpace($dataPost[':varWert']);
				$varVal = Fltr::filterStr($varVal);

				break;

			case 'bic':
				$varName = 'bic';
				$varVal = Fltr::deleteSpace($dataPost[':varWert']);
				$varVal = Fltr::filterStr($varVal);

				break;

			case 'strasse':
				$varName = 'zdStrasse';
				$varVal = Fltr::filterStr($dataPost[':varWert']);
				if(Fltr::isStrasse($varVal))
				{
					$varVal = $dataPost[':varWert'];
				}
				else
				{
					$fehler = "Beim Strassennamen sind keine Ziffern erlaubt. Bspl.: Musterstr. Allee.";
				}

				break;

			case 'hausnummer':
				$varName = 'zdHausnummer';
				$varVal = Fltr::filterStr($dataPost[':varWert']);
				$varVal = Fltr::deleteSpace($varVal);
				if(Fltr::isHausNr($varVal))
				{

				}
				else
				{
					$fehler = "Beim Hausnummer sind nur Ziffern und Buchstaben erlaubt. Bspl.: 123a.";
				}

				break;

			case 'ort':
				$varName = 'zdOrt';
				$varVal = Fltr::filterStr($dataPost[':varWert']);
				$varVal = $dataPost[':varWert'];

				break;

			case 'plz':
				$varName = 'zdPlz';
				$varVal = Fltr::deleteSpace($dataPost[':varWert']);
				if(Fltr::isPlz($varVal))
				{
					$varVal = $dataPost[':varWert'];
				}
				else
				{
					$fehler = "PLZ besteht aus genau 5 ziffern. Bspl.: 41469.";
				}

				break;

			default :
				$fehler = "kein passendes Item gefunden. itemName=$varName. itemValue=$varVal";
				break;
		endswitch;

		if(!empty($fehler))
		{
			$output['status'] = $fehler;
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		/*
		$q = "INSERT INTO zahlungsdaten (kndId, $varName) VALUES ('$kndId', '$varVal')"
				. " ON DUPLICATE KEY UPDATE zahlungsdaten SET $varName = '$varVal' WHERE kndId = '$kndId'";
		*/

		$q = "INSERT INTO payment_data (kndId, $varName) VALUES ('$kndId', '$varVal')"
				. " ON DUPLICATE KEY UPDATE $varName = '$varVal'";


		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			$output['status'] = "kein DBH";
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		$res ="";
		try
		{
			$res = $dbh->exec($q);
		} catch (Exception $ex) {
			$output['status'] = $ex;
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		$r = ($res>0) ? "ok" : "$varName konnte nicht geändert werden. Wahrscheinlich, Fehler im Datenbank.";

		$output['status'] = $r;
		header("Content-type: application/json");
		exit(json_encode($output));
	}
}
