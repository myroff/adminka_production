<?php
namespace Kurse;
use PDO as PDO;
require_once BASIS_DIR.'/MVC/DBFactory.php';
use MVC\DBFactory as DBFactory;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;

class ChangeKurs 
{
	public function getInfoJson()
	{
		$postData = array();
		$fehler   = array();
//eintrId
		if( empty($_POST['eintrId']) ) {
			$fehler[] = "eintrId fehlt!";
		}
		else {
			$postData['eintrId'] = $_POST['eintrId'];
		}
//altes KurId
		if( empty($_POST['oldKurId']) ) {
			$fehler[] = "oldKurId fehlt!";
		}
		else {
			$postData['oldKurId'] = $_POST['oldKurId'];
		}
//altes SeasonId
		if(empty($_POST['oldSeasonId'])) {
			$fehler[] = "oldSeasonId fehlt!";
		}
		else {
			$postData['oldSeasonId'] = $_POST['oldSeasonId'];
		}
//neues KurId
		if( empty($_POST['newKurId']) ) {
			$fehler[] = "newKurId fehlt!";
		}
		else {
			$postData['newKurId'] = $_POST['newKurId'];
		}
//neues SeasonId
		if(empty($_POST['newSeasonId'])) {
			$fehler[] = "newSeasonId fehlt!";
		}
		else {
			$postData['newSeasonId'] = $_POST['newSeasonId'];
		}
		
		if(!empty($fehler))
		{
			$output = array('status' => 'error', 'message' => implode(' ', $fehler));
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			$output = array('status' => 'error', 'message' => "no connection to db (dbh).");
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$qOldKur = "SELECT kur.kurId, kur.kurName, st.raum, l.name, l.vorname"
			.", group_concat('{\"wochentag\":\"',wochentag,'\",\"time\":\"', TIME_FORMAT(anfang, '%H:%i'),' - ', TIME_FORMAT(ende, '%H:%i'),'\"}' SEPARATOR',') as 'termine'"
			.", season.season_name"
			." FROM kundehatkurse as khk LEFT JOIN kurse as kur USING(kurId) LEFT JOIN stundenplan as st USING(kurId) LEFT JOIN lehrer as l USING(lehrId)"
			." LEFT JOIN seasons as season ON khk.season_id=season.season_id"
			." WHERE khk.eintrId=:eintrId AND khk.season_id=:seasonId"
			." GROUP BY kur.KurId";
		
		$qNewKur = "SELECT kur.kurId, kur.kurName, st.raum, l.name, l.vorname"
			.", group_concat('{\"wochentag\":\"',wochentag,'\",\"time\":\"', TIME_FORMAT(anfang, '%H:%i'),' - ', TIME_FORMAT(ende, '%H:%i'),'\"}' SEPARATOR',') as 'termine'"
			.", season.season_name"
			." FROM kurse as kur LEFT JOIN stundenplan as st USING(kurId) LEFT JOIN lehrer as l USING(lehrId) LEFT JOIN seasons as season ON st.season_id=season.season_id"
			." WHERE kur.kurId=:kurId AND st.season_id=:seasonId"
			." GROUP BY kur.KurId";
		$oldKur = array();
		$newKur = array();
		$rs = array();
		try
		{
			$sth = $dbh->prepare($qOldKur);
			$sth->execute(array(':eintrId' => $postData['eintrId'], ':seasonId' => $postData['oldSeasonId']));
			$oldKur = $sth->fetch(PDO::FETCH_ASSOC,1);
			
			$sth = $dbh->prepare($qNewKur);
			$sth->execute(array(':kurId' => $postData['newKurId'], ':seasonId' => $postData['newSeasonId']));
			$newKur = $sth->fetch(PDO::FETCH_ASSOC,1);
			
		} catch (Exception $ex) {
			//print $ex;
			$output = array('status' => 'error', 'message' => $ex);
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		$out = array(
			"oldKur" => $oldKur['season_name']."<br><b>".$oldKur['kurName']."</b><br><i>".$oldKur['vorname']." ".$oldKur['name']."</i><br>Raum ". $oldKur['raum'].Fltr::printSqlTermin($oldKur['termine']),
			"newKur" => $newKur['season_name']."<br><b>".$newKur['kurName']."</b><br><i>".$newKur['vorname']." ".$newKur['name']."</i><br>Raum ". $newKur['raum'].Fltr::printSqlTermin($newKur['termine'])
		);
		
		$output = array('status' => 'ok', 'message' => $out );
		header("Content-type: application/json");
		exit(json_encode($output));
	}//public function getInfoJson()
	
	public function changeKursJson()
	{
		$postData = array();
		$fehler   = array();
//kndId
		if( empty($_POST['kndId']) OR !isset($_POST['kndId']) ) {
			$fehler[] = "kndId fehlt!";
		}
		else {
			$postData[':kndId'] = $_POST['kndId'];
		}
//altes KurId
		if( empty($_POST['eintrId']) OR !isset($_POST['eintrId']) ) {
			$fehler[] = "eintrId fehlt!";
		}
		else {
			$postData[':eintrId'] = $_POST['eintrId'];
		}
//altes KurId
		if( empty($_POST['oldKurId']) OR !isset($_POST['oldKurId']) ) {
			$fehler[] = "oldKurId fehlt!";
		}
		else {
			$postData[':oldKurId'] = $_POST['oldKurId'];
		}
//neues KurId
		if( empty($_POST['newKurId']) OR !isset($_POST['newKurId']) ) {
			$fehler[] = "oldKurId fehlt!";
		}
		else {
			$postData[':newKurId'] = $_POST['newKurId'];
		}
//altes SeasonId
		if(empty($_POST['oldSeasonId'])) {
			$fehler[] = "oldSeasonId fehlt!";
		}
		else {
			$postData['oldSeasonId'] = $_POST['oldSeasonId'];
		}
//neues SeasonId
		if(empty($_POST['newSeasonId'])) {
			$fehler[] = "newSeasonId fehlt!";
		}
		else {
			$postData['newSeasonId'] = $_POST['newSeasonId'];
		}
		
		if(!empty($fehler))
		{
			$output = array('status' => 'error', 'message' => implode(' ', $fehler));
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			$output = array('status' => 'error', 'message' => "no connection to db (dbh).");
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		
		$q_timeLimit = "SELECT EXTRACT(YEAR_MONTH from von) as 'von', EXTRACT(YEAR_MONTH from bis) as 'bis' FROM kundehatkurse WHERE eintrId=:eintrId";
		
		$q_khk = "UPDATE kundehatkurse SET kurId=:newKurId, season_id=:seasonId  WHERE eintrId=:eintrId ";
		
		$q_rechnDat = "UPDATE rechnungsdaten as rd JOIN rechnungen as r USING(rnId)"
				. " SET rd.kurId=:newKurId, rd.season_id=:seasonId"
				. " WHERE r.kndId=:kndId AND rd.kurId=:oldKurId AND (EXTRACT(YEAR_MONTH from rnMonat) BETWEEN :von AND :bis)";
		
		$dbh->beginTransaction();

		try
		{
			$sth = $dbh->prepare($q_timeLimit);
			$sth->execute(array(':eintrId'=>$postData[':eintrId']));
			$res = $sth->fetch(PDO::FETCH_ASSOC, 1);
			
			$sth = $dbh->prepare($q_khk);
			$sth->execute(array(':eintrId'=>$postData[':eintrId'], ':newKurId'=>$postData[':newKurId'], ':seasonId'=>$postData['newSeasonId']));
			
			$sth = $dbh->prepare($q_rechnDat);
			$sth->execute(array(':newKurId'=>$postData[':newKurId'], ':seasonId'=>$postData['newSeasonId'], ':kndId'=>$postData[':kndId'], ':oldKurId'=>$postData[':oldKurId'], ':von'=>$res['von'], ':bis'=>$res['bis']));
			
		} catch(Exception $ex){
			$dbh->rollBack();
			
			$output = array('status' => 'error', 'message' => $ex);
			header("Content-type: application/json");
			exit(json_encode($output));
		}

		$dbh->commit();
		
		$output = array('status' => 'ok', 'message' => "Höchstwahrscheinlich der Kurs wurde erfolgreich umgetauscht.\nBitte Nachprüfen!" );
		header("Content-type: application/json");
		exit(json_encode($output));
	}
}
