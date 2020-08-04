<?php
namespace Kurse;
use PDO as PDO;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
//twig
require_once BASIS_DIR . '/Vendor/autoload.php';
require_once BASIS_DIR.'/MVC/DBFactory.php';

class Seasons 
{
	public function showEditList()
	{
		$res = $this->searchDates();
		
		$options = []; #array('cache' => TWIG_CACHE_DIR);
		$loader = new \Twig_Loader_Filesystem(TWIG_TEMPLATE_DIR);
		$twig = new \Twig_Environment($loader, $options);
		$twigTmpl = $twig->load('/Kurse/SeasonsBearbeitenListe.twig');
		echo $twigTmpl->render(['seasons' =>$res]);
	}
	
	public function searchDates()
	{
		
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}
		
		$q = "SELECT * FROM seasons ORDER BY date_end DESC, date_start DESC";
		
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute();
			$rs = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			return $rs;
			
		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
	}
	
	public function addNewSeason()
	{
		$errors   = [];
		$response = [];
		
		$editSeasonId		= filter_input(INPUT_POST, 'edit_season_id', FILTER_SANITIZE_NUMBER_INT);
		$newSeasonName		= filter_input(INPUT_POST, 'new_season_name', FILTER_SANITIZE_STRING);
		$newSeasonDateStart	= filter_input(INPUT_POST, 'new_seasons_date_start', FILTER_SANITIZE_STRING);
		$newSeasonDateEnd	= filter_input(INPUT_POST, 'new_seasons_date_end', FILTER_SANITIZE_STRING);
		
		if(!$newSeasonName)								{$errors[] = "Der Name für neues Season fehlt.";}
		
		if(!$newSeasonDateStart)						{$errors[] = "Anfangsdatum für neues Season fehlt.";}
		elseif(Fltr::isDate($newSeasonDateStart))		{$newSeasonDateStart = Fltr::strToSqlDate($newSeasonDateStart);}
		elseif(!Fltr::isSqlDate($newSeasonDateStart))	{$errors[] = "Anfangsdatum ist kein Datum.";}
		
		if(!$newSeasonDateEnd)							{$errors[] = "Enddatum für neues Season fehlt.";}
		elseif(Fltr::isDate($newSeasonDateEnd))			{$newSeasonDateEnd = Fltr::strToSqlDate($newSeasonDateStart);}
		elseif(!Fltr::isSqlDate($newSeasonDateEnd))		{$errors[] = "Enddatum ist kein Datum.";}
		
		if(!empty($errors))
		{
			$response['status']  = "error";
			$response['message'] = implode("\n", $errors);
			
		}
		else 	
		{
			require_once BASIS_DIR.'/MVC/DBFactory.php';
			$dbh = \MVC\DBFactory::getDBH();
			
			if(!$editSeasonId)
			{
				//check doublicates
				$qDuplicates = "SELECT * FROM seasons WHERE season_name = :name";
				try
				{
					$sthDup = $dbh->prepare($qDuplicates);
					$sthDup->execute([':name' => $newSeasonName]);
					$resDup = $sthDup->fetch(PDO::FETCH_ASSOC, 1);

					if(empty($resDup))
					{
						$qInsert = "INSERT INTO seasons (season_name, date_start, date_end) VALUES(:season_name, :date_start, :date_end)";
						$sth = $dbh->prepare($qInsert);
						$sth->execute([':season_name' => $newSeasonName, ':date_start' => $newSeasonDateStart, ':date_end' => $newSeasonDateEnd]);

						$response['status']  = "ok";
						#$response['message'] = "Der Seasonsname existiert bereits. Geben Sie ein anderes ein.";
					}
					else
					{
						$response['status']  = "error";
						$response['message'] = "Der Seasonsname existiert bereits. Geben Sie ein anderes ein.";
					}

				}
				catch (Exception $ex) {
					print $ex;
					return "Fehler bei SQL-Queries.";
				}
			}
			else
			{
				$qUpdate = "UPDATE seasons SET season_name = :season_name, date_start = :date_start, date_end = :date_end WHERE season_id = :season_id";
				try
				{
					$sth = $dbh->prepare($qUpdate);
					$sth->execute([':season_name' => $newSeasonName, ':date_start' => $newSeasonDateStart, ':date_end' => $newSeasonDateEnd, ':season_id' => $editSeasonId]);
					$response['status']  = "ok";
				}
				catch (Exception $ex) {
					print $ex;
					return "Fehler bei SQL-Queries.";
				}
			}
		}
		
		header("Content-type: application/json");
		exit(json_encode($response));
	}
	
	public function setActiveSeason()
	{
		$seasonId	= filter_input(INPUT_POST, 'season_id', FILTER_SANITIZE_NUMBER_INT);
		$response	= array();
		
		if(!$seasonId)
		{
			$response['status']  = "error";
			$response['message'] = "season_id fehlt.";
		}
		else
		{
			$dbh     = \MVC\DBFactory::getDBH();
			$qReset  = "UPDATE seasons SET is_active = '0'";
			$qUpdate = "UPDATE seasons SET is_active = '1'  WHERE season_id = :season_id";
			
			try
			{
				$dbh->query($qReset);

				$sth = $dbh->prepare($qUpdate);
				$sth->execute([':season_id' => $seasonId]);
				$response['status']  = "ok";
			}
			catch (Exception $ex) {
				print $ex;
				return "Fehler bei SQL-Queries.";
			}
		}
		
		header("Content-type: application/json");
		exit(json_encode($response));
	}
	
	public static function getActiveSeason()
	{
		$q   = "SELECT * FROM seasons WHERE is_active = '1'";
		$dbh = \MVC\DBFactory::getDBH();
		try
		{
			$dbh->query($q);

			$sth = $dbh->prepare($q);
			$sth->execute();
			$rs = $sth->fetch(PDO::FETCH_ASSOC);
			
			return $rs;
		}
		catch (Exception $ex) {
			print $ex;
			return "Fehler bei SQL-Queries.";
		}
	}
}
