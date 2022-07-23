<?php
namespace Kurse;

use \AbstractSources\AbstractModel as AbstractModel;

class KurseModel extends AbstractModel
{
    const tableName = 'kurse';
    const key       = ['kurId'];

    public function getSeasonalCourseData(int $kurId, int $seasonId) : array
    {
        $courseData = $this->getEntriesWhere(['kurId' => $kurId]);
        $courseData = $courseData[0] ?? [];

        if (empty($seasonId) || empty($courseData)) {

            return $courseData;
        }

        $courseToSeason = new \Kurse\CourseToSeasonsModel();

        $seasonalData = $courseToSeason->getEntryByKey($kurId, $seasonId);

        if (empty($seasonalData)) {

            return $courseData;
        }
        // remove primary keys
        unset($seasonalData['kurId']);
        unset($seasonalData['season_id']);

        //extend course name
        $courseData['kurName'] .= ' ' . $seasonalData['course_name_suffix'] ?? '';
        unset($seasonalData['course_name_suffix']);

        foreach($seasonalData as $key => $val) {
            if( !is_null($val)) {
                $courseData[$key] = $val;
            }
        }

        return $courseData;
    }

}
