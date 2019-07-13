<?php
namespace Archiv;
use PDO as PDO;
require_once BASIS_DIR.'/MVC/DBFactory.php';
use MVC\DBFactory as DBFactory;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;

class ArchivTools {
	public function copyKunde()
	{
		$kndId = "";
		#$toYear = (int)ACTUAL_YEAR+1;
		#$fromYear = ACTUAL_YEAR;
		$toYear = DB_PRODUCTION;
		$fromYear = (int)ACTUAL_YEAR - 1;
		$fehler = "";
		/*
		if(!empty($_POST['toYear'])){
			if(Fltr::isInt($_POST['toYear'])){
				$toYear = $_POST['toYear'];
			}
			else{
				$fehler .= "'toYear' soll ein Integer sein.\n";
			}
		}
		else{
			$fehler .= "'toYear' fehlt.\n";
		}
		*/
		if(!empty($_POST['kndId'])){
			if(Fltr::isInt($_POST['kndId'])){
				$kndId = $_POST['kndId'];
			}
			else{
				$fehler .= "'kndId' soll ein Integer sein.\n";
			}
		}
		else{
			$fehler .= "'kndId' fehlt.\n";
		}
		
		$dbh = DBFactory::getDBH();
		
		if(!$dbh)
		{
			$fehler = "no connection to db (dbh).\n";
		}
		
		if(!empty($fehler)){
			$output = array('status' => "error", 'message' => $fehler);
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$q = "INSERT INTO `swiff.crm.$toYear`.`kunden` SELECT * FROM `swiff.crm.".$fromYear."`.`kunden` WHERE kndId = :kndId";
		$qZ = "INSERT INTO `swiff.crm.".$toYear."`.`zahlungsdaten` SELECT * FROM `swiff.crm.".$fromYear."`.`zahlungsdaten` WHERE kndId = :kndId".
				" ON DUPLICATE KEY UPDATE `swiff.crm.".$toYear."`.`zahlungsdaten`.zdId=`swiff.crm.".$fromYear."`.`zahlungsdaten`.zdId;";
		$tq = "SELECT count( * ) AS 'count', kundenNummer"
			." FROM `swiff.crm.$toYear`.`kunden`"
			." WHERE kndId = :kndId";
		
		try
		{
			$sthTest = $dbh->prepare($tq);
			$sthTest->execute(array(':kndId' => $kndId));
			$resTest = $sthTest->fetch(PDO::FETCH_ASSOC, 1);

			if(intval($resTest['count']) > 0)
			{
				$output = array('status'=>"error", 'message' => "Der Kunde mit dem kundenNummer '".$resTest['kundenNummer']."' existiert schon in neuem DB '$toYear'\n");
			}
			else
			{
				$sth = $dbh->prepare($q);
				$res = $sth->execute(array(':kndId' => $kndId));
				$sth = $dbh->prepare($qZ);
				$res = $sth->execute(array(':kndId' => $kndId));

				if($res>0)
				{
					$output = array('status'=>"ok", 'message' => "Der Kunde wurde erfolgreich übertragen.\n");
				}
				else
				{
					$output = array('status'=>"error", 'message' => "[DB]Der Kunde mit dem ID '$kndId' konnte nicht übetragen werden.\n");
				}
			}
		}
		catch (Exception $ex) {
			$output = array('status'=>"error", message => $ex);
		}
		
		header("Content-type: application/json");
		exit(json_encode($output));
	}
}
