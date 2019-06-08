<?php
namespace Kunde;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
require_once BASIS_DIR.'/MVC/DBFactory.php';
use MVC\DBFactory as DBFactory;
use PDO as PDO;

class CommentsToolsAjax {
	public function newComment()
	{
		$fehler = "";
		$dataPost = array();
		$output = array();
	//kndId
		if(empty($_POST['kndId']))
		{
			$fehler .= "kndId fehlt.\n";
		}
		else{
			if(Fltr::isInt($_POST['kndId']))
			{
				$dataPost[':kndId'] = $_POST['kndId'];
			}
			else{
				$fehler .= "kndId ist kein Integer.\n";
			}
		}
		
	//get Current Admin Id
		require_once BASIS_DIR.'/Tools/User.php';
		$curAdminId = \Tools\User::getCurrentUserId();
		if($curAdminId)
		{
			$dataPost[':mtId'] = $curAdminId;
		}
		else{
			$fehler .= "mtId konnte nicht bestimmt werden.\n";
		}
		
	//text
		if(empty($_POST['comment']))
		{
			$fehler .= "Kommentar fehlt.\n";
		}
		else{
			$dataPost[':comment'] = Fltr::filterStr($_POST['comment']);
		}
		
		if(empty($fehler))
		{
			$dbh = DBFactory::getDBH();
			
			if(!$dbh)
			{
				$output = array('status' => "error", 'message' => "Keine Verbindung mit DB.");
				header("Content-type: application/json");
				exit(json_encode($output));
			}
			
			$tbl = "";
			$vl = "";
			
			foreach ($dataPost as $key=>$val)
			{
				$tbl .= substr($key, 1).",";
				$vl .= $key.",";
			}
			$tbl .= "created";
			$vl .= "NOW()";
			//$tbl = substr($tbl, 0, -1);
			//$vl = substr($vl, 0, -1);
			
			$q = "INSERT INTO kndComments (".$tbl.") VALUES(".$vl.")";
			
			try
			{
				$sth = $dbh->prepare($q);
				$res = $sth->execute($dataPost);

				if($res>0)
				{
					$output = array('status' => "ok", 'message' => "[DB] Neuer Kommentar wurde erfolgreich hinzugefügt.");
				}
				else
				{
					$output = array('status' => "error", 'message' => "[DB] Neuer Kommentar konnte nicht hinzugefügt werden.");
				}
			}
			catch (Exception $ex) {
				$output = array('status' => "error", 'message' => $ex);
			}
		}
		else{
			$output = array('status' => "error", 'message' => $fehler);
		}
		
		header("Content-type: application/json");
		exit(json_encode($output));
	}//end public function newComment()
	
	public function deleteComment()
	{
		$fehler = "";
		$output = array();
		$cmntId;
		$mtId;
	//cmntId
		if(empty($_POST['cmntId']))
		{
			$fehler .= "Kommentar-Id fehlt.\n";
		}
		else{
			if(Fltr::isInt($_POST['cmntId']))
			{
				$cmntId = $_POST['cmntId'];
			}
			else{
				$fehler .= "cmntId ist kein Integer.\n";
			}
		}
		
	//get Current Admin Id
		require_once BASIS_DIR.'/Tools/User.php';
		$curAdminId = \Tools\User::getCurrentUserId();
		if($curAdminId)
		{
			$mtId = $curAdminId;
		}
		else{
			$fehler .= "mtId konnte nicht bestimmt werden.\n";
		}
		
		if(empty($fehler))
		{
			$dbh = DBFactory::getDBH();
			
			if(!$dbh)
			{
				$output = array('status' => "error", 'message' => "Keine Verbindung mit DB.");
				header("Content-type: application/json");
				exit(json_encode($output));
			}
			
			$q = "SELECT cmntId, kndId, mtId FROM kndComments WHERE cmntId=:cmntId";
			$qD = "DELETE FROM kndComments WHERE cmntId=:cmntId";
			
			try
			{
				$sth = $dbh->prepare($q);
				$sth->execute(array(':cmntId' => $cmntId));
				$res = $sth->fetch(PDO::FETCH_ASSOC, 1);
				
				if(isset($res['cmntId']))
				{
					if($res['mtId'] === $mtId)
					{
						$sth = $dbh->prepare($qD);
						$res = $sth->execute(array(':cmntId' => $cmntId));

						if($res>0)
						{
							$output = array('status' => "ok", 'message' => "[DB] Der Kommentar wurde erfolgreich gelöscht.");
						}
						else
						{
							$output = array('status' => "error", 'message' => "[DB] Der Kommentar konnte nicht gelöscht werden.");
						}
					}
					else {
						$output = array('status' => "error", 'message' => "[DB] Sie haben den Kommentar nicht geschriben, also durfen Sie den nicht löschen.");
					}
				}
				else{
					$output = array('status' => "error", 'message' => "[DB] Der Kommentar existiert nicht.");
				}			
			}
			catch (Exception $ex) {
				$output = array('status' => "error", 'message' => "try exception");
			}
		}
		else{
			$output = array('status' => "error",'message' => $fehler);
		}
		
		header("Content-type: application/json");
		exit(json_encode($output));
	}//end public function deleteComment()
	
	public function updateComment()
	{
		$fehler = "";
		$output = array();
		$cmntId;
		$mtId;
		$comment = "";
	//cmntId
		if(empty($_POST['cmntId']))
		{
			$fehler .= "Kommentar-Id fehlt.\n";
		}
		else{
			if(Fltr::isInt($_POST['cmntId']))
			{
				$cmntId = $_POST['cmntId'];
			}
			else{
				$fehler .= "cmntId ist kein Integer.\n";
			}
		}
	//comment
		if(empty($_POST['comment']))
		{
			$fehler .= "Kommentar fehlt\n";
		}
		else{
			$comment = Fltr::filterStr($_POST['comment']);
		}
		
	//get Current Admin Id
		require_once BASIS_DIR.'/Tools/User.php';
		$curAdminId = \Tools\User::getCurrentUserId();
		if($curAdminId)
		{
			$mtId = $curAdminId;
		}
		else{
			$fehler .= "mtId konnte nicht bestimmt werden.\n";
		}
		
		if(empty($fehler))
		{
			$dbh = DBFactory::getDBH();
			
			if(!$dbh)
			{
				$output = array('status' => "error", 'message' => "Keine Verbindung mit DB.");
				header("Content-type: application/json");
				exit(json_encode($output));
			}
			
			$q = "SELECT cmntId, kndId, mtId FROM kndComments WHERE cmntId=:cmntId";
			$qU = "UPDATE kndComments SET comment=:comment, updated=NOW() WHERE cmntId=:cmntId";
			
			try
			{
				$sth = $dbh->prepare($q);
				$sth->execute(array(':cmntId' => $cmntId));
				$res = $sth->fetch(PDO::FETCH_ASSOC, 1);
				
				if(isset($res['cmntId']))
				{
					if($res['mtId'] === $mtId)
					{
						$sth = $dbh->prepare($qU);
						$res = $sth->execute(array(':cmntId' => $cmntId, ':comment' => $comment));

						if($res>0)
						{
							$output = array('status' => "ok", 'message' => "[DB] Der Kommentar wurde erfolgreich geändert.");
						}
						else
						{
							$output = array('status' => "error", 'message' => "[DB] Der Kommentar konnte nicht geändert werden.");
						}
					}
					else {
						$output = array('status' => "error", 'message' => "[DB] Sie haben den Kommentar nicht geschriben, also durfen Sie den nicht ändern.");
					}
				}
				else{
					$output = array('status' => "error", 'message' => "[DB] Der Kommentar existiert nicht.");
				}			
			}
			catch (Exception $ex) {
				$output = array('status' => "error", 'message' => $ex);
			}
		}
		else{
			$output = array('status' => "error",'message' => $fehler);
		}
		
		header("Content-type: application/json");
		exit(json_encode($output));
	}//end public function updateComment()
}
