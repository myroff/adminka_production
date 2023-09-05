<?php
namespace Kurse;

use \AbstractSources\AbstractModel as AbstractModel;

class CourseProfileModel extends AbstractModel
{
    const tableName = 'course_profiles';
    const key       = ['profile_id'];
}
