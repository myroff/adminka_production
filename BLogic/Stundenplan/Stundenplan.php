<?php
namespace Stundenplan;
use PDO as PDO;
require_once BASIS_DIR.'/MVC/DBFactory.php';
use MVC\DBFactory as DBFactory;

//twig
require_once BASIS_DIR . '/Vendor/autoload.php';

require_once BASIS_DIR.'/Tools/TmplTools.php';
use Tools\TmplTools as TmplTls;

require_once BASIS_DIR.'/Tools/User.php';
use Tools\User as User;

class Stundenplan {
	public function showStundeplan()
	{
		$sArr = array();
		$sArr[':wochentag'] = !empty($_GET['search_wochentag']) ? trim($_GET['search_wochentag']) : '';
		$sArr[':lehrId'] = !empty($_GET['search_lehrId']) ? trim($_GET['search_lehrId']) : '';
		$sArr[':raum'] = !empty($_GET['search_raum']) ? trim($_GET['search_raum']) : '';
		$sArr[':kurName'] = !empty($_GET['search_kurs']) ? trim($_GET['search_kurs']) : '';
		$sArr[':alter'] = !empty($_GET['search_alter']) ? trim($_GET['search_alter']) : '';
		$sArr[':klasse'] = !empty($_GET['search_klasse']) ? trim($_GET['search_klasse']) : '';
		
		$res = $this->searchDates($sArr);
		$raum = $this->getRaum();
		
		$this->loadTamplate($res, $raum, $sArr);
		return;
	}
	public function loadTamplate($res, $raum, $sArr)
	{
		$vars['pageName']	= "Kundenliste";
		$vars['sArr']		= $sArr;
		$vars['stunden']	= $this->sortStundenPlan($res);
		$vars['raum']		= $raum;
		$vars['s_kurId']	= TmplTls::getKursSelectorByCourseName("search_kurs", "s_kurId", $sArr[':kurName'], "Kurse", 1);
		$vars['s_lehrId']	= TmplTls::getLehrerSelector("search_lehrId", "s_lehrId", $sArr[':lehrId'], "Lehrer", 1);
		$vars['wochentag']	= TmplTls::getWeekdaySelector("search_wochentag", "wochentag", $sArr[':wochentag'], "Tag", 1);
		$vars['s_raum']		= TmplTls::getRaumSelector("search_raum", "search_raum", $sArr[':raum'], "Raum", 1);
		$vars['s_klasse']	= TmplTls::getKlasseSelector("search_klasse", "search_klasse", $sArr[':klasse'], "Klasse", 1);
		$vars['s_alter']	= TmplTls::getAlterSelector("search_alter", "search_alter", $sArr[':alter'], "Alter", 1);
		$vars['editTerminForm_wochentag']	= TmplTls::getWeekdaySelector("wochentag", "editTerminForm_wochentag", $sArr[':wochentag'], "Tag", 1);
		
		$vars['userGroups']	= User::getUserGroup();
		
		$options = []; #array('cache' => TWIG_CACHE_DIR);
		$loader = new \Twig_Loader_Filesystem(TWIG_TEMPLATE_DIR);
		$twig = new \Twig_Environment($loader, $options);
		$twigTmpl = $twig->load('/Stundenplan/StundenplanListe.twig');
		echo $twigTmpl->render($vars);
		
		
		#include_once  BASIS_DIR.'/Templates/Stundenplan/StundenplanListe.tmpl.php';
	}
	
	/* @param array $lessons - array with lessons ordered by: day, hour, room in ASC order.
	 * @return array: array('week_day' => array('hour' => array('room' =>'lesson info') ));
	 */
	public function sortStundenPlan($lessons)
	{
		$out = array();
		
		foreach($lessons as $stn)
		{
			$_d = (int)$stn['wochentag'];
			$_h = (int)date('G', strtotime($stn['anfang']) );
			$_r = $stn['raum'];
			$out[$_d][$_h][$_r][] = $stn;
		}
		#var_dump($out);die();
		return $out;
	}
	
	public function searchDates($searchArr)
	{
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}
		
		$where = " k.isKurInactive is NOT TRUE AND ";
		$having = "";
		
		//delete empty entries
		$searchArr = array_filter($searchArr);
		
		if(isset($searchArr[':raum']))
		{
			$having .= " stpl.raum = :raum AND";
		}
		if(isset($searchArr[':lehrId']))
		{
			$having .= " l.lehrId = :lehrId AND";
		}
		if(isset($searchArr[':wochentag']))
		{
			$where .= " stpl.wochentag = :wochentag AND";
		}
		if(isset($searchArr[':kurName']))
		{
			$where .= " k.kurName LIKE :kurName AND";
			$searchArr[':kurName'] .= '%';
		}
		if(isset($searchArr[':alter']))
		{
			$where .= "( :alter BETWEEN k.kurMinAlter AND k.kurMaxAlter) AND";
			$searchArr[':alter'] .= '%';
		}
		if(isset($searchArr[':klasse']))
		{
			$where .= "( :klasse BETWEEN k.kurMinKlasse AND k.kurMaxKlasse) AND";
			$searchArr[':klasse'] .= '%';
		}
		
		$having = substr($having, 0, -4);
		$where = substr($where, 0, -4);
		
		$q = "SELECT ( (TIME_TO_SEC(ende) - TIME_TO_SEC(anfang) )/60 ) as kurLength,"
				. " TIME_FORMAT(anfang, '%H:%i') as anfang, TIME_FORMAT(ende, '%H:%i') as ende, wochentag, raum,"
				. " k.*, l.name, l.vorname, l.lehrId, count(khk.kndId) as countKnd, k.maxKnd, stpl.stnPlId"
				. " FROM stundenplan as stpl"
				. " LEFT JOIN kurse as k USING(kurId)"
				. " LEFT JOIN lehrer as l USING(lehrId)"
				. " LEFT JOIN (SELECT * FROM kundehatkurse WHERE NOW() <= bis) as khk USING(kurId)";//BETWEEN von AND
		
		$q .= empty($where) ? '' : " WHERE ".$where;
		$q .= " GROUP By stpl.stnPlId ";
		
		$q .= empty($having) ? '' : " HAVING " . $having;
		$q .= " ORDER By stpl.wochentag, HOUR(stpl.anfang), cast(stpl.raum as unsigned) ASC";
		
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
	
	private function getRaum()
	{
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}
		
		$q = "SELECT raum FROM stundenplan"
			." GROUP BY raum"
			." ORDER BY CAST(raum AS UNSIGNED) ASC";
		
		try
		{
			
			$sth = $dbh->prepare($q);
			$sth->execute();
			$rs = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			return $rs;
			
		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
	}
}