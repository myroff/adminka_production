<?php
namespace Kunde;
use PDO as PDO;
//twig
require_once BASIS_DIR . '/Vendor/autoload.php';

require_once BASIS_DIR.'/BLogic/Kurse/Seasons.php';
use Kurse\Seasons as Seasons;

require_once BASIS_DIR.'/Tools/TmplTools.php';
use Tools\TmplTools as TmplTls;

class KundeWithLastschrift
{
	public function getList()
	{
		$sArr = array();
		$sArr[':vorname']	= empty($_POST['vorname'])	? '' : $_POST['vorname'];
		$sArr[':name']		= empty($_POST['name'])		? '' : $_POST['name'];
		$sArr[':lehrId']	= empty($_POST['s_lehrId'])	? '' : $_POST['s_lehrId'];
		$sArr[':wochentag']	= empty($_POST['wochentag'])? '' : $_POST['wochentag'];
		$sArr[':season']	= empty($_POST['s_season'])	? Seasons::getActiveSeason()['season_id'] : $_POST['s_season'];
		
		$vars['s_lehrId']	= TmplTls::getLehrerSelector("s_lehrId", "s_lehrId", $sArr[':lehrId'], "Lehrer", 1);
		$vars['wochentag']	= TmplTls::getWeekdaySelector("wochentag", "wochentag", $sArr[':wochentag'], "Tag", 1);
		$vars['clients']	= $this->searchDates($sArr);
		$vars['sArr']		= $sArr;
		$vars['s_season']	= TmplTls::getSeasonsSelector("s_season", "s_season", $sArr[':season'], "Season", 1);
		
		$options = []; #array('cache' => TWIG_CACHE_DIR);
		$loader = new \Twig_Loader_Filesystem(TWIG_TEMPLATE_DIR);
		$twig = new \Twig_Environment($loader, $options);
		$twigTmpl = $twig->load('/Kunde/KundeMitLastschrift.twig');
		echo $twigTmpl->render($vars);
		
		#include_once BASIS_DIR.'/Templates/Kunde/KundeWithLastschrift.tmpl.php';
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
		
		if(isset($searchArr[':vorname']))
		{
			$where .= " k.vorname LIKE :vorname AND";
			$searchArr[':vorname'] .= '%';
		}
		if(isset($searchArr[':name']))
		{
			$where .= " k.name LIKE :name AND";
			$searchArr[':name'] .= '%';
		}
		if(isset($searchArr[':lehrId']))
		{
			$where .= " l.lehrId = :lehrId AND";
		}
		if(isset($searchArr[':wochentag']))
		{
			$where .= " stn.wochentag = :wochentag AND";
		}
		if(isset($searchArr[':season']))
		{
			$where .= " khk.season_id = :season AND";
		}
		
		$where = substr($where, 0, -4);
		//echo "where = " . $where . "<br>";
		
		$q = "SELECT k.*, GROUP_CONCAT(kr.kurName) as kurse"
			." FROM kunden as k JOIN kundehatkurse as khk USING(kndId) LEFT JOIN kurse as kr USING(kurId)"
			." LEFT JOIN stundenplan as stn USING(kurId)"
			." LEFT JOIN zahlungsdaten as z USING(kndId)"
			." LEFT JOIN lehrer as l USING(lehrId)"
			." WHERE z.isCash <> 1";
		
		$q .= empty($where) ? '' : " AND " . $where;
		$q .= " GROUP BY k.kndId";
		$q .= " ORDER BY k.kundenNummer DESC";
		
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute($searchArr);
			$sth->execute();
			$rs = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			return $rs;
			
		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
	}
}
