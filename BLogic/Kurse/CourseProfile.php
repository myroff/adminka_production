<?php
namespace Kurse;

use PDO as PDO;
use MVC\DBFactory as DBFactory;
use Kurse\CourseProfileModel;
use Viewer\Viewer;
use Tools\Filter as Fltr;

class CourseProfile
{
    public function showListPage()
    {
        $message = '';

        // check adding new prodile
        if (!empty($_POST['new_profile_name'])) {
            $message = $this->addNewProfile(
                Fltr::filterStr($_POST['new_profile_name'])
            );
        }
        // check editing of existed profiles
        elseif (!empty($_POST['edited_profile_name']) && !empty($_POST['edit_course_profile_profile_id'])) {
            $message = $this->updateProfileName (
                $_POST['edit_course_profile_profile_id'],
                Fltr::filterStr($_POST['edited_profile_name'])
            );
        }
        // check if we have to delete the profile
        elseif (!empty($_POST['delete_profile_id'])) {
            $message = $this->deleteProfile((int) $_POST['delete_profile_id']);
        }

        $courseList = (new CourseProfileModel)->getAllEntries(['profile_name' => 'ASC']);

        $data = ['profiles' => $courseList, 'systemMessage' => $message];

        $viewer = new Viewer();
        $viewer->display('/Courses/CourseProfileList.twig', $data);
    }

    public function addNewProfile($profileName): string
    {
        $model = new CourseProfileModel();
        $checkName = $model->getEntriesWhere(['profile_name' => $profileName]);

        if (!empty($checkName)) {
            return "Das Fach mit dem Namen ist bereits belegt.";
        }

        $inserted = $model->insertNewEntry(['profile_name' => $profileName]);

        if ($inserted) {
            return "Neues Fach ist eingetragen.";
        }

        return "Neues Fach konnte nicht gespeichert werden. Leiten Sie die Frage an Sysadmin.";
    }

    public function updateProfileName($profileId, $profileName): string
    {
        if (empty ($profileName)) {
            return "Kann nicht updated werden: der neue Fachname ist leer.";
        }

        $model = new CourseProfileModel();

        $updated = $model->updateEntry([
            'profile_id' => $profileId,
            'profile_name' => $profileName
        ]);

        if ($updated) {
            return "Fachname ist updated.";
        }

        return "Der Fachname konnte nicht updated werden. Leiten Sie die Frage an Sysadmin.";
    }

    public function deleteProfile($profileId): string
    {
        $model = new CourseProfileModel();
        $deleted = $model->deleteEntryWith(['profile_id' => (int) $profileId]);

        if ($deleted) {
            return "Der Fach wurde erfolgreich entfernt.";
        }

        return "Der Fach konnte nicht entfernt werden. Evtl. haben Sie falsche Fach-ID übermittelt.";
    }
}
