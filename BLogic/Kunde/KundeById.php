<?php
namespace Kunde;
use PDO as PDO;

class KundeById {
    public function show($kId)
    {
        require_once BASIS_DIR.'/MVC/DBFactory.php';
        $dbh = \MVC\DBFactory::getDBH();
        if(!$dbh)
        {
            echo "no Connection to DB";
            return FALSE;
        }
		
		$q = "SELECT k.*, zd.*, GROUP_CONCAT(empf.vorname,' ', empf.name) as empfohlenDurch"
			. " FROM kunden as k LEFT JOIN zahlungsdaten as zd USING(kndId)"
			. " LEFT JOIN kunden as empf ON empf.kndId=k.empfohlenId"
			. " WHERE k.kndId=:kndId";
		
		$qu = "SELECT khk.*, k.*, l.name as 'lName', l.vorname as 'lVorname'"
				.", group_concat('{\"wochentag\":\"',st.wochentag,'\",\"time\":\"', TIME_FORMAT(st.anfang, '%H:%i'),' - ', TIME_FORMAT(st.ende, '%H:%i'),'\"}' SEPARATOR',') as termin"
			. " FROM kundehatkurse as khk LEFT JOIN kurse as k USING(kurId)"
			. " LEFT JOIN stundenplan as st USING(kurId)"
			. " LEFT JOIN lehrer as l USING(lehrId)"
			. " WHERE khk.kndId=:kndId"
			. " GROUP BY khk.eintrId"
			. " ORDER By k.kurName ASC";
		
		
		try
		{
		//Kunde-Info
			$sth = $dbh->prepare($q);
			$sth->execute(array(":kndId" => $kId));
			$res = $sth->fetch(PDO::FETCH_ASSOC, 1);
		//Kurse
			$sth = $dbh->prepare($qu);
			$sth->execute(array(":kndId" => $kId));
			$ures = $sth->fetchAll(PDO::FETCH_ASSOC);
			
		} catch (Exception $ex) {
			print $ex;
			return FALSE;
		}
		
		include_once BASIS_DIR .'/Templates/Kunde/KundeById.tmpl.php';
		return;
    }
}
