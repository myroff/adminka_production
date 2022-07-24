<?php
namespace Viewer\TwigExtension;

/** add my custom functions to twig templates */

class FunctionLoader extends \Twig\Extension\AbstractExtension
{
    public function getFunctions()
    {
        return [
            new \Twig\TwigFunction('getAdminMenu',       [$this, 'getAdminMenu'], ['is_safe' => array('all')]),
            new \Twig\TwigFunction('printSqlKursTermin', [$this, 'printSqlKursTermin'], ['is_safe' => array('all')]),
            new \Twig\TwigFunction('indxToWeekday',	     [$this, 'indxToWeekday'], ['is_safe' => array('all')]),
            new \Twig\TwigFunction('getWeekdayFromInt',  [$this, 'getWeekdayFromInt'], ['is_safe' => array('all')]),
            new \Twig\TwigFunction('printSqlTermin',     [$this, 'printSqlTermin'], ['is_safe' => array('all')]),
            new \Twig\TwigFunction('sqlTimeToStr',       [$this, 'sqlTimeToStr'], ['is_safe' => array('all')]),
            new \Twig\TwigFunction('getLehrerSelector',  [$this, 'getLehrerSelector'], ['is_safe' => array('all')]),
            new \Twig\TwigFunction('getSeasonsSelector', [$this, 'getSeasonsSelector'], ['is_safe' => array('all')]),
            new \Twig\TwigFunction('getWeekdaySelector', [$this, 'getWeekdaySelector'], ['is_safe' => array('all')]),
        ];
    }

    /**
     * generate main menu.
     */
    public function getAdminMenu()
    {
        #require_once BASIS_DIR.'/Templates/Menu.class.php';
        \TemplateTools\Menu::adminMenu();
    }

    public function printSqlKursTermin($sqlStr)
    {
        $out = "";
        $sql = "[".$sqlStr."]";

        $kurse = json_decode($sql,true);

        foreach ($kurse as $ind=>$k)
        {
            $color = strtotime("now") < strtotime($k['von']) ? "Dark yellow1" : "green";
            $color = strtotime("now") < strtotime($k['bis']) ? "green" : "red";
            $out .= "<i>".$k['vorname']." ".$k['name']."</i><br>";
            $out .= "<b>".$k['kurName']."</b><br>"
                    . "<b style='color:$color;'>[".date('d.m.y' ,strtotime($k['von']))."-".date('d.m.y', strtotime($k['bis']))."]</b>";
            foreach ($k['termin'] as $iind=>$t)
            {
                $out .= "<br>";
                $out .= $this->indxToWeekday(trim($t['wochentag']));
                $out .= " ".$t['time'];
            }
            #$out .= "<br><br>";
            //.self::indxToWeekday(trim($t[]['wochentag']));
        }
        return $out;
    }

    public function indxToWeekday($ind)
    {
        if(empty($ind)) return '';

        $week = array(1=>"Mo.", 2=>"Di.", 3=>"Mi.", 4=>"Do.",
            5=>"Fr.", 6=>"Sa.", 7=>"So.");
        return $week[$ind];
    }

    public function getWeekdayFromInt($n)
    {
        $week = array(1=>"Montag", 2=>"Dienstag", 3=>"Mitwoch", 4=>"Donnerstag", 5=>"Freitag", 6=>"Samstag", 7=>"Sonntag");

        return $week[$n];
    }

    public function printSqlTermin($sqlStr)
    {
        $out = "";
        $sql = "[".$sqlStr."]";
        $termin = json_decode($sql,true);

        foreach ($termin as $i=>$t)
        {
            #$out .= "<br>";
            $out .= self::indxToWeekday(trim($t['wochentag']));
            $out .= " ".$t['time'];
        }

        return $out;
    }

    public function sqlTimeToStr($sqlStr)
    {
        return \Tools\Filter::printSqlTermin($sqlStr);
    }

    public function getLehrerSelector($selectorName="", $selectorId="", $selectedValue="", $label="", $meterializeOn=FALSE)
    {
        return \Tools\TmplTools::getLehrerSelector($selectorName, $selectorId, $selectedValue, $label, $meterializeOn);
    }

    public function getSeasonsSelector($selectorName="", $selectorId="", $selectedValue="", $label="", $meterializeOn=FALSE)
    {
        return \Tools\TmplTools::getSeasonsSelector($selectorName, $selectorId, $selectedValue, $label, $meterializeOn);
    }

    public function getWeekdaySelector($selectorName="", $selectorId="", $selectedValue="", $label="", $meterializeOn=FALSE)
    {
        return \Tools\TmplTools::getWeekdaySelector($selectorName, $selectorId, $selectedValue, $label, $meterializeOn);
    }
}