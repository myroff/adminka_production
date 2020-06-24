<?php
namespace Stundenplan;

use PDO as PDO;
require_once BASIS_DIR.'/MVC/DBFactory.php';
use MVC\DBFactory as DBFactory;

class Website
{
	/* create dump and upload it to website
	 * 
	 */
	public function uploadStundenplan()
	{
		$csvPathKurse = __DIR__.'/kurse.csv';
		$csvPathStund = __DIR__.'/stundenplan.csv';
		
		$isKurseOK = $this->exportKurse($csvPathKurse);
		$isStundOK = $this->exportStundenplan($csvPathStund);
		
		$uploadResponse = $this->uploadFile(array($csvPathKurse, $csvPathStund));
		
		exit($uploadResponse);
	}
	/* export table 'kurse' to csv file.
	 * @param string $csvPath	- absolute path for export file
	 */
	public function exportKurse($csvPath)
	{
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			exit("locale database is not available");
		}
		//stundenplan , kurse
		$qKurse = "SELECT kurId, kurName, kurBeschreibung, kurMinAlter, kurMaxAlter, kurMinKlasse, kurMaxKlasse"
				. " FROM kurse"
				. " WHERE isKurInactive = '0' OR isKurInactive IS NULL";
		
		try
		{
			$sth = $dbh->prepare($qKurse);
			$sth->execute();
			$rsKurse = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			return $this->saveDataIntoCsvFile($rsKurse, $csvPath);
			
		} catch (Exception $ex) {
			//print $ex;
		}
		
		return FALSE;
	}
	
	public function exportStundenplan($csvPath)
	{
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			exit("locale database is not available");
		}
		//stundenplan , kurse
		$qStund = "SELECT stn.stnPlId, stn.kurId, stn.wochentag, stn.anfang, stn.ende, stn.raum"
				. " FROM stundenplan as stn"
				. " JOIN kurse as kur USING(kurId)"
				. " WHERE kur.isKurInactive = '0' OR kur.isKurInactive IS NULL";
		
		try
		{
			$sth = $dbh->prepare($qStund);
			$sth->execute();
			$rsKurse = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			return $this->saveDataIntoCsvFile($rsKurse, $csvPath);
			
		} catch (Exception $ex) {
			//print $ex;
		}
		
		return FALSE;
	}

	/* 
	 * @param string $csvPath	- absolute path for export file
	 */
	public function saveDataIntoCsvFile($data, $csvPath)
	{
		if(file_exists($csvPath))
		{
			unlink($csvPath);
		}
		
		$fp = fopen($csvPath, 'w+');
		
		$headers = array_keys($data[0]);
		
		if(empty($headers))
		{
			return FALSE;
		}
		
		fputcsv($fp, $headers, ";");
		
		foreach($data as $d)
		{
			fputcsv($fp, $d, ";", '"');
		}
		
		fclose($fp);
		
		return TRUE;
	}
	/* upload files with CURL
	 * @param array $files - array with absolute pathes to files u want upload
	 */
	public function uploadFile($files)
	{
		//https://www.swiff-online.localhost/administrator/index.php?option=com_swiffstundenplan&task=stundenplan.update
		$url = "https://www.swiff-online.de/administrator/components"
		#		."?option=com_swiffstundenplan&task=stundenplan.uploadFiles";
		#$url = "https://www.swiff-online.localhost/administrator/components"
				. "/com_swiffstundenplan/controllers/import_files.php";
		
		$post = array('pswd'=>"as4Dkj13Jm865rZk");
		
		foreach($files as $index => $f)
		{
			$file = curl_file_create($f);
			$post['import_files['.$index.']'] = $file;
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 180);
		$response = curl_exec($ch);
		#$info = curl_getinfo($ch);
		curl_close($ch);
		
		return $response;
	}
}
