<?php
namespace Login;
use PDO as PDO;

use MVC\DBFactory as DBFactory;

require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;

class LoginTools
{
	public function ajaxSetPassword()
	{
		$newPswd = "";
		$pswd = "";
		$login = "";
		$mtId = "";
	//login
		if( !isset($_POST['login']) OR empty($_POST['login']) ){
			$output = array('status' => "error", 'message' => "[POST] Login fehlt.");
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		elseif(Fltr::isWordsAndNumbers($_POST['login'])){
			$login = $_POST['login'];
		}
		else{
			$output = array('status' => "error", 'message' => "[Fltr] Login darf nut die Buchstaben und Ziffern beinhalten.");
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
		if( !isset($_POST['newPswd']) OR empty($_POST['newPswd']) )
		{
			$output = array('status' => "error", 'message' => "[POST] Neues Password fehlt.");
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		elseif(Fltr::isWordsAndNumbers($_POST['newPswd'])){
			$pswd = $_POST['newPswd'];
		}
		else{
			$output = array('status' => "error", 'message' => "[Fltr] Neues Password darf nut die Buchstaben und Ziffern beinhalten.");
			header("Content-type: application/json");
			exit(json_encode($output));
		}
//generate password
		$salt = substr(md5(time()), 0, 10);
		$newPswd = hash('sha512', $salt.$pswd);

		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			$output = array('status' => "error", 'message' => "[DB] Verbindung mit DB fehlgeschlagen.");
			header("Content-type: application/json");
			exit(json_encode($output));
		}

		$q = "INSERT INTO mtblogin(mtId, login, pswd, salt, istAktiv) VALUES (:mtId, :login, :pswd, :salt, :istAktiv)";
		$tq ="SELECT count(mtId) as 'count' FROM mtblogin WHERE login=:login";
		try
		{
			$sthTest = $dbh->prepare($tq);
			$sthTest->execute(array(':login' => $login));
			$resTest = $sthTest->fetch(PDO::FETCH_ASSOC, 1);

			if($resTest['count'] > 0)
			{
				$output = array('status' => "error", 'message' => "Login ist schon besetzt.");
			}
			else
			{
				$sth = $dbh->prepare($q);
				$res = $sth->execute( array(':mtId' => $mtId, ':login' => $login,
											':pswd' => $newPswd, ':salt' => $salt, ':istAktiv' => "1") );

				if($res>0)
				{
					$output = array('status' => "ok", 'message' => "[DB] Neues Login&Password wurden erfolgreich hinzugefügt.");
				}
				else
				{
					$output = array('status' => "error",'message' => "[DB] Neues Login&Password konnten nicht hinzugefügt werden.", 'data' => $dataPost);
				}
			}
		}
		catch (Exception $ex) {
			$output = array('status' => "error",'message' => $ex);
		}

		header("Content-type: application/json");
		exit(json_encode($output));
	}

	public function ajaxUpdateLogin()
	{
		$login = "";
		$mtId = "";

		//login
		if( !isset($_POST['login']) OR empty($_POST['login']) ){
			$output = array('status' => "error", 'message' => "[POST] Login fehlt.");
			header("Content-type: application/json");
			exit(json_encode($output));
		}
		elseif(Fltr::isWordsAndNumbers($_POST['login'])){
			$pswd = $_POST['login'];
		}
		else{
			$output = array('status' => "error", 'message' => "[Fltr] Login darf nut die Buchstaben und Ziffern beinhalten.");
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

		$tq ="SELECT count(mtId) as 'count' FROM mtblogin WHERE login=:login";
		$q = "";
	}
}
