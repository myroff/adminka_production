<?php
namespace Kunde;
use PDO as PDO;
#require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
#require_once BASIS_DIR.'/BLogic/Kunde/Empfohlen.php';
use Kunde\Empfohlen as Empf;
#require_once BASIS_DIR.'/BLogic/Kurse/KursSelector.php';
use Kurse\KursSelector as KurSel;
#require_once BASIS_DIR.'/BLogic/Kunde/CommentToolsHtml.php';
use Kunde\CommentToolsHtml as CmntTlsHtml;

#require_once BASIS_DIR.'/Tools/TmplTools.php';
use Tools\TmplTools as TmplTls;

#require_once BASIS_DIR.'/BLogic/Payment/PaymentApi.php';
use Payment\PaymentApi as PaymentApi;

#require_once BASIS_DIR.'/BLogic/Kunde/ClientsCourses.php';
use Kunde\ClientsCourses as ClientsCourses;

//twig
require_once BASIS_DIR . '/Vendor/autoload.php';

class KundeBearbeiten
{
    public function showList()
    {
        $sArr = array();
        $sArr[':vorname'] = empty($_POST['vorname']) ? '' : $_POST['vorname'];
        $sArr[':name'] = empty($_POST['name']) ? '' : $_POST['name'];

        $res = $this->searchDates($sArr);

        include_once BASIS_DIR.'/Templates/KundeBearbeiten/KundeBearbeitenListe.tmpl.php';
        return;
    }

    private function searchDates($searchArr)
    {

        $dbh = \MVC\DBFactory::getDBH();
        if(!$dbh)
        {
            return FALSE;
        }

        $where = "";

        //delete empty entries
        $searchArr = array_filter($searchArr);

        foreach ($searchArr as $key => $value)
        {
            $where .= substr($key, 1) . " LIKE ";
            $where .= $key;
            $searchArr[$key] .= "%";
            $where .= " AND ";
        }

        $where = substr($where, 0, -5);

        $q = "SELECT * FROM kunden LEFT JOIN payment_data USING (kndId)";
        $q .= empty($where) ? '' : " WHERE " . $where;
        $q .= " ORDER BY LENGTH(kundenNummer) DESC, kundenNummer DESC";

        try
        {
            $sth = $dbh->prepare($q);
            $sth->execute($searchArr);
            $rs = $sth->fetchAll(PDO::FETCH_ASSOC);

            return $rs;

        } catch (Exception $ex) {
            //print $ex;
            return FALSE;
        }
    }

    public function showKundeById($kndId)
    {

        $dbh = \MVC\DBFactory::getDBH();
        if(!$dbh)
        {
            return FALSE;
        }

        $meldung = ['status' => 'ok', 'message' => ""];

        if(!empty($_POST['updateItemTable_Form_Name']))
        {
            $itemName = trim($_POST['updateItemTable_Form_Name']);
            $itemName = preg_replace("/\s+/  ", " ", $itemName);
        }

        if(isset($_POST['updateItemTable_Form_Value']))
        {
            $itemValue = trim($_POST['updateItemTable_Form_Value']);
            $itemValue = preg_replace("/\s+/  ", " ", $itemValue);
        }

        if(isset($itemName) AND isset($itemValue))
        {
            $updateStatus = self::updateItemInDB($kndId,$itemName,$itemValue);

            if ($updateStatus['status'] === 'duplicate') {

                $meldung['status'] = 'error';
                $meldung['message'] = "Update ist nicht möglich: wir haben einen Kunden mit den Daten gefunden.<br>".
                "Check '$itemName' = '$itemValue' bei ".
                "<a href='/admin/kundeBearbeitenById/{$updateStatus['duplicateData']['kndId']}' target='_blank'>".
                "Kunden-Nummer = {$updateStatus['duplicateData']['kundenNummer']} ".
                "{$updateStatus['duplicateData']['vorname']} {$updateStatus['duplicateData']['name']}".
                "</a>";
            }
            elseif (!empty($updateStatus['message'])) {
                $meldung['status'] = $updateStatus['status'];
                $meldung['message'] = $updateStatus['message'];
            }
        }

        $q = "SELECT k.*"
                .", pd.zdId, pd.kontoinhaber, pd.zdStrasse, pd.zdHausnummer, pd.zdPlz, pd.zdOrt, pd.bankName, pd.iban, pd.bic, pd.payment_id"
                .", pm.payment_name, GROUP_CONCAT(m.anrede,' ',m.vorname,' ',m.name) as mitarbeiter, GROUP_CONCAT(empf.vorname,' ', empf.name) as empfohlenDurch"
                ." FROM kunden as k"
                ." LEFT JOIN payment_data as pd USING(kndId)"
                ." LEFT JOIN payment_methods as pm USING(payment_id)"
                ." LEFT JOIN mitarbeiter as m ON k.erstelltVom = m.mtId"
                ." LEFT JOIN kunden as empf ON empf.kndId=k.empfohlenId"
                ." WHERE k.kndId=:kndId";
        /*
        $qu = "SELECT khk.*, k.*, l.vorname, l.name,"
                    ." group_concat('{\"wochentag\":\"',st.wochentag,'\",\"time\":\"', TIME_FORMAT(st.anfang, '%H:%i'),' - ', TIME_FORMAT(st.ende, '%H:%i'),'\"}' SEPARATOR',') as termin"
                ." FROM kundehatkurse as khk LEFT JOIN kurse as k USING(kurId)"
                ." LEFT JOIN lehrer as l USING(lehrId)"
                ." LEFT JOIN stundenplan as st USING(kurId)"
                ." WHERE khk.kndId=:kndId"
                ." GROUP BY khk.eintrId "
                ." ORDER By khk.eintrId DESC";
        */
        $res = array();
        $ures = array();

        // get max kundenNummer
        $qMaxKndId = "SELECT * FROM kunden ORDER BY CONVERT (kundenNummer, UNSIGNED) DESC LIMIT 1";

        try
        {
            $sth = $dbh->prepare($q);
            $sth->execute(array(":kndId" => $kndId));
            $res = $sth->fetch(PDO::FETCH_ASSOC, 1);

            $sthMaxKndId = $dbh->query($qMaxKndId);
            $resMaxKndId = $sthMaxKndId->fetch(PDO::FETCH_ASSOC);

            #$sth = $dbh->prepare($qu);
            #$sth->execute(array(":kndId" => $kndId));
            #$ures = $sth->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            print $ex;
            return FALSE;
        }
        //set Geburtsdatum to string format
        $res['geburtsdatum']	= Fltr::sqlDateToStr($res['geburtsdatum']);
        $res['printZahlungsArt']= Fltr::printZahlungsArt($res['payment_id']);

        $vars['pageName']	= "Kunde bearbeiten";
        $vars['client']		= $res;
        #$vars['lessons']	= $ures;
        $vars['meldung']	= $meldung;
        $vars['requestUri']	= $_SERVER['REQUEST_URI'];

        $vars['kursSelector']			= KurSel::getKursSelector("kurId", 'k_kurId', "10", "/admin/ajaxKursSelectorUpdate", 1);
        $vars['empfohlenDurchSelector']	= Empf::setButton("empfohlenId", 1);
        $vars['newCommentsForm']		= CmntTlsHtml::newCommentsForm($kndId);
        $vars['comments']				= CmntTlsHtml::showComments($kndId);
        $vars['commentsJs']				= CmntTlsHtml::newCommentsJsFnct($kndId);
        $vars['changeCourseSelector']	= KurSel::getKursSelector("newKurId", "changeKurs_newKurId",  "10", "/admin/ajaxKursSelectorUpdate", 1);

        $vars['clientsCourseModule']	= ClientsCourses::getCourseModule($kndId, 1);

        $bezahlMethoden					= PaymentApi::getSelectorData();
        $vars['zahlenMitSelector']		= TmplTls::printMaterializeSelector($bezahlMethoden, "updateBankDates_Form_Value", "zahlenMit", "", "", 0);

        $vars['maxKundenNummer']		= $resMaxKndId['kundenNummer'];
/*
        $options = []; #array('cache' => TWIG_CACHE_DIR);
        $loader = new \Twig_Loader_Filesystem(TWIG_TEMPLATE_DIR);
        $twig = new \Twig_Environment($loader, $options);
        $twigTmpl = $twig->load('/KundeBearbeiten/KundeBearbeitenById.twig');
        echo $twigTmpl->render($vars);
*/
        $viewer = new \Viewer\Viewer();
        $viewer->display('/KundeBearbeiten/KundeBearbeitenById.twig', $vars);
        return;
    }

    /**
     * @return array ['status' => <ok/error/duplicate>, 'message' => 'string', ('duplicateData' => <array>)]
     */
    private function updateItemInDB($kId, $itemName, $itemVal): array
    {

        if(!$kId)
        {
            return ['status' => 'ok', 'message' => "Es wurde keine kunden ID übermittelt."];
        }
        /*
        if(!$itemVal)
        {
            return "Es wurde keine kunden items Value übermittelt.";
        }
        */
        // $in für $itemName
        $in = "";

        $itemVal = trim($itemVal);

        // remove item description from the item name like "Kunden-Nummer\r\r\n(zuletzt:+'1457')"
        $itemName = trim(explode(' ', $itemName)[0]);

        $errorMsg = "";

        switch ($itemName)
        {
            case 'Kunden-Nummer':
                $in = "kundenNummer";

                if(empty($itemVal))
                {
                    $itemVal = NULL;
                }
                elseif( Fltr::isWordsAndNumbers($itemVal) )
                {

                }
                else
                {
                    $errorMsg = "Bei Kundennummer sind nur die Ziffern und Buchstaben erlaubt.";
                }
                break;

                break;
            case "Anrede":
                $in = "anrede";

                if(Fltr::isRowString($itemVal))
                {

                }
                else
                {
                    $errorMsg = "Ziffern bei Anrede sind unzulässig.";
                }
                break;

            case "Vorname":
                $in = "vorname";

                if(Fltr::isRowString($itemVal))
                {

                }
                else
                {
                    $errorMsg = "Beim Vornamen sind nur die Buchstaben erlaubt (klein und gross).";
                }
                break;

            case 'Name':
                $in = "name";

                if(Fltr::isRowString($itemVal))
                {

                }
                else
                {
                    $errorMsg = "Beim Namen sind nur die Buchstaben erlaubt (klein und gross).";
                }
                break;

            case "Geburtsdatum":
                $in = "geburtsdatum";
                $itemVal = str_replace(" ", "", $itemVal);
                if(Fltr::isDate($itemVal))
                {
                    $itemVal = Fltr::strToSqlDate($itemVal);
                }
                else
                {
                    $errorMsg = "Geburtsdatum ist falsch eingegeben.<br>";
                }
                break;

            case 'Telefon':
                $in = "telefon";
                $itemVal = str_replace(" ", "", $itemVal);
                if(Fltr::isTelefone($itemVal))
                {

                }
                else
                {
                    $errorMsg = "Telefon ist falsch eingegeben. Beispiel: +49 211 123 456 789.";
                }
                break;

            case 'Handy':
                $in = "handy";

                $itemVal = str_replace(" ", "", $itemVal);
                if(Fltr::isTelefone($itemVal))
                {

                }
                else
                {
                    $errorMsg = "Handy ist falsch eingegeben. Beispiel: +49 151 123 456 789.";
                }
                break;

            case 'Email':
                $in = "email";

                $itemVal = str_replace(" ", "", $itemVal);
                if(Fltr::isEmail($itemVal))
                {

                }
                else
                {
                    $errorMsg = "Email ist falsch eingegeben.<br>";
                }
                break;

            case 'Strasse':
                $in = "strasse";

                $itemVal = trim($itemVal);

                if(Fltr::isStrasse($itemVal))
                {

                }
                else
                {
                    $errorMsg = "Beim Strassennamen sind keine Ziffern erlaubt. Bspl.: Musterstr. Allee.";
                }
                break;

            case 'Haus':
                $in = "strNr";

                $itemVal = str_replace(" ", "", $itemVal);
                if(Fltr::isHausNr($itemVal))
                {

                }
                else
                {
                    $errorMsg = "Beim Hausnummer sind nur Ziffern und Buchstaben erlaubt. Bspl.: 123a.<br>";
                }
                break;

            case 'Stadt':
                $in = "stadt";

                if(Fltr::isRowString($itemVal))
                {

                }
                else
                {
                    $errorMsg = "Bei der Stadt sind nur die Buchstaben erlaubt. Bspl.: Düsseldorf.<br>";
                }
                break;

            case 'PLZ':
                $in = "plz";

                $itemVal = str_replace(" ", "", $itemVal);
                if(Fltr::isPlz($itemVal))
                {

                }
                else
                {
                    $errorMsg = "PLZ besteht aus genau 5 ziffern. Bspl.: 40210.<br>";
                }
                break;

            case 'Geburtsland':
                $in = "geburtsland";

                $itemVal = str_replace(" ", "", $itemVal);
                if(Fltr::isRowString($itemVal))
                {

                }
                else
                {
                    $errorMsg = "Bei dem Geburtsland sind nur die Buchstaben erlaubt. Bspl.: Finland.<br>";
                }
                break;

            case 'Muttersprache':
                $in = "muttersprache";

                $itemVal = str_replace(" ", "", $itemVal);
                if(Fltr::isText($itemVal))
                {

                }
                elseif(empty($itemVal))
                {
                    $itemVal = "";
                }
                else
                {
                    $errorMsg = "Bei der Muttersprache sind nur die Buchstaben erlaubt. Bspl.: Nahua.<br>";
                }
                break;

            case 'istFotoErlaubt':
                $in = "istFotoErlaubt";

                $itemVal = str_replace(" ", "", $itemVal);
                if(Fltr::isRowString($itemVal))
                {
                    $itemVal = $itemVal === 'ja' ? 1 : 0;
                }
                else
                {
                    $errorMsg = "Bei der istFotoErlaubt sind nur die Buchstaben erlaubt. Bspl.: Nein.<br>";
                }
                break;

            case 'istDatenschutzAkzeptiert':
                $in = "istDatenschutzAkzeptiert";

                $itemVal = str_replace(" ", "", $itemVal);
                if(Fltr::isRowString($itemVal))
                {
                    $itemVal = $itemVal === 'ja' ? 1 : 0;
                }
                else
                {
                    $errorMsg = "Bei der istDatenschutzAkzeptiert sind nur die Buchstaben erlaubt. Bspl.: Nein.<br>";
                }
                break;

            case 'empfohlenId':
                $in = "empfohlenId";

                $itemVal = str_replace(" ", "", $itemVal);
                if( Fltr::isInt($itemVal) )
                {

                }
                elseif(empty($itemVal))
                {
                    $itemVal = "";
                }
                else
                {
                    $errorMsg = "Beim 'Empfohlen durch' sind nur die Ziffern erlaubt. Bspl.: 159.<br>";
                }
                break;

            default :
                $errorMsg = "kein passendes Item gefunden." . "<br>itemName=$itemName<br>itemValue=$itemVal";
                break;
        }

        if ($errorMsg) {
            return ['status' => 'error', 'message' => $errorMsg];
        }

        // check duplicates
        $duplicate = $this->checkDuplicateAddresses($kId, $in, $itemVal);

        if ($duplicate) {
            return ['status' => 'duplicate', 'duplicateData' => $duplicate];
        }

        $q = "UPDATE kunden SET $in = :itemVal WHERE kndId = $kId";


        $dbh = \MVC\DBFactory::getDBH();
        if(!$dbh)
        {
            return ['status' => 'error', 'message' => "kein DBH"];
        }

        try
        {
            $sth = $dbh->prepare($q);
            $res = $sth->execute(array(":itemVal" => $itemVal));

        } catch (Exception $ex) {
            print $ex;
            return ['status' => 'error', 'message' => "Fehler beim Update."];
        }

        $report = ($res>0) ?
            ['status' => 'ok', 'message' => "$itemName wurde erfolgreich geändert."]:
            ['status' => 'error', 'message' => "$itemName konnte nicht geändert werden. Wahrscheinlich, Fehler im Datenbank."];

        return $report;

    }

    /**
     * check if we already have the address in DB.
     * @return array with matched addresses.
     */
    private function checkDuplicateAddresses($kId, $itemName, $itemVal): array
    {
        $qClient = "SELECT *
        FROM kunden
        WHERE kndId=:kndId";
        $dbh = \MVC\DBFactory::getDBH();

        try {
            $sth = $dbh->prepare($qClient);
            $sth->execute([":kndId" => $kId]);
            $resClient = $sth->fetch(PDO::FETCH_ASSOC, 1);
        } catch (Exception $ex) {
            print $ex;
            return ['status' => 'error' , 'message' => "Fehler beim Update: find client data."];
        }

        if (empty($resClient)) {
            return [];
        }

        $qCheck = "SELECT *
        FROM kunden
        WHERE anrede=:anrede
        AND vorname=:vorname
        AND name=:name
        AND geburtsdatum=:geburtsdatum
        AND strasse=:strasse
        AND strNr=:strNr
        AND stadt=:stadt
        AND plz=:plz";

        // insert new item to current data
        $resClient[$itemName] = $itemVal;

        try {
            $sth = $dbh->prepare($qCheck);
            $sth->execute([
                ':anrede'       => $resClient['anrede'],
                ':vorname'      => $resClient['vorname'],
                ':name'         => $resClient['name'],
                ':geburtsdatum' => $resClient['geburtsdatum'],
                ':strasse'      => $resClient['strasse'],
                ':strNr'        => $resClient['strNr'],
                ':stadt'        => $resClient['stadt'],
                ':plz'          => $resClient['plz'],
            ]);

            $resCheck = $sth->fetch(PDO::FETCH_ASSOC, 1);

        } catch (Exception $ex) {
            print $ex;
            return [];
        }

        if (!$resCheck) {
            $resCheck = [];
        }

        return $resCheck;
    }
}
