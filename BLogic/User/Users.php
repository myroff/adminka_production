<?php
namespace User;
use PDO as PDO;
require_once BASIS_DIR.'/MVC/DBFactory.php';
use MVC\DBFactory as DBFactory;

require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;

require_once BASIS_DIR.'/Tools/User.php';
use Tools\User as User;

class Users {
	public function getUsersList() {
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			$meldung = "DBFactory failed.";
		}
		
		$qGroups = "SELECT * FROM groups";
		
		$qUsers = "SELECT ln.mtId, ln.istAktiv, ln.login, mt.anrede, mt.vorname, mt.name, GROUP_CONCAT(grpName SEPARATOR ', ') as 'gruppen'"
			." FROM mtblogin as ln LEFT JOIN mitarbeiter as mt USING(mtId) LEFT JOIN mtbingrp USING(mtId) LEFT JOIN groups as grp USING(grpId)"
			." GROUP BY mtId";
		
		$groups = [];
		$users = [];
		
		try
		{
			$sth = $dbh->prepare($qGroups);
			$sth->execute();
			$groups = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			$sth = $dbh->prepare($qUsers);
			$sth->execute();
			$users = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		} catch (Exception $ex) {
			//print $ex;
			$meldung = $ex;
		}
		
		include_once BASIS_DIR.'/Templates/User/UsersListe.tmpl.php';
		return;
	}
	
	public function updatePassword(){
		$pswd = "";
		$mtId = "";
		$output = "";
		
		$userGroups = User::getUserGroup();
		
		if(!in_array("SuperAdmin", $userGroups)){
			$output = array('status' => "error", 'message' => "Sie haben keine Rechte für diese Operation.\n");
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
//mtId
		if( !isset($_POST['mtId']) OR empty($_POST['mtId']) ){
			$output = array('status' => "error", 'message' => "[POST] mtId fehlt.");
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		elseif(Fltr::isInt($_POST['mtId'])){
			$mtId = $_POST['mtId'];
		}
		else{
			$output = array('status' => "error", 'message' => "[Fltr] mtId darf nut die Ziffern beinhalten.");
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
//newPswd
		if( !isset($_POST['pswd']) OR empty($_POST['pswd']) )
		{
			$output = array('status' => "error", 'message' => "[POST] Neues Password fehlt.");
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		elseif(Fltr::isWordsAndNumbers($_POST['pswd'])){
			$pswd = str_replace($delete, '', $_POST['pswd']);;
		}
		else{
			$output = array('status' => "error", 'message' => "[Fltr] Das Passwort darf nut die Buchstaben und Ziffern beinhalten.");
			header("Content-type: application/json");
			exit(json_encode($output));
		}
//generate password
		$salt = substr(md5(time()),0, 10);
		$newPswd = hash('sha512', $salt.$pswd);
		
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			$output = array('status' => "error", 'message' => "[DB] Verbindung mit DB fehlgeschlagen.");
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$qUpdate = "UPDATE mtblogin SET pswd=:pswd, salt=:salt, istAktiv=1 WHERE mtId=:mtId";
		$qTest = "SELECT count(mtId) as 'count' FROM mtblogin WHERE mtId=:mtId";
		
		try
		{
			$sthTest = $dbh->prepare($qTest);
			$sthTest->execute(array(':mtId' => $mtId));
			$resTest = $sthTest->fetch(PDO::FETCH_ASSOC, 1);

			if(intval($resTest['count']) === 0)
			{
				$output = array('status' => "error", 'message' => "Login ist noch nicht eingetragen.");
			}
			else
			{
				$sth = $dbh->prepare($qUpdate);
				$res = $sth->execute( array(':mtId' => $mtId, ':pswd' => $newPswd, ':salt' => $salt) );

				if($res>0)
				{
					$output = array('status' => "ok", 'message' => "[DB] Neues Passwort wurde erfolgreich hinzugefügt.\n");
				}
				else
				{
					$output = array('status' => "error",'message' => "[DB] Neues Passwort konnte nicht hinzugefügt werden.");
				}
			}
		}
		catch (Exception $ex) {
			$output = array('status' => "error",'message' => $ex);
		}

		header("Content-type: application/json");
		exit(json_encode($output));
	}//public function setNewPassword
}
