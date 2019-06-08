<?php
namespace Tools;
//error_reporting( E_ALL );
class DateTools {
	public static function getDatesOfWeekdaysInMonthYear($weeksDay, $format="%d.%m.%Y", $month = false, $mnthOffset=3,$year = false){
		setlocale(LC_TIME, 'de_DE.UTF-8');
		
		if( $weeksDay<1 AND $weeksDay>7 AND !is_int($weeksDay) ){
			return false;
		}
		
		if(!$month){
			$month = date('m');
		}
		elseif( $month>0 AND $month<13 AND is_int($month) ){
			$month = (int)$month;
		}
		else{
			return false;
		}
		
		if(!$year){
			$year = date('Y');
		}
		elseif(preg_match("/\d\d\d\d/", $year)){
			$year = (int)$year;
		}
		else{
			return false;
		}
		
		
		
		$dates = array();
		for($m=$month,$mn=0; $mn<$mnthOffset; $mn++, $m++){
			$number = cal_days_in_month(CAL_GREGORIAN, $m, $year);
			for($i=1; $i<=$number; $i++){
				$d = date('N', strtotime("$i.$month.$year"));
				if( in_array($d, $weeksDay)  ){
					$dates[] = strftime($format, mktime(0, 0, 0, $m, $i, $year));//
				}
			}
		}
		
		return $dates;
	}
}
