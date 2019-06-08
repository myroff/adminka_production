<?php
namespace Stundenplan;
use PDO as PDO;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;

class UpdateTermin {
	public function ajaxUpdate()
	{
		$fehler = "";
		$dataPost = array();
		$testData = array();
		$output = array();
		
		if(isset($_POST['stnPlId']) AND !empty($_POST['stnPlId']))
		{
			$stnPlId = $_POST['stnPlId'];
			$testData[':stnPlId'] = $_POST['stnPlId'];
		}
		else{
			$fehler .= "Stundenplan-ID fehlt.\n";
		}
		
		if(isset($_POST['raum']))
		{
			$dataPost[':raum'] = Fltr::filterStr($_POST['raum']);
			$testData[':raum'] = $_POST['raum'];
		}
		else{
			$fehler .= "Raum fehlt.\n";
		}
		
		if(isset($_POST['wochentag']))
		{
			if(Fltr::isInt($_POST['wochentag'])){
				$dataPost[':wochentag'] = Fltr::filterStr($_POST['wochentag']);
				$testData[':wochentag'] = $_POST['wochentag'];
			}
			else{
				$fehler .= "Wochentag ist kein Ganzzahl.\n";
			}
		}
		else{
			$fehler .= "Tag der Woche fehlt.\n";
		}
		
		if(!empty($_POST['anfang']))
		{
			$_POST['anfang'] = str_replace("/\s/", "", $_POST['anfang']);
			if(Fltr::isStrTime($_POST['anfang']))
			{
				$dataPost[':anfang'] = $_POST['anfang'].":00";
				$testData[':anfang'] = $_POST['anfang'].":00";
			}
			else
			{
				$fehler .= "Anfangszeit ist falsch eingegeben. Bspl.: 15:45.\n";
			}
		}
		else 
		{
			$fehler .= "Anfangszeit fehlt.\n";
		}
		
		if(!empty($_POST['ende']))
		{
			$_POST['ende'] = str_replace("/\s/", "", $_POST['ende']);
			if(Fltr::isStrTime($_POST['ende']))
			{
				$dataPost[':ende'] = $_POST['ende'].":00";
				$testData[':ende'] = $_POST['ende'].":00";
			}
			else
			{
				$fehler .= "Endzeit ist falsch eingegeben. Bspl.: 15:45.\n";
			}
		}
		else 
		{
			$fehler .= "Anfangszeit fehlt.\n";
		}
		
		if(empty($fehler))
		{	
			require_once BASIS_DIR.'/MVC/DBFactory.php';
			$dbh = \MVC\DBFactory::getDBH();
			
			if(!$dbh)
			{
				$output = array('info' => "no connection to db (dbh).");
				header("Content-type: application/json");
				exit(json_encode($output));
			}

			$set = "";
			
			foreach ($dataPost as $key=>$val)
			{
				$set .= substr($key, 1)."=".$key.", ";
			}
			$set = substr($set, 0, -2);
			
			$q = "UPDATE stundenplan SET ".$set." WHERE stnPlId=".$stnPlId;
			$tq = "SELECT st.*, k.kurName FROM stundenplan as st LEFT JOIN kurse as k USING(kurId)"
					. " WHERE stnPlId<>:stnPlId AND raum=:raum AND wochentag=:wochentag"
					. " AND ( (:anfang > anfang AND :anfang < ende) OR (:ende > anfang AND :ende < ende) )";
			
			try
			{
				$sthTest = $dbh->prepare($tq);
				$sthTest->execute($testData);
				$resTest = $sthTest->fetchAll(PDO::FETCH_ASSOC);
				
				if(count($resTest) > 0)
				{
					$stn = "";
					foreach ($resTest as $r)
					{
						$stn .= "Kurs:".$r['kurName']."; Raum:".$r['raum']."; von ".$r['anfang']." bis ".$r['ende'];
					}
					$output = array('status' => "Die Zeit oder der Raum ist schon besetz.\nq = $tq\n$stn");
				}
				else
				{
					$sth = $dbh->prepare($q);
					$res = $sth->execute($dataPost);

					if($res>0)
					{
						$output = array('status' => "ok");
					}
					else
					{
						$output = array('status' => "[DB] Der Termin konnte nicht geändert werden.");
					}
				}
			}
			catch (Exception $ex) {
				$output = array('status' => $ex);
			}
		}
		else
		{
			$output = array('status' => $fehler);
		}

		header("Content-type: application/json");
		exit(json_encode($output));
	}
	
	public function ajaxDelete()
	{
		$output = array();
		if( isset($_POST['stnPlId']) AND !empty($_POST['stnPlId']) )
		{
			if(Fltr::isInt($_POST['stnPlId']))
			{
				$q = "DELETE FROM stundenplan WHERE stnPlId=".$_POST['stnPlId'];
				
				require_once BASIS_DIR.'/MVC/DBFactory.php';
				$dbh = \MVC\DBFactory::getDBH();

				if(!$dbh)
				{
					$output = array('status' => "no connection to db (dbh).");
					header("Content-type: application/json");
					exit(json_encode($output));
				}
				
				try
				{
					$res = $dbh->exec($q);
					if($res > 0)
					{
						$output['status'] = 'ok';
					}
					else
					{
						$output['status'] = 'Der Termin konnte nicht gelöscht werden. Evtl. wurde er schon von anderem Admin entfernt worden.';
					}
				} catch (Exception $ex) {
					$output = array('status' => $ex);
				}
			}
			else{
				$output['status'] = 'Stundenplan-ID hat unpassendes Format.';
			}
		}
		else {
			$output['status'] = 'Stundenplan-ID fehlt.';
		}
		header("Content-type: application/json");
		exit(json_encode($output));
	}
}
