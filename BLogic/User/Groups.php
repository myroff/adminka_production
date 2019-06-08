<?php
namespace User;
use PDO as PDO;
require_once BASIS_DIR.'/MVC/DBFactory.php';
use MVC\DBFactory as DBFactory;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
require_once BASIS_DIR.'/Tools/User.php';
use Tools\User as User;

class Groups {
	public function renameGroups(){
		$grpId;
		$grpName;
		$fehler;
//grpId
		if (isset($_POST['grpId']) AND !empty($_POST['grpId'])) {
			$grpId = intval($_POST['grpId']);
		}
		else{
			$fehler = "gruppen Id fehlt.\n";
		}
//grpName
		if (isset($_POST['grpName']) AND !empty($_POST['grpName'])) {
			$grpName = Fltr::filterStr($_POST['grpName']);
		}
		else{
			$fehler .= "Gruppenname fehlt.\n";
		}
		
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			$fehler .= "DbFactory Fehler.\n";
		}
//if fehler
		if(!empty($fehler)){
			$output['status'] = "error";
			$output['message'] = $fehler;
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$q = "UPDATE groups SET grpName=:grpName WHERE grpId=:grpId";
		
		try
		{
			$sth = $dbh->prepare($q);
			$res = $sth->execute(array(":grpName" => $grpName, ":grpId"=>$grpId));
		} catch (Exception $ex) {
			$output['status'] = "error";
			$output['message'] = $ex;
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$output['status'] = "ok";
		$output['message'] = "Die Gruppe wurde erfolgreich umbenannt.";

		header("Content-type: application/json");
		exit(json_encode($output));
	}//public function renameGroups()
	
	public function createGroups(){
		$newGrp;
		$fehler="";
//newGrp
		if (isset($_POST['newGrp']) AND !empty($_POST['newGrp'])) {
			$newGrp = Fltr::filterStr($_POST['newGrp']);
		}
		else{
			$fehler .= "Gruppenname fehlt.\n";
		}
		
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			$fehler .= "DbFactory Fehler.\n";
		}
//if fehler
		if(!empty($fehler)){
			$output['status'] = "error";
			$output['message'] = $fehler;
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$lastId = "SELECT MAX(grpId) as id FROM groups";
		$qInsert = "INSERT INTO groups (grpId, grpName) VALUES(:grpId,:grpName)";
		
		try
		{
	//get last id
			$sth = $dbh->prepare($lastId);
			$sth->execute();
			$res = $sth->fetch(PDO::FETCH_ASSOC,1);
			$lastId = intval($res['id']) + 1;
	//insert
			$sth = $dbh->prepare($qInsert);
			$res = $sth->execute(array("grpId"=>$lastId,":grpName" => $newGrp));
		} catch (Exception $ex) {
			$output['status'] = "error";
			$output['message'] = $ex;
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$output['status'] = "ok";
		$output['message'] = "Eine neue Gruppe ist erfolgreich erschaffen.";

		header("Content-type: application/json");
		exit(json_encode($output));
	}//public function createGroups()
	
	public function deleteGroups(){
		$grpId;
		$fehler="";
//newGrp
		if (isset($_POST['grpId']) AND !empty($_POST['grpId'])) {
			$grpId = intval($_POST['grpId']);
		}
		else{
			$fehler .= "grpId fehlt.\n";
		}
		if($grpId === 1){
			$fehler .= "Diese Gruppe darf nicht entfernt werden!.\n";
		}
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			$fehler .= "DbFactory Fehler.\n";
		}
		
		$userGroups = User::getUserGroup();
		
		if(!in_array("SuperAdmin", $userGroups)){
			$fehler .= "Sie haben keine Rechte für diese Operation.\n";
		}
		
//if fehler
		if(!empty($fehler)){
			$output['status'] = "error";
			$output['message'] = $fehler;
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$qDelete = "DELETE FROM groups WHERE grpId = :grpId";
		
		try
		{
	//delete
			$sth = $dbh->prepare($qDelete);
			$res = $sth->execute(array(":grpId"=>$grpId));
		} catch (Exception $ex) {
			$output['status'] = "error";
			$output['message'] = $ex;
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$output['status'] = "ok";
		$output['message'] = "Eine Gruppe ist erfolgreich entfernt.";

		header("Content-type: application/json");
		exit(json_encode($output));
	}//public function createGroups()
	
	public function getGroupListJson(){
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			$output['status'] = "error";
			$output['message'] = "keine Verbindung zur DB.";
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$q = "SELECT * FROM groups ORDER BY grpName";
		$groups = [];
		
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute();
			$groups = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		} catch (Exception $ex) {
			$output['status'] = "error";
			$output['message'] = $ex;
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$output['status'] = "ok";
		$output['message'] = $groups;

		header("Content-type: application/json");
		exit(json_encode($output));
	}//public function getGroupListJson()
	
	public function addGroupToMitarbeiter() {
		$mtId; $grpId; $fehler;
		
		if (isset($_POST['grpId']) AND !empty($_POST['grpId'])) {
			$grpId = intval($_POST['grpId']);
		}
		else{
			$fehler .= "grpId fehlt.\n";
		}
		
		if (isset($_POST['mtId']) AND !empty($_POST['mtId'])) {
			$mtId = intval($_POST['mtId']);
		}
		else{
			$fehler .= "mtId fehlt.\n";
		}
		$userGroups = User::getUserGroup();
		
		if(!in_array("SuperAdmin", $userGroups)){
			$fehler .= "Sie haben keine Rechte für diese Operation.\n";
		}
		
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			$fehler .= "DbFactory Fehler.\n";
		}
//if fehler
		if(!empty($fehler)){
			$output['status'] = "error";
			$output['message'] = $fehler;
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$q = "INSERT INTO mtbingrp (mtId, grpId) VALUES (:mtId, :grpId)"
				." ON DUPLICATE KEY UPDATE mtId=:mtId";
		
		try
		{
	//insert
			$sth = $dbh->prepare($q);
			$res = $sth->execute(array(":grpId"=>$grpId, ":mtId"=>$mtId));
		} catch (Exception $ex) {
			$output['status'] = "error";
			$output['message'] = $ex;
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$output['status'] = "ok";
		$output['message'] = "Ein User ist zu einer Gruppe erfolgreich hinzugefügt.";

		header("Content-type: application/json");
		exit(json_encode($output));
	}//public function addGroupToMitarbeiter()
	
	public function getUsersGroup() {
		$mtId; $fehler;
		
		if (isset($_POST['mtId']) AND !empty($_POST['mtId'])) {
			$mtId = intval($_POST['mtId']);
		}
		else{
			$fehler .= "mtId fehlt.\n";
		}
		
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			$fehler .= "DbFactory Fehler.\n";
		}
//if fehler
		if(!empty($fehler)){
			$output['status'] = "error";
			$output['message'] = $fehler;
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$q = "SELECT grpId, grpName FROM groups RIGHT JOIN mtbingrp USING (grpId) WHERE mtId=:mtId";
		$groups = [];
		
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute(array(":mtId"=>$mtId));
			$groups = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		} catch (Exception $ex) {
			$output['status'] = "error";
			$output['message'] = $ex;
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$output['status'] = "ok";
		$output['message'] = $groups;

		header("Content-type: application/json");
		exit(json_encode($output));
	}//public function getUsersGroup()
	
	public function removeUserFromGroup() {
		$mtId; $gtpId; $fehler;
		
		if (isset($_POST['grpId']) AND !empty($_POST['grpId'])) {
			$grpId = intval($_POST['grpId']);
		}
		else{
			$fehler .= "grpId fehlt.\n";
		}
		
		if (isset($_POST['mtId']) AND !empty($_POST['mtId'])) {
			$mtId = intval($_POST['mtId']);
		}
		else{
			$fehler .= "mtId fehlt.\n";
		}
		$userGroups = User::getUserGroup();
		
		if(!in_array("SuperAdmin", $userGroups)){
			$fehler .= "Sie haben keine Rechte für diese Operation.\n";
		}
		
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			$fehler .= "DbFactory Fehler.\n";
		}
//if fehler
		if(!empty($fehler)){
			$output['status'] = "error";
			$output['message'] = $fehler;
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$q = "DELETE FROM mtbingrp WHERE mtId=:mtId AND grpId=:grpId";
		
		try
		{
	//insert
			$sth = $dbh->prepare($q);
			$res = $sth->execute(array(":grpId"=>$grpId, ":mtId"=>$mtId));
		} catch (Exception $ex) {
			$output['status'] = "error";
			$output['message'] = $ex;
			
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$output['status'] = "ok";
		$output['message'] = "Ein User ist von einer Gruppe erfolgreich entfernt.";

		header("Content-type: application/json");
		exit(json_encode($output));
	}
}
