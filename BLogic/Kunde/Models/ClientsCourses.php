<?php
/**
 * Get full information to clients courses.
 */
namespace Kunde\Models;
use PDO as PDO;

class ClientsCourses
{
    /**
     * get courses Data to clients id and season id.
     * without $seasonId u get the empty array
     * @param int $clientsId
     * @param int $seasonId
     * @return array
     */
    public function getCourseData($clientsId, $seasonId)
    {
        $results = array();

        if(!(int)$seasonId) {

            return $results;
        }

        $q = <<<SQL
SELECT khk.*, k.*,
CONCAT('[', GROUP_CONCAT(JSON_OBJECT('kurId', stn.kurId, 'season_id', stn.season_id, 'anfang', stn.anfang, 'ende', stn.ende, 'raum', stn.raum)), ']') as stundenplan
FROM kundehatkurse as khk
JOIN kurse as k USING (kurId)
LEFT JOIN stundenplan as stn USING(season_id, kurId)
WHERE khk.kndId = :clientsId AND season_id = :seasonId
GROUP BY khk.eintrId
SQL;

        try
        {
            $dbh = \MVC\DBFactory::getDBH();
            $sth = $dbh->prepare($q);
            $sth->execute([':clientsId' => $clientsId, ':seasonId' => $seasonId]);
            $results = $sth->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as $key => $val) {
                if (!empty($val)) {
                    $results[$key]['stundenplan'] = json_decode($val['stundenplan'], 1);
                }
            }

        } catch (Exception $ex) {
            //print $ex;
            return FALSE;
        }

        return $results;
    }
}
