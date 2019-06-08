<?php
namespace Kunde;
use PDO as PDO;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;

class KursEntfernen {
	public function ajaxEntfernen() {
		$fehler = "";
		$output = array();
	//kndId
		if(!isset($_POST['eintrId']) OR empty($_POST['eintrId']))
		{
			$fehler .= "EintragID fehlt";
		}
		else
		{
			if(Fltr::isInt($_POST['eintrId']))
			{
				$dataPost[':eintrId'] = $_POST['eintrId'];
			}
			else
			{
				$fehler .= "EintragID ist KEIN INT-Wert";
			}
		}
		
		if(empty($fehler))
		{
			require_once BASIS_DIR.'/MVC/DBFactory.php';
			$dbh = \MVC\DBFactory::getDBH();
			
			if(!$dbh)
			{
				$output = array('fehler' => "no connection to db (dbh).");
				header("Content-type: application/json");
				exit(json_encode($output));
			}
			
			$q = "DELETE FROM kundehatkurse WHERE eintrId=".$dataPost[':eintrId'];
			
			try
			{
				$resCount = $dbh->exec($q);
				
				if($resCount > 0)
				{
					$output = array('info' => "Der Kurs wurde erfolgreich entfernt.");
				}
				else
				{
					$output = array('fehler' => "Der Kurs konnte nicht entfernt werden. (DB-Problem)");
				}
			}
			catch (Exception $ex) {
				$output = array('info' => $ex, 'data' => $dataPost);
			}
		}
		else {
			$output['fehler'] = $fehler;
		}
		
		header("Content-type: application/json");
		exit(json_encode($output));
	}
}
