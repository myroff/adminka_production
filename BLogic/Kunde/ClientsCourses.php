<?php
namespace Kunde;
/**
 * Show courses that has the client
 */
use PDO as PDO;

use Tools\TmplTools as TmplTls;
use Kurse\KursSelector as KurSel;
use Kurse\Seasons as Seasons;

class ClientsCourses
{
	public static function getCourseModule($clientId, $editMode=false)
	{
		$activeSeason		= Seasons::getActiveSeason();
		$lessonsData		= self::getCoursesData($clientId, $activeSeason['season_id']);
		$curSeason			= empty($lessonsData[0]['season_id']) ? '' : $lessonsData[0]['season_id'];
		$vars['lessons']	= $lessonsData;
		$vars['client_id']	= $clientId;
		$vars['editMode']	= $editMode ? true : false;
		$vars['s_season']	= TmplTls::getSeasonsSelector("s_season", "s_season", $curSeason, "Season", 1);
		$vars['changeCourseSelector']	= KurSel::getKursSelector("newKurId", "changeKurs_newKurId",  "10", "/admin/ajaxKursSelectorUpdate", 1);
/*
		$options	= []; #array('cache' => TWIG_CACHE_DIR);
		$loader		= new \Twig_Loader_Filesystem(TWIG_TEMPLATE_DIR);
		$twig		= new \Twig_Environment($loader, $options);
		$twigTmpl	= $twig->load('/Module/Client/ClientsCourseModule.twig');

		return $twigTmpl->render($vars);
*/
		$viewer = new \Viewer\Viewer();
		return $viewer->render('/Module/Client/ClientsCourseModule.twig', $vars);
	}

	public static function getCoursesData($clientId, $curSeasonId=false)
	{
		if((int)$curSeasonId)
		{
			$whereSeason = " AND khk.season_id = ".(int)$curSeasonId;
		}
		else
		{
			$whereSeason = "";//" AND season.is_active = 1";
		}

		$q = "SELECT khk.*, k.*, l.vorname, l.name,"
					." group_concat('{\"wochentag\":\"',st.wochentag,'\",\"time\":\"', TIME_FORMAT(st.anfang, '%H:%i'),' - ', TIME_FORMAT(st.ende, '%H:%i'),'\"}' SEPARATOR',') as termin"
					.", season.season_name, season.season_id"
				." FROM kundehatkurse as khk LEFT JOIN kurse as k USING(kurId)"
				." LEFT JOIN lehrer as l USING(lehrId)"
				." LEFT JOIN stundenplan as st USING(kurId)"
				." LEFT JOIN seasons as season ON season.season_id = khk.season_id"
				." WHERE khk.kndId=:kndId".$whereSeason
				." GROUP BY khk.eintrId "
				." ORDER By khk.eintrId DESC";

		$dbh = \MVC\DBFactory::getDBH();

		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute(array(":kndId" => $clientId));
			$res = $sth->fetchAll(PDO::FETCH_ASSOC);

			$stdModel = new \Stundenplan\StundenplanModel();
			$res = $stdModel->updateSeasonalData($res);
		} catch (Exception $ex) {
			print $ex;
			return FALSE;
		}

		return $res;
	}

	public function updateTable()
	{
		$clientId = filter_input(INPUT_POST, 'client_id', FILTER_SANITIZE_NUMBER_INT);
		$seasonId = filter_input(INPUT_POST, 'season_id', FILTER_SANITIZE_NUMBER_INT);
		$editMode = filter_input(INPUT_POST, 'mode', FILTER_SANITIZE_NUMBER_INT);

		$vars['lessons']	= self::getCoursesData($clientId, $seasonId);
		$vars['client_id']	= $clientId;
		$vars['editMode']	= $editMode ? true : false;
/*
		$options	= []; #array('cache' => TWIG_CACHE_DIR);
		$loader		= new \Twig_Loader_Filesystem(TWIG_TEMPLATE_DIR);
		$twig		= new \Twig_Environment($loader, $options);
		$twigTmpl	= $twig->load('/Module/Client/ClientsCourseTable.twig');
		$content	= $twigTmpl->render($vars);
*/
		$viewer 	= new \Viewer\Viewer();
		$content	= $viewer->render('/Module/Client/ClientsCourseTable.twig', $vars);

		exit($content);
	}
}
