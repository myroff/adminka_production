<?php
namespace Warteliste;

error_reporting(E_ALL); ini_set('display_errors', '1');

class Warteliste {
	public function showList(){
		$kurse = $this->search_kurse();
		
		include_once BASIS_DIR.'/Templates/Warteliste/Warteliste.tmpl.php';
		return;
	}
	
	public function search_kurse($sArr=FALSE){
		$q = "";
		if($sArr){
			$q = "SELECT * FROM wl_kurse WHERE title LIKE ':kurse%'";
		}
		else{
			$q = "SELECT * FROM wl_kurse LIMIT 0,50";
		}
	}
}
