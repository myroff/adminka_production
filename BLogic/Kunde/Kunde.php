<?php
namespace Kunde;
use PDO as PDO;
//twig
require_once BASIS_DIR . '/Vendor/autoload.php';

require_once BASIS_DIR.'/Tools/TmplTools.php';
use Tools\TmplTools as TmplTls;

class Kunde 
{
	private $isCashImg = array(
		0 => "lastschrift.001.png",
		1 => "euro_cash.jpg",
		2 => "bamf.002.jpeg",
		3 => "euro_cash.jpg",
		4 => "euro_cash.jpg",
		5 => "ueberweisung.logo.png",
		);
	
	private function filterPostData()
	{
		$sArr = array();
		$sArr[':vorname']	  = empty($_POST['vorname'])	? '' : $_POST['vorname'];
		$sArr[':name']		  = empty($_POST['name'])		? '' : $_POST['name'];
		$sArr[':kurId']		  = empty($_POST['s_kurId'])	? '' : $_POST['s_kurId'];
		$sArr[':lehrId']	  = empty($_POST['s_lehrId'])	? '' : $_POST['s_lehrId'];
		$sArr[':wochentag']	  = empty($_POST['wochentag'])	? '' : $_POST['wochentag'];
		$sArr[':zeit']		  = empty($_POST['zeit'])		? '' : $_POST['zeit'];
		$sArr[':showIntegra'] = empty($_POST['showIntegra'])? '' : $_POST['showIntegra'];
		$sArr[':abgemeldet']  = empty($_POST['abgemeldet']) ? '' : $_POST['abgemeldet'];
		$sArr[':season']	  = empty($_POST['s_season'])	? '' : $_POST['s_season'];
		
		return $sArr;
	}
	
	public function showList()
	{
		$sArr = $this->filterPostData();
		
		$vars['clients']	= $this->searchDates($sArr);
		
		$vars['pageName']	= "Kundenliste";
		$vars['sArr']		= $sArr;
		$vars['s_kurId']	= TmplTls::getKursSelectorById("s_kurId", "s_kurId", $sArr[':kurId'], "Kurse", 1);
		$vars['s_lehrId']	= TmplTls::getLehrerSelector("s_lehrId", "s_lehrId", $sArr[':lehrId'], "Lehrer", 1);
		$vars['wochentag']	= TmplTls::getWeekdaySelector("wochentag", "wochentag", $sArr[':wochentag'], "Tag", 1);
		$vars['zeit']		= TmplTls::getTimeSelector("zeit", "zeit", $sArr[':zeit'], "Zeit");
		$vars['s_season']	= TmplTls::getSeasonsSelector("s_season", "s_season", $sArr[':season'], "Season", 1);
		$vars['isCashImg']	= $this->isCashImg;
		
		$options = []; #array('cache' => TWIG_CACHE_DIR);
		$loader = new \Twig_Loader_Filesystem(TWIG_TEMPLATE_DIR);
		$twig = new \Twig_Environment($loader, $options);
		$twigTmpl = $twig->load('/Kunde/KundenListe.twig');
		echo $twigTmpl->render($vars);
	}
	public function printList()
	{
		$sArr = array();
		$sArr = $this->filterPostData();
		
		$selectArr = "";
		$selectArr[] = isset($_POST['print_kndnr']) ? "k.kundenNummer as 'Knd.-Nr.'" : '';
		$selectArr[] = isset($_POST['print_anrede']) ? "k.anrede as 'Anrede'" : '';
		$selectArr[] = isset($_POST['print_vorname']) ? "k.vorname as 'Vorname'" : '';
		$selectArr[] = isset($_POST['print_name']) ? "k.name as 'Name'": '';
		$selectArr[] = isset($_POST['print_alter']) ? "TIMESTAMPDIFF(YEAR,k.geburtsdatum,CURDATE()) as 'Alter'": '';
		$selectArr[] = isset($_POST['print_geburtsdatum']) ? "k.geburtsdatum as 'Geburtsdatum'" : '';
		$selectArr[] = isset($_POST['print_telefon']) ? "k.telefon as 'Telefon'" : '';
		$selectArr[] = isset($_POST['print_handy']) ? "k.handy as 'Handy'" : '';
		$selectArr[] = isset($_POST['print_email']) ? "k.email as 'Email'" : '';
		
		$arrayToPrint = $this->searchDates($sArr, $selectArr);
//add title over table
		if( isset($_POST['print_titel']) && !empty($_POST['print_titel']) ){
			$arrayToPrint['print_titel'] = $_POST['print_titel'];
		}
		
		include_once BASIS_DIR.'/Templates/PrintArray.tmpl.php';
		return;
	}
	private function searchDates($searchArr, $selectArr=false)
	{
		require_once BASIS_DIR.'/MVC/DBFactory.php';
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}
		//$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, 10);
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
		if(isset($searchArr[':kurId']))
		{
			$where .= " kndKurse.kurId = :kurId AND";
		}
		if(isset($searchArr[':lehrId']))
		{
			$where .= " l.lehrId = :lehrId AND";
		}
		if(isset($searchArr[':wochentag']))
		{
			$where .= " st.wochentag = :wochentag AND";
		}
		if(isset($searchArr[':zeit']))
		{
			$where .= " TIME(:zeit) BETWEEN st.anfang AND st.ende AND";
		}
		
		if(isset($searchArr[':showIntegra']))//showIntegra = "i"
		{
			unset($searchArr[':showIntegra']);
		}else{
			$where .= " k.kundenNummer NOT LIKE 'i%' AND";
		}
		
		if(isset($searchArr[':abgemeldet']))//showIntegra = "i"
		{
			unset($searchArr[':abgemeldet']);
		}else{
			$where .= " NOW() BETWEEN kndKurse.von AND kndKurse.bis AND";
		}
		
		if(isset($searchArr[':season']))
		{
			$where .= " kndKurse.season_id=:season AND";
		}
		
		$where = substr($where, 0, -4);
		
		$select = "";
		if($selectArr){
			$selectArr = array_filter($selectArr);
			$select = implode(",", $selectArr);
		}
		else{
			$select = 
"khk.kndId as kndIdInKhk,k.*, z.isCash, TIMESTAMPDIFF(YEAR,k.geburtsdatum,CURDATE()) as 'alter',
	GROUP_CONCAT('{\"name\":\"',l.name, '\",\"vorname\":\"',l.vorname, '\",\"kurName\":\"', kr.KurName,'\",\"von\":\"',kndKurse.von,'\",\"bis\":\"',kndKurse.bis,'\",\"termin\":[', st.termin, ']}' SEPARATOR ',') as kurse";
		}
		
		$q = 
"SELECT ".$select."
FROM kunden as k 
LEFT JOIN kundehatkurse as kndKurse USING(kndId) 
LEFT JOIN kurse as kr USING(kurId)
LEFT JOIN lehrer as l USING(lehrId)
LEFT JOIN zahlungsdaten as z USING(kndId)
LEFT JOIN
(
	SELECT kndId, season_id, von, bis
	FROM kundehatkurse
	WHERE EXTRACT(YEAR_MONTH FROM NOW()) BETWEEN EXTRACT(YEAR_MONTH FROM von) AND EXTRACT(YEAR_MONTH FROM bis)
	GROUP BY kndId
) as khk USING (kndId)
LEFT JOIN
(
	SELECT kurId, wochentag, anfang, ende, group_concat('{\"wochentag\":\"',wochentag,'\",\"time\":\"', TIME_FORMAT(anfang, '%H:%i'),' - ', TIME_FORMAT(ende, '%H:%i'),'\"}' SEPARATOR',') as termin
	FROM stundenplan
	GROUP BY kurId
)
as st USING (kurId)
";
		
		$q .= empty($where) ? '' : " WHERE " . $where;
		$q .= " GROUP BY k.kndId";
		//$q .= empty($where) ? '' : " HAVING " . $where;
		$q .= " ORDER BY LENGTH(k.kundenNummer) DESC, k.kundenNummer DESC";
		
		try
		{
			$dbh->exec("SET SESSION group_concat_max_len = 10000;");
			$sth = $dbh->prepare($q);
			$sth->execute($searchArr);
			$rs = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			return $rs;
			
		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
	}
}
