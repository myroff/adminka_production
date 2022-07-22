<?php
namespace Kurse;

/**
 * 12.06.2022
 * API to work with seasonal course configurations.
 */

use \AbstractSources\AbstractApi as AbstractApi;
use \Tools\Filter as Fltr;

class SeasonalCourseConfigAPI extends AbstractApi
{
    public function insertUpdateConfig()
    {
        $fehler         = array();
        $fehlerInput    = array();
        $dataPost       = array();
        $output         = array();
        $testData       = array();

        if(!empty($_POST['kurId'])) {

            $_POST['kurId'] = Fltr::deleteSpace($_POST['kurId']);
            if(Fltr::isInt($_POST['kurId']))
            {
                $dataPost['kurId'] = $_POST['kurId'];
                $testData['kurId'] = $_POST['kurId'];
            }
            else
            {
                $fehler[] = "Kurs-Id für Unterricht soll ein Integer sein.";
                $fehlerInput[] = 'kurId';
            }
        }
        else
        {
            $fehler[] = "Kurs-ID für Unterricht fehlt.";
            $fehlerInput[] = 'kurId';
        }

        if(!empty($_POST['season_id'])) {

            $_POST['season_id'] = Fltr::deleteSpace($_POST['season_id']);
            if(Fltr::isInt($_POST['season_id']))
            {
                $dataPost['season_id'] = $_POST['season_id'];
                $testData['season_id'] = $_POST['season_id'];
            }
            else
            {
                $fehler[] = "SeasonID für Unterricht soll ein Integer sein.";
                $fehlerInput[] = 'kurId';
            }
        }
        else
        {
            $fehler[] = "Saison-ID für Unterricht fehlt.";
            $fehlerInput[] = 'season_id';
        }
        // Lehrer
        if(!empty($_POST['lehrId'])) {

            $_POST['lehrId'] = Fltr::deleteSpace($_POST['lehrId']);
            if(Fltr::isInt($_POST['lehrId']))
            {
                $dataPost['lehrId'] = $_POST['lehrId'];
            }
            else
            {
                $fehler[] = "lehrId für Unterricht soll ein Integer sein.";
                $fehlerInput[] = 'lehrId';
            }
        }

        //kurName - Name des Kurses
        if(!empty($_POST['course_name_suffix'])) {

            $_POST['course_name_suffix'] = trim($_POST['course_name_suffix']);
            $_POST['course_name_suffix'] = str_replace("/\s+/", " ", $_POST['course_name_suffix']);

            if(Fltr::isText($_POST['course_name_suffix'])) {
                $dataPost['course_name_suffix'] = $_POST['course_name_suffix'];
            }
            else {
                $fehler[] = "Der Name des Kurses ist falsch eingegeben.<br>Erlaubt sind Ziffern 0 bis 9, Buchstaben, Leerzeichen, Symbolen (. , : ; ! ? ' * = # / \ \" - + _).";
                $fehlerInput[] = 'kurName';
            }
        }

        //Preis des Unterrichts
        if(!empty($_POST['kurPreis'])) {

            $_POST['kurPreis'] = str_replace(" ", "", $_POST['kurPreis']);
            $_POST['kurPreis'] = str_replace(",", ".", $_POST['kurPreis']);

            if(Fltr::isFloat($_POST['kurPreis']) OR Fltr::isInt($_POST['kurPreis'])) {
                $dataPost['kurPreis'] = $_POST['kurPreis'];
            }
            else {
                $fehler[] = "Preis des Kurses ist falsch eingegeben.";
                $fehlerInput[] = 'kurPreis';
            }
        }

        //Preis-Typ: pro Monat oder Stunde
        if(!empty($_POST['isHourPrice'])) {

            $_POST['isHourPrice'] = Fltr::deleteSpace($_POST['isHourPrice']);
            if(Fltr::isRowString($_POST['isHourPrice']))
            {
                $dataPost['isHourPrice'] = $_POST['isHourPrice'] === 'proStunde' ? 1 : 0;
            }
            else
            {
                $fehler[] = "Zahlungstyp ist falsch eingegeben. Erlaubt sind nur die Buchstaben.";
                $fehlerInput[] = 'isHourPrice';
            }
        }

        // Alter
        $seasonMinAge = empty($_POST['kurMinAlter']) ? '' : Fltr::deleteSpace($_POST['kurMinAlter']);
        $seasonMaxAge = empty($_POST['kurMaxAlter']) ? '' : Fltr::deleteSpace($_POST['kurMaxAlter']);

        if(!empty($seasonMinAge) && Fltr::isInt($seasonMinAge)) {

            $dataPost['kurMinAlter'] = $seasonMinAge;

            if(empty($seasonMaxAge)) {
                $dataPost['kurMaxAlter'] = $seasonMinAge;
            }
        }
        elseif(!empty($seasonMinAge)) {
            $fehler[] =  "Jungstes Alter ist falsch eingegeben.";
            $fehlerInput[] = 'kurMinAlter';
        }

        if(!empty($seasonMaxAge) && Fltr::isInt($seasonMaxAge)) {

            $dataPost['kurMaxAlter'] = $seasonMaxAge;

            if(empty($seasonMinAge)) {
                $dataPost['kurMinAlter'] = $seasonMaxAge;
            }
        }
        elseif(!empty($seasonMaxAge)) {
            $fehler[] =  "Ältestes Alter ist falsch eingegeben.";
            $fehlerInput[] = 'kurMaxAlter';
        }

        // Klasse
        $seasonMinClass = empty($_POST['kurMinKlasse']) ? '' : Fltr::deleteSpace($_POST['kurMinKlasse']);
        $seasonMaxClass = empty($_POST['kurMaxKlasse']) ? '' : Fltr::deleteSpace($_POST['kurMaxKlasse']);

        //min_class
        if(!empty($seasonMinClass) && Fltr::isInt($seasonMinClass)) {

            $dataPost['kurMinKlasse'] = $seasonMinClass;

            if(empty($seasonMaxClass)) {
                $dataPost['kurMaxKlasse'] = $seasonMinClass;
            }
        }
        elseif(!empty($seasonMinClass)) {
            $fehler[] =  "Jungste Klasse ist falsch eingegeben.";
            $fehlerInput[] = 'kurMinKlasse';
        }

        // max_class
        if(!empty($seasonMaxClass) && Fltr::isInt($seasonMaxClass)) {

            $dataPost['kurMaxKlasse'] = $seasonMaxClass;

            if(empty($seasonMinClass)) {
                $dataPost['kurMinKlasse'] = $seasonMaxClass;
            }
        }
        elseif(!empty($seasonMaxClass)) {
            $fehler[] =  "Älteste Klasse ist falsch eingegeben.";
            $fehlerInput[] = 'kurMaxKlasse';
        }

        if(empty($fehler)) {

            // check if there is already an entry for this season and course
            $model = new \Kurse\CourseToSeasonsModel();

            $foundEntry = $model->getEntryByKey($testData['kurId'], $testData['season_id']);

            // update entry if the keys are already in use
            if (!empty($foundEntry)) {

                $model->updateEntry($dataPost);
                $fehler[] = "Es gibt bereits ein Eintrag für dieses Season.";

            } else {

                $model->insertNewEntry($dataPost);
            }
        }
        else {
            $output = array(
                'fehler' => implode('<br>', $fehler),
                'fehlerInput' => $fehlerInput
            );
        }

        $this->outputJson($output);
    }

    public function deleteConfig()
    {
        $fehler         = array();
        $dataPost       = array();
        $output         = array();

        if(!empty($_POST['kurId']))
        {
            $_POST['kurId'] = Fltr::deleteSpace($_POST['kurId']);

            if(Fltr::isInt($_POST['kurId']))
            {
                $dataPost['kurId'] = $_POST['kurId'];
            }
            else
            {
                $fehler[] = "Kurs-Id für Unterricht soll ein Integer sein.";
            }
        }
        else
        {
            $fehler[] = "Kurs-ID für Unterricht fehlt.";
        }

        if(!empty($_POST['season_id']))
        {
            $_POST['season_id'] = Fltr::deleteSpace($_POST['season_id']);

            if(Fltr::isInt($_POST['season_id']))
            {
                $dataPost['season_id'] = $_POST['season_id'];
            }
            else
            {
                $fehler[] = "SeasonID für Unterricht soll ein Integer sein.";
            }
        }
        else
        {
            $fehler[] = "Saison-ID für Unterricht fehlt.";
        }

        if(empty($fehler)) {

            $model = new \Kurse\CourseToSeasonsModel();

            $model->deleteEntryWith($dataPost);

            $output = array(
                'status' => 'ok'
            );
        }
        else {
            $output = array(
                'fehler' => implode('<br>', $fehler),
                'fehlerInput' => $fehlerInput
            );
        }

        $this->outputJson($output);
    }
}
