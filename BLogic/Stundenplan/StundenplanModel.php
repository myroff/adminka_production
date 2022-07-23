<?php
namespace Stundenplan;

class StundenplanModel
{
    const tableName = 'stundenplan';
    const key       = ['stnPlId'];

    /**
     * Get prepared and sorted data to print scheduler table.
     * @param array $data - conditions to filter scheduler
     * @return array
     */
    public function getDataForScheduler(array $data) : array
    {
        $where = " k.isKurInactive is NOT TRUE AND ";
        $having = "";

        if(isset($data[':raum']))
        {
            $having .= " stpl.raum = :raum AND";
        }
        if(isset($data[':lehrId']))
        {
            $having .= " l.lehrId = :lehrId AND";
        }
        if(isset($data[':wochentag']))
        {
            $where .= " stpl.wochentag = :wochentag AND";
        }
        if(isset($data[':kurName']))
        {
            $where .= " k.kurName LIKE :kurName AND";
            $data[':kurName'] .= '%';
        }
        if(isset($data[':alter']))
        {
            $where .= "( :alter BETWEEN k.kurMinAlter AND k.kurMaxAlter) AND";
            $data[':alter'] .= '%';
        }
        if(isset($data[':klasse']))
        {
            $where .= "( :klasse BETWEEN k.kurMinKlasse AND k.kurMaxKlasse) AND";
            $data[':klasse'] .= '%';
        }
        //set current season
        $curSeason = "s.is_active = 1";
        if(!empty($data[':season_id']))
        {
            $curSeason = "stpl.season_id = :season_id";
        }

        $having = substr($having, 0, -4);
        $where = substr($where, 0, -4);

        $q = "SELECT ( (TIME_TO_SEC(ende) - TIME_TO_SEC(anfang) )/60 ) as kurLength,"
                . " TIME_FORMAT(anfang, '%H:%i') as anfang, TIME_FORMAT(ende, '%H:%i') as ende, wochentag, raum,"
                . " s.season_id, k.*, l.name, l.vorname, l.lehrId, count(khk.kndId) as countKnd, k.maxKnd, stpl.stnPlId"
                . " FROM stundenplan as stpl"
                . " LEFT JOIN seasons as s ON s.season_id=stpl.season_id"
                . " LEFT JOIN kurse as k USING(kurId)"
                . " LEFT JOIN lehrer as l USING(lehrId)"
                . " LEFT JOIN (SELECT * FROM kundehatkurse WHERE NOW() <= bis) as khk USING(kurId)"//BETWEEN von AND
                . " WHERE ".$curSeason;

        $q .= empty($where) ? '' : " AND ".$where;
        $q .= " GROUP By stpl.stnPlId ";

        $q .= empty($having) ? '' : " HAVING " . $having;
        $q .= " ORDER By stpl.wochentag, HOUR(stpl.anfang), cast(stpl.raum as unsigned) ASC";

        try
        {
            $dbh = \MVC\DBFactory::getDBH();
            $sth = $dbh->prepare($q);
            $sth->execute($data);
            $rs = $sth->fetchAll(\PDO::FETCH_ASSOC);

            $rs = $this->updateSeasonalData($rs);

            return $rs;

        } catch (Exception $ex) {
            //print $ex;
            return FALSE;
        }
    }

    /**
     * Update course data with seosonal data (teachers, age, class etc.)
     * @param array $data - results of function $this->getDataForScheduler(...)
     * @return array
     */
    function updateSeasonalData(array $data) : array
    {
        $seasonalModel = new \Kurse\CourseToSeasonsModel();

        foreach($data as $key => $val) {

            $seasonalData = $seasonalModel->getFrontendInfoToCourseId($val['kurId'], $val['season_id']);

            if (!empty($seasonalData[0])) {

                $seasonalData = $seasonalData[0];

                if(!empty($seasonalData['course_name_suffix'])) {
                    $data[$key]['kurName'] .= ' '.$seasonalData['course_name_suffix'];
                }

                foreach($seasonalData as $seasonKey => $seasonVal) {

                    if (!empty($seasonVal) && isset($val[$seasonKey])) {
                        $data[$key][$seasonKey] = $seasonVal;
                    }
                }

            }
        }

        return $data;
    }

    /**
     * get scheduler to course.
     * @param int $kurId - course id
     * @param int $seasonId - season id
     * @return array - array with course dates
     */
    public function getStundenplanToKurId(int $kurId, int $seasonId) : array
    {
        $q = "SELECT * FROM stundenplan WHERE kurId = :kurId AND season_id = :seasonId";

        $dbh = \MVC\DBFactory::getDBH();
        $sth = $dbh->prepare($q);
        $sth->execute([':kurId' => $kurId, ':seasonId' => $seasonId]);

        return $sth->fetchAll(\PDO::FETCH_ASSOC) ?? [];
    }
}
