<?php
namespace Kunde;
use PDO as PDO;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;

class SonderPreis {
	public function ajaxEdit()
	{
		$fehler = "";
		$dataPost = array();
		$output = array();
		
		if(!isset($_POST['eintrId']) AND empty($_POST['eintrId']))
		{
			$fehler .= "eintrId fehlt.\n";
		}
		else
		{
			$dataPost[':eintrId'] = $_POST['eintrId'];
		}
		
		if(!isset($_POST['sonderPreis']) AND empty($_POST['sonderPreis']))
		{
			$fehler .= "sonderPreis fehlt.\n";
		}
		else
		{
			$_POST['sonderPreis'] = Fltr::deleteSpace($_POST['sonderPreis']);
			$_POST['sonderPreis'] = str_replace(',', '.', $_POST['sonderPreis']);
			if(Fltr::isPrice($_POST['sonderPreis']))
			{
				$dataPost[':sonderPreis'] = $_POST['sonderPreis'];
			}
			else
			{
				$fehler .= "sonderPreis ist kein Zahl.\n";
			}
		}
		
		if(!isset($_POST['khkIsStdPreis']) AND empty($_POST['khkIsStdPreis']))
		{
			$fehler .= "Zahlungstype fehlt.\n";
		}
		else
		{
			$_POST['khkIsStdPreis'] = Fltr::deleteSpace($_POST['khkIsStdPreis']);
			$dataPost[':khkIsStdPreis'] = $_POST['khkIsStdPreis'] === 'proStunde' ? 1 : 0;
		}
		
		if(!empty($fehler))
		{
			$output['status'] = $fehler;
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$q = "UPDATE kundehatkurse SET sonderPreis=:sonderPreis,khkIsStdPreis=:khkIsStdPreis WHERE eintrId=:eintrId";
		$res ="";
		
		require_once BASIS_DIR.'/MVC/DBFactory.php';
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			$output['status'] = "kein DBH";
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		try
		{
			$sth = $dbh->prepare($q);
			$res = $sth->execute($dataPost);
		}
		catch (Exception $ex) {
			$output['status'] = $ex;
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		$r = ($res>0) ? "ok" : "Update fehlgeschlagen\n".print_r($dataPost,1);
		
		$output['status'] = $r;
		header("Content-type: application/json");
		exit(json_encode($output));
	}
	
	public function ajaxDelete()
	{
		if(!isset($_POST['eintrId']) AND empty($_POST['eintrId']))
		{
			$fehler .= "eintrId fehlt.\n";
		}
		else
		{
			$dataPost[':eintrId'] = $_POST['eintrId'];
		}
		
		if(!empty($fehler))
		{
			$output['status'] = $fehler;
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$q = "UPDATE kundehatkurse SET sonderPreis=NULL, khkIsStdPreis=NULL WHERE eintrId=:eintrId";
		$res ="";
		
		require_once BASIS_DIR.'/MVC/DBFactory.php';
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			$output['status'] = "kein DBH";
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		try
		{
			$sth = $dbh->prepare($q);
			$res = $sth->execute($dataPost);
		}
		catch (Exception $ex) {
			$output['status'] = $ex;
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		$r = ($res>0) ? "ok" : "Entfeernen fehlgeschlagen\n".print_r($dataPost,1);
		
		$output['status'] = $r;
		header("Content-type: application/json");
		exit(json_encode($output));
	}
}
