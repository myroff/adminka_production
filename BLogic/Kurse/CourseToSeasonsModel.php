<?php
namespace Kurse;
/**
 * 12.06.2022.
 * DB 'course_to_seasons'.
 */
use \AbstractSources\AbstractModel as AbstractModel;

class CourseToSeasonsModel extends AbstractModel
{
    const tableName = 'course_to_seasons';
    const key       = ['kurId', 'season_id'];

    /**
     * get entry by primary key: kurId + season_id
     * @param int $courseId
     * @param int $seasonId
     * @return array
     */
    public function getEntryByKey(int $courseId, int $seasonId) : array
    {
        $out = array();

        if(!(int) $courseId || !(int) $seasonId) {
            return $out;
        }

        $where = ['kurId' => $courseId, 'season_id' => $seasonId];
        $data = $this->getEntriesWhere($where);
        return $data[0] ?? [];
    }

    /**
     * get info for frontend: data with season names and teachers names.
     */
    public function getFrontendInfoToCourseId(int $courseId, int $seasonId = NULL) : array
    {
        $qSeasonsConfigs = "
 SELECT cts.*, sea.season_name, l.vorname, l.name
 FROM course_to_seasons as cts
 JOIN seasons as sea USING(season_id)
 LEFT JOIN lehrer as l ON(l.lehrId = cts.lehrId)
 WHERE cts.kurId = :courseId";

        $data = [":courseId" => $courseId];

        if($seasonId) {
            $qSeasonsConfigs .= " AND cts.season_id = :season_id";
            #$qSeasonsConfigs .= " LIMIT 1";
            $data[':season_id'] = $seasonId;
        }

        $dbh = \MVC\DBFactory::getDBH();
        $sth = $dbh->prepare($qSeasonsConfigs);
        $sth->execute($data);

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

}
