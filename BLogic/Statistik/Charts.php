<?php
namespace Statistik;
use PDO as PDO;

use MVC\DBFactory as DBFactory;

class Charts {
	public function getDataChar() {
		$charReq = isset($_POST['charReq']) && !empty($_POST['charReq']) ? $_POST['charReq']: "citizens";
		$data = array();
		$o = "";
		$seasonId = $_POST['season_id'] ?: '';

		switch ($charReq) {
			case "gender":
				$data = $this->getGenderData($seasonId);
				$o .= $this->arrayToCharData($data,"circle");
				break;
			case 'population':
				$data = $this->getPopulationStat($seasonId);
				$o .= $this->arrayToCharData($data,"bar");
				break;
			case 'ageStat':
				$data = $this->getAgeStat($seasonId);
				$o .= $this->arrayToCharData($data,"bar");
				break;
			case 'klassesStat':
				$data = $this->getKlassesStat($seasonId);
				$o .= $this->arrayToCharData($data,"bar");
				break;
			default:
				break;
		}
		$o .= "";
	//return data-string AgeStat
		header('Content-Type:application/javascript');// text/plain; charset=utf-8
		exit($o);
	}//public function getDataChar()

	private function arrayToCharData($arr,$charType='line'){
		$labels = "";
		$value = "";
	//get labels and value
		foreach($arr as $k=>$v)
		{
			$labels .= "'".$k."',";
			$value .= $v.",";
		}
		$labels = substr($labels, 0, -1);
		$value = substr($value, 0, -1);

	//data for Line-Diagram
		if($charType === "line"){
			/*
			foreach($arr as $k=>$v)
			{
				$labels .= "'".$k."',";
				$value .= $v.",";
			}
			$labels = substr($labels, 0, -1);
			$value = substr($value, 0, -1);
			*/
			return "{"
						."labels:[".$labels."]],"
						."datasets:[{label:'Bevölkerung',data:[".$value."]}]"
					."}";
		}
	//data for bar-diagram
		elseif($charType === "bar"){
			$out = "{labels: [".$labels."],"
						."datasets: ["
							."{"
								."label: 'My First dataset',"
								//."fillColor: 'rgba(220,220,220,0.5)',"
								//."strokeColor: 'rgba(220,220,220,0.8)',"
								//."highlightFill: 'rgba(220,220,220,0.75)',"
								//."highlightStroke: 'rgba(220,220,220,1)',"
								."data: [".$value."]"
							."}"
						."]"
					."}";
			return $out;
		}
	//data for Circle-Diagram
		elseif($charType === "circle"){
			$out = "[";
			foreach($arr as $k=>$v)
			{
				$out .= "{label:'".$k."',value:".$v."},";
			}
			$out = substr($out, 0, -1);
			$out .= "]";
			return $out;
		}
	}//private function arrayToCharData($arr)

	private function getGenderData($seasonId){
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}

		$condition = [':season' => '%'];

		if($seasonId)
		{
			$condition = [':season' => $seasonId];
		}

		$qMan = "SELECT count(*) as 'count' FROM kunden WHERE anrede LIKE 'Herr' AND kndId IN (SELECT kndId FROM kundehatkurse WHERE season_id LIKE :season GROUP BY kndId)";
		$qWomen = "SELECT count(*) as 'count' FROM kunden WHERE anrede LIKE 'Frau' AND kndId IN (SELECT kndId FROM kundehatkurse WHERE season_id LIKE :season GROUP BY kndId)";

		$resM = array();
		$resW = array();

		try
		{
			$sth = $dbh->prepare($qMan);
			$sth->execute($condition);
			$resM = $sth->fetch(PDO::FETCH_ASSOC,1);

			$sth = $dbh->prepare($qWomen);
			$sth->execute($condition);
			$resW = $sth->fetch(PDO::FETCH_ASSOC,1);

		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}

		$res = array("Man"=>$resM['count'], "Frau"=>$resW['count']);
		return $res;

	}//private function getGenderData()

	private function getPopulationStat($seasonId)
	{
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}

		$condition = [':season' => '%'];

		if($seasonId)
		{
			$condition = [':season' => $seasonId];
		}

		$q = "SELECT stadt, count(*) as population FROM kunden"
			." WHERE kndId IN (SELECT kndId FROM kundehatkurse WHERE season_id LIKE :season GROUP BY kndId)"
			." GROUP BY stadt ORDER BY population DESC";

		$res = array();

		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute($condition);
			$res = $sth->fetchAll(PDO::FETCH_KEY_PAIR);

			return $res;

		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
	}//private function getPopulationStat()

	private function getAgeStat($seasonId)
	{
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}

		$condition = [':season' => '%'];

		if($seasonId)
		{
			$condition = [':season' => $seasonId];
		}

		$q = "SELECT TIMESTAMPDIFF(YEAR,geburtsdatum,CURDATE()) AS age, count(kndId) as number FROM kunden"
			." WHERE kndId IN (SELECT kndId FROM kundehatkurse WHERE season_id LIKE :season GROUP BY kndId)"
			." GROUP BY age ORDER BY age";

		$res = array();

		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute($condition);
			$res = $sth->fetchAll(PDO::FETCH_KEY_PAIR);

			return $res;

		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
	}//private function getAgeStat()

	private function getKlassesStat($seasonId)
	{
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}

		$condition = [':season' => '%'];

		if($seasonId)
		{
			$condition = [':season' => $seasonId];
		}

		$q = "SELECT ku.kurMaxKlasse AS Klasse, count( k.kndId ) AS 'Schueler'
FROM kunden AS k
JOIN kundehatkurse
USING ( kndId )
JOIN kurse AS ku
USING ( kurId )
WHERE ku.kurMaxKlasse IS NOT NULL AND kndId IN (SELECT kndId FROM kundehatkurse WHERE season_id LIKE :season GROUP BY kndId)
GROUP BY ku.kurMaxKlasse";

		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute($condition);
			$res = $sth->fetchAll(PDO::FETCH_KEY_PAIR);

			return $res;

		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
	}
}
