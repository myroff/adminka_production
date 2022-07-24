<?php
namespace Kunde;
use PDO as PDO;
use \Tools\TmplTools as TmplTls;
use \Kurse\Seasons as Seasons;

class Kunde
{
    private $isCashImg = array(
        0 => "lastschrift.001.png",
        1 => "euro_cash.jpg",
        2 => "bamf.002.jpeg",
        3 => "euro_cash.jpg",
        4 => "euro_cash.jpg",
        5 => "ueberweisung.logo.png",
        );

    private function filterPostData()
    {
        $sArr = array();
        $sArr[':vorname']     = empty($_GET['vorname'])     ? '' : $_GET['vorname'];
        $sArr[':name']        = empty($_GET['name'])        ? '' : $_GET['name'];
        $sArr[':kur_name']    = empty($_GET['kur_name'])    ? '' : $_GET['kur_name'];
        $sArr[':lehrId']      = empty($_GET['s_lehrId'])    ? '' : $_GET['s_lehrId'];
        $sArr[':wochentag']   = empty($_GET['wochentag'])   ? '' : $_GET['wochentag'];
        $sArr[':zeit']        = empty($_GET['zeit'])        ? '' : $_GET['zeit'];
        $sArr[':showIntegra'] = empty($_GET['showIntegra']) ? '' : $_GET['showIntegra'];
        $sArr[':abgemeldet']  = empty($_GET['abgemeldet'])  ? '' : $_GET['abgemeldet'];
        $sArr[':season']      = empty($_GET['s_season'])    ? (Seasons::getActiveSeasonId() ?? '') : $_GET['s_season'];

        return $sArr;
    }

    public function showList()
    {
        $sArr = $this->filterPostData();

        $vars['data']       = $this->searchDates($sArr);

        $vars['pageName']   = "Kundenliste";
        $vars['sArr']       = $sArr;
        $vars['s_lehrId']   = TmplTls::getLehrerSelector("s_lehrId", "s_lehrId", $sArr[':lehrId'], "Lehrer", 1);
        $vars['wochentag']  = TmplTls::getWeekdaySelector("wochentag", "wochentag", $sArr[':wochentag'], "Tag", 1);
        $vars['zeit']       = TmplTls::getTimeSelector("zeit", "zeit", $sArr[':zeit'], "Zeit");
        $vars['s_season']   = TmplTls::getSeasonsSelector("s_season", "s_season", $sArr[':season'], "Season", 1);
        #$vars['isCashImg'] = $this->isCashImg;
        /*
        $options = []; #array('cache' => TWIG_CACHE_DIR);
        $loader = new \Twig\Loader\FilesystemLoader(TWIG_TEMPLATE_DIR);
        $twig = new \Twig\Environment($loader, $options);
        $twigTmpl = $twig->load('/Kunde/KundenListe.twig');
        echo $twigTmpl->render($vars);
        */
        $viewer = new \Viewer\Viewer();
        $viewer->display('/Kunde/KundenListe.twig', $vars);
    }
    public function printList()
    {
        $sArr = array();
        $sArr = $this->filterPostData();

        $selectArr = "";
        $selectArr[] = isset($_POST['print_kndnr']) ? "k.kundenNummer as 'Knd.-Nr.'" : '';
        $selectArr[] = isset($_POST['print_anrede']) ? "k.anrede as 'Anrede'" : '';
        $selectArr[] = isset($_POST['print_vorname']) ? "k.vorname as 'Vorname'" : '';
        $selectArr[] = isset($_POST['print_name']) ? "k.name as 'Name'": '';
        $selectArr[] = isset($_POST['print_alter']) ? "TIMESTAMPDIFF(YEAR,k.geburtsdatum,CURDATE()) as 'Alter'": '';
        $selectArr[] = isset($_POST['print_geburtsdatum']) ? "k.geburtsdatum as 'Geburtsdatum'" : '';
        $selectArr[] = isset($_POST['print_telefon']) ? "k.telefon as 'Telefon'" : '';
        $selectArr[] = isset($_POST['print_handy']) ? "k.handy as 'Handy'" : '';
        $selectArr[] = isset($_POST['print_email']) ? "k.email as 'Email'" : '';

        $arrayToPrint = $this->searchDates($sArr, $selectArr);
//add title over table
        if( isset($_POST['print_titel']) && !empty($_POST['print_titel']) ){
            $arrayToPrint['print_titel'] = $_POST['print_titel'];
        }

        include_once BASIS_DIR.'/Templates/PrintArray.tmpl.php';
        return;
    }
    private function searchDates($searchArr, $selectArr=false)
    {

        $dbh = \MVC\DBFactory::getDBH();
        if(!$dbh)
        {
            return FALSE;
        }
        //$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, 10);
        $where = "";

    //delete empty entries
        $searchArr = array_filter($searchArr);

        if(isset($searchArr[':vorname']))
        {
            $where .= " k.vorname LIKE :vorname AND";
            $searchArr[':vorname'] .= '%';
        }
        if(isset($searchArr[':name']))
        {
            $where .= " k.name LIKE :name AND";
            $searchArr[':name'] .= '%';
        }
        if(isset($searchArr[':kur_name']))
        {
            $where .= " kr.kurName LIKE :kur_name AND";
            $searchArr[':kur_name'] .= '%';
        }
        if(isset($searchArr[':lehrId']))
        {
            $where .= " l.lehrId = :lehrId AND";
        }
        if(isset($searchArr[':wochentag']))
        {
            $where .= " st.wochentag = :wochentag AND";
        }
        if(isset($searchArr[':zeit']))
        {
            $where .= " TIME(:zeit) BETWEEN st.anfang AND st.ende AND";
        }

        if(isset($searchArr[':showIntegra']))//showIntegra = "i"
        {
            unset($searchArr[':showIntegra']);
        } else {
            $where .= " k.kundenNummer NOT LIKE 'i%' AND";
        }

        if(isset($searchArr[':abgemeldet']))//showIntegra = "i"
        {
            unset($searchArr[':abgemeldet']);
        } else {
            // das selektiert nur die Kunden mit akutellen Kursen. Wir wolen Kunden in Season sehen.
            //$where .= " NOW() BETWEEN kndKurse.von AND kndKurse.bis AND";
        }

        $khkWhere = "";
        $seasonId = 0;

        if (isset($searchArr[':season'])) {
            $where   .= " kndKurse.season_id=:season AND";
            $khkWhere = "WHERE season_id = :season";
            $seasonId = (int)$searchArr[':season'];
        }

        $where = substr($where, 0, -4);

        $select = "";
        if($selectArr){
            $selectArr = array_filter($selectArr);
            $select = implode(",", $selectArr);
        }
        else{
            $select =
"khk.kndId as kndIdInKhk,k.*, khk.courses, pd.payment_id, pm.logo_file, TIMESTAMPDIFF(YEAR,k.geburtsdatum,CURDATE()) as 'alter'";
        }

        $q =
"SELECT ".$select."
FROM kunden as k
LEFT JOIN kundehatkurse as kndKurse USING(kndId)
LEFT JOIN kurse as kr USING(kurId)
LEFT JOIN lehrer as l USING(lehrId)
LEFT JOIN payment_data as pd USING(kndId)
LEFT JOIN payment_methods as pm USING(payment_id)
LEFT JOIN
(
    SELECT kndId, GROUP_CONCAT(kurId SEPARATOR '|') as courses, season_id, von, bis
    FROM kundehatkurse
    $khkWhere
    GROUP BY kndId
) as khk USING (kndId)
LEFT JOIN
(
    SELECT *
    FROM stundenplan
    $khkWhere
    GROUP BY kurId
)
as st USING (kurId)
";

        $q .= empty($where) ? '' : " WHERE " . $where;
        $q .= " GROUP BY k.kndId";
        //$q .= empty($where) ? '' : " HAVING " . $where;
        $q .= " ORDER BY LENGTH(k.kundenNummer) DESC, k.kundenNummer DESC";

        try
        {
            $dbh->exec("SET SESSION group_concat_max_len = 10000;");
            $sth = $dbh->prepare($q);
            $sth->execute($searchArr);
            $rs = $sth->fetchAll(PDO::FETCH_ASSOC);

            $kursData = [];

            if (!empty($rs)) {



                foreach ($rs as $key => $val) {

                    if (!empty($val['courses'])) {

                        $courseIds = explode('|', $val['courses']);
                        $rs[$key]['courseIds'] = $courseIds;

                        $kursModel = new \Kurse\KurseModel();
                        $stundenplanModel = new \Stundenplan\StundenplanModel();


                        foreach ($courseIds as $cid) {

                            if (!isset($kursData[$cid])) {

                                $kursData[$cid] = $kursModel->getSeasonalCourseData($cid, $seasonId);
                                $kursData[$cid]['stundenplan'] = $stundenplanModel->getStundenplanToKurId($cid, $seasonId);
                            }
                        }

                    }

                }
            }
            return ['clients' => $rs, 'courses' => $kursData];

        } catch (Exception $ex) {
            //print $ex;
            return FALSE;
        }
    }
}
