<?php
namespace User;
use PDO as PDO;
require_once BASIS_DIR.'/MVC/DBFactory.php';
use MVC\DBFactory as DBFactory;

class newUser {
	public function getMitarbeiterListJson(){
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			$output['status'] = "error";
			$output['message'] = "keine Verbindung zur DB.";
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$q = "SELECT mtId, vorname, name FROM mitarbeiter ORDER BY vorname";
		$mitarbeiter = [];
		
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute();
			$mitarbeiter = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		} catch (Exception $ex) {
			$output['status'] = "error";
			$output['message'] = "Fehler bei SQL-Abfrage";
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		//exit(print_r($mitarbeiter));
		$output['status'] = "ok";
		$output['message'] = $mitarbeiter;

		header("Content-type: application/json");
		exit(json_encode($output));
	}//public function getMitarbeiterListJson()
	
	public function getMitarbeiterInfoJson(){
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			$output['status'] = "error";
			$output['message'] = "keine Verbindung zur DB.";
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$mtId;
		
		if( !empty($_POST['mtId']) ){
			$mtId = (int)$_POST['mtId'];
		}
		else{
			$output['status'] = "error";
			$output['message'] = "Mitarbeiters ID fehlt.";
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$qHasLogin = "SELECT * FROM mtblogin WHERE mtId=:mtId";
		$q = "SELECT mtId, vorname, name FROM mitarbeiter WHERE mtId=:mtId";
		$mitarbeiter = [];
		
		try
		{
			$sth = $dbh->prepare($qHasLogin);
			$sth->execute( array(":mtId"=>$mtId) );
			$hasLogin = $sth->fetch(PDO::FETCH_ASSOC,1);
			
			if($hasLogin){
				$output['status'] = "error";
				$output['message'] = "Der Mitarbeiter hat schon einen Login: ".$hasLogin['login'];

				header("Content-type: application/json");
				exit(json_encode($output));
			}
			else{
				$sth = $dbh->prepare($q);
				$sth->execute( array(":mtId"=>$mtId) );
				$mitarbeiter = $sth->fetch(PDO::FETCH_ASSOC,1);
			}
		
		} catch (Exception $ex) {
			$output['status'] = "error";
			$output['message'] = "Fehler bei SQL-Abfrage";
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		//exit(print_r($mitarbeiter));
		$output['status'] = "ok";
		$output['message'] = $mitarbeiter;

		header("Content-type: application/json");
		exit(json_encode($output));
	}
}
