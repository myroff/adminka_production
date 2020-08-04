<?php
/* 
 * my own functions.
*/
class Twig_Extension_CustomTools extends Twig_Extension
{
  
	public function getFunctions()
	{
		return [
			new Twig_Function('getPhpGlobals',		[$this, 'getPhpGlobals'], ['is_safe' => array('all')]),
			new Twig_Function('getAdminMenu',		[$this, 'getAdminMenu'], ['is_safe' => array('all')]),
			new Twig_Function('printSqlKursTermin',	[$this, 'printSqlKursTermin'], ['is_safe' => array('all')]),
			new Twig_Function('indxToWeekday',		[$this, 'indxToWeekday'], ['is_safe' => array('all')]),
			new Twig_Function('getWeekdayFromInt',	[$this, 'getWeekdayFromInt'], ['is_safe' => array('all')]),
			new Twig_Function('printSqlTermin',		[$this, 'printSqlTermin'], ['is_safe' => array('all')]),
		];
	}

	public function getPhpGlobals($globalsName)
	{
	  return constant($globalsName);
	}
	public function getAdminMenu()
	{
	  require_once BASIS_DIR.'/Templates/Menu.class.php';
	  TemplateTools\Menu::adminMenu();
	}
  
	public function printSqlKursTermin($sqlStr)
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
				$out .= $this->indxToWeekday(trim($t['wochentag']));
				$out .= " ".$t['time'];
			}
			#$out .= "<br><br>";
			//.self::indxToWeekday(trim($t[]['wochentag']));
		}
		return $out;
	}
	
	public static function indxToWeekday($ind)
	{
		if(empty($ind)) return '';
		
		$week = array(1=>"Mo.", 2=>"Di.", 3=>"Mi.", 4=>"Do.",
			5=>"Fr.", 6=>"Sa.", 7=>"So.");
		return $week[$ind];
	}
	
	public static function getWeekdayFromInt($n)
	{
		$week = array(1=>"Montag", 2=>"Dienstag", 3=>"Mitwoch", 4=>"Donnerstag", 5=>"Freitag", 6=>"Samstag", 7=>"Sonntag");
		
		return $week[$n];
	}
	
	public static function printSqlTermin($sqlStr)
	{
		$out = "";
		$sql = "[".$sqlStr."]";
		$termin = json_decode($sql,true);
		
		foreach ($termin as $i=>$t)
		{
			#$out .= "<br>";
			$out .= self::indxToWeekday(trim($t['wochentag']));
			$out .= " ".$t['time'];
		}
		
		return $out;
	}
}

class_alias('Twig_Extension_CustomTools', 'Twig\Extension\CustomToolsExtension', false);
#class_exists('Twig_Extension_CustomTools');