<?php
namespace Kunde;
use PDO as PDO;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;

class UpdateKundenKursKommentar
{
	public function ajaxUpdateKomm()
	{
		$fehler = "";
		$dataPost = array();
		$output = array();
		$eId; $komm;

		if(isset($_POST['eintrId']) AND !empty($_POST['eintrId']))
		{
			$dataPost[':eintrId'] = Fltr::deleteSpace($_POST['eintrId']);
			if(Fltr::isInt($dataPost[':eintrId']))
			{

			}
			else {
				$fehler = "Eintrags-ID ist kein Integer.";
			}
		}
		else
		{
			$fehler = "Eintrags-ID fehlt.";
		}

		if(isset($_POST['khkKomm']))
		{
			if(empty($_POST['khkKomm']))
			{
				$dataPost[':khkKomm'] = NULL;
			}
			else
			{
				$dataPost[':khkKomm'] = Fltr::filterStr($_POST['khkKomm']);
			}
		}
		else
		{
			$fehler = "Kommentar fehlt.";
		}

		if(!empty($fehler))
		{
			$output['status'] = $fehler;
			header("Content-type: application/json");
			exit(json_encode($output));
		}

		$q = "UPDATE kundehatkurse SET khkKomm=:khkKomm WHERE eintrId=:eintrId";
		$res ="";


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
}
