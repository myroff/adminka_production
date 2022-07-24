<?php
namespace Tools;

class Filter
{
	public static function isRowString($str)
	{
		return preg_match("/^[a-zA-ZäöüÄÖÜß\s\-]+$/", $str);
	}

	public static function isWordsAndNumbers($str)
	{
		return preg_match("/^[0-9a-zA-ZäöüÄÖÜß\s]+$/", $str);
	}

	public static function isInt($x)
	{
		return preg_match("/^\d+$/", $x);
	}

	public static function isFloat($x)
	{
		return preg_match("/^\d+\.\d+$/", $x);
	}

	public static function isPrice($x)
	{
		return preg_match("/^(\-)?\d+((\.|\,)\d+)?$/", $x);
	}

	public static function filterStr($str)
	{
		$str = trim($str);

		//löschen doppelte Leerzeichen
		$str = preg_replace('/\s+/',' ', $str);

		//löschen alle Zeichen ausser Buchstaben und
		$str = preg_replace("/[^\w+\s\,\€\@\;\:\+\%\.\-\=\!\?\/]/u", "", $str);
		/* Empfohlen durch Симон Машкович. Gibt's nicht in DB. */
		return $str;
	}

	public static function deleteSpace($str)
	{
		//löschen Leerzeichen
		$str = preg_replace('/\s/','', $str);

		return $str;
	}

	public static function isDate($d)
	{
		if(preg_match("/\d\d\.\d\d\.\d\d\d\d/", $d))
		{
			$arr = explode('.', $d);

			return checkdate($arr[1], $arr[0], $arr[2]); //month,day,year
		}
		else return FALSE;
	}

	public static function isSqlDate($d)
	{
		if(preg_match("/\d\d\d\d\-\d\d\-\d\d/", $d))
		{
			$arr = explode('-', $d);

			return checkdate($arr[1], $arr[2], $arr[0]);
		}
		else return FALSE;
	}

	public static function strToSqlDate($d)
	{
		if(!self::isDate($d)) return false;

		$arr = explode('.', $d);

		return $arr[2]."-".$arr[1]."-".$arr[0];
	}

	public static function sqlDateToStr($d)
	{
		if(!self::isSqlDate($d)) return false;

		$arr = explode('-', $d);

		return $arr[2].".".$arr[1].".".$arr[0];
	}

	public static function sqlDateToMonatYear($d)
	{
		if(!self::isSqlDate($d)) return false;

		$arr = explode('-', $d);

		return $arr[1].".".$arr[0];
	}

	public static function isTelefone($tel)
	{
		return preg_match("/^[\+]{0,1}[0-9\s]+$/", $tel);
	}

	public static function isEmail($email)
	{
		//return preg_match("/^[a-zA-Z0-9\_\.\-]+\@[a-zA-Z0-9\-]+\.[a-zA-Z]{2,6}$/", $email);
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	public static function isStrasse($strasse)
	{
		return preg_match("/^[a-zA-ZäöüÄÖÜß\s\.\-]+$/", $strasse);
	}

	public static function isHausNr($haus)
	{
		return preg_match("/^[0-9A-Za-z\s\/\-]+$/", $haus);
	}

	public static function isPlz($haus)
	{
		return preg_match("/^[0-9]{5}$/", $haus);
	}

	public static function isText($text)
	{
		return preg_match("/^[0-9a-zA-ZäöüÄÖÜß\s\.\,\:\;\!\?\'\*\=\€\#\/\\\"\-\+\_]+$/", $text);
	}

	public static function indxToWeekday($ind)
	{
		if(empty($ind)) return '';

		$week = array(1=>"Mo.", 2=>"Di.", 3=>"Mi.", 4=>"Do.",
			5=>"Fr.", 6=>"Sa.", 7=>"So.");
		return $week[$ind];
	}

	public static function isStrTime($t)
	{
		return preg_match("/^[0-2]{1}[0-9]{1}:[0-6]{1}[0-9]{1}$/", $t);
	}

	public static function isSqlTime($t)
	{
		return preg_match("/^\d\d:\d\d:\d\d$/", $t);
	}

	public static function sqlTimeToStr($t)
	{
		if(!self::isSqlTime($t)) {return false;}

		$arr = explode(':', $t);

		return $arr[0].":".$arr[1];
	}
	/*
	public static function printSqlTermin($sqlStr, $delimiterTermin, $delimiterDayTime, $delimiterPrint)
	{
		$out = "";
		$tTermin = explode($delimiterTermin, $sqlStr);
		$ttTermin = array();

		foreach($tTermin as $t)
		{
			$ttTermin[] = explode($delimiterDayTime, $t);
		}

		foreach ($ttTermin as $t)
		{
			$out .= self::indxToWeekday(trim($t[0]))." ".$t[1].$delimiterPrint;
		}
		return $out;
	}

	public static function printSqlKursTermin($sqlStr, $delimiterKurs, $delimiterKursTermin, $delimiterTermin, $delimiterDayTime, $delimiterPrint)
	{
		if(empty($sqlStr))
			return FALSE;
		$out = "";

		$tKurs = explode($delimiterKurs, $sqlStr);

		foreach($tKurs as $tk)
		{
			$tmp = explode($delimiterKursTermin, $tk);
			$out .= "<b>".$tmp[0]."</b><br>"; //Kursname

			$out .= self::printSqlTermin($tmp[1], $delimiterTermin, $delimiterDayTime, $delimiterPrint);
		}

		return $out;
	}
	*/

	public static function printSqlKursTermin($sqlStr)
	{
		$out = "";
		$sql = "[".$sqlStr."]";

		$kurse = json_decode($sql,true);

		foreach ($kurse as $ind=>$k)
		{
			$color = strtotime("now") < strtotime($k['von']) ? "Dark yellow1" : "green";
			$color = strtotime("now") < strtotime($k['bis']) ? "green" : "red";
			$out .= "<i>".$k['vorname']." ".$k['name']."</i><br>";
			$out .= "<b>".$k['kurName']."</b><br>"
					. "<b style='color:$color;'>[".date('d.m.y' ,strtotime($k['von']))."-".date('d.m.y', strtotime($k['bis']))."]</b>";
			foreach ($k['termin'] as $iind=>$t)
			{
				$out .= "<br>";
				$out .= self::indxToWeekday(trim($t['wochentag']));
				$out .= " ".$t['time'];
			}
			$out .= "<br><br>";
			//.self::indxToWeekday(trim($t[]['wochentag']));
		}
		return $out;
	}

	public static function printSqlTermin($sqlStr)
	{
		$out = "";
		$sql = "[".$sqlStr."]";
		$termin = json_decode($sql,true);

		foreach ($termin as $i=>$t)
		{
			$out .= self::indxToWeekday(trim($t['wochentag']));
			$out .= " ".$t['time'];
			$out .= "<br>";
		}

		return $out;
	}

	public static function getWeekdayFromInt($n)
	{
		$week = array(1=>"Montag", 2=>"Dienstag", 3=>"Mitwoch", 4=>"Donnerstag", 5=>"Freitag", 6=>"Samstag", 7=>"Sonntag");

		return $week[$n];
	}

	public static function printZahlungsArt($id)
	{
		$zArt = false;

		switch ($id) {
			case "0":
				$zArt = "Lastschrift";
				break;
			case "1":
				$zArt = "Bar";
				break;
			case "2":
				$zArt = "BAMF";
				break;
			case "3":
				$zArt = "Zuzahler";
				break;
			case "4":
				$zArt = "Selbstzahler";
				break;
			case "5":
				$zArt = "Überweisung";
				break;
			default:
				$zArt .= "Zahlungsart ist nicht gefunden.";
				break;
		}

		return $zArt;
	}
}