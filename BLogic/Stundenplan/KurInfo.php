<?php
namespace Stundenplan;
use PDO as PDO;

class KurInfo
{
	public function ajaxGetInfo($kurId)
	{
		if(!isset($kurId) OR empty($kurId))
		{
			header("Content-type: html");
			exit("kurId fehlt.");
		}
		
		require_once BASIS_DIR.'/MVC/DBFactory.php';
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}
		
		$q = "SELECT kn.kndId, kn.vorname, kn.name, khk.von, khk.bis FROM kurse as ku LEFT JOIN kundehatkurse as khk USING(kurId) LEFT JOIN kunden as kn USING(kndId)"
				. " WHERE ku.kurId = :kurId AND (NOW() <= khk.bis)";//BETWEEN khk.von AND khk.bis
		
		$res = array();
		
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute(array(':kurId' => (int)$kurId));
			$res = $sth->fetchAll(PDO::FETCH_ASSOC);
			
		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
		
		if(empty($res))
		{
			$output = "Der Kurs ist nicht besetzt.";
		}
		else
		{
			$output = self::infoOutput($res);
		}
		
		header("Content-type: html");
		exit($output);
		return $output;
	}
	
	private function infoOutput($arr)
	{
		$out = "<table><tr><th>Vorname</th><th>Name</th><th>Angemeldet</th></tr>";
		
		foreach($arr as $r)
		{
			$out .= "<tr><td><a target='_blank' href='".BASIS_URL."/admin/bezahlenById/".$r['kndId']."'>".$r['vorname']."</a></td><td>".$r['name']."</td>"
					."<td>".date('d.m.y',  strtotime($r['von']))." - ".date('d.m.y',  strtotime($r['bis']))."</td></tr>";
		}
		
		$out .= "</table>";
		
		return $out;
	}
}
