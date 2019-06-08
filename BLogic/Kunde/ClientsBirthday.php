<?php
namespace Kunde;
require_once BASIS_DIR.'/MVC/DBFactory.php';
use PDO as PDO;
//twig
require_once BASIS_DIR . '/Vendor/autoload.php';

class ClientsBirthday {
  public function showList(){
	
	$vars = array();
	#$startMonth = !empty($_GET['start_month']) ? $_GET['start_month'] : "";
	#$endMonth = !empty($_GET['end_month']) ? $_GET['end_month'] : "";
	
	//date to check
	$date = date("m");
	$searchArr = array(":month" => $date);
	$q = "SELECT kundenNummer, anrede, vorname, name, geburtsdatum, email, muttersprache"
		." FROM kunden"
		." WHERE MONTH(geburtsdatum) = :month"
		." ORDER BY vorname ASC, name ASC, geburtsdatum ASC";
	
	$dbh = \MVC\DBFactory::getDBH();
	if(!$dbh)
	{
		return FALSE;
	}
	try
	{
		$sth = $dbh->prepare($q);
		$sth->execute($searchArr);
		$results = $sth->fetchAll(PDO::FETCH_ASSOC);

	}catch(Exception $ex){
		print_r($ex);
		return FALSE;
	}
	$vars['clients'] = $results;
	$options = []; #array('cache' => TWIG_CACHE_DIR);
	$loader = new \Twig_Loader_Filesystem(TWIG_TEMPLATE_DIR);
	$twig = new \Twig_Environment($loader, $options);
	$twigTmpl = $twig->load('/Kunde/ClientsBirthday/index.html.twig');
	echo $twigTmpl->render($vars);
  }
}
