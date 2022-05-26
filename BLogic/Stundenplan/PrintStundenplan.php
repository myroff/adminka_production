<?php
namespace Stundenplan;
/*use PDO as PDO;

use MVC\DBFactory as DBFactory;
*/
require_once 'Stundenplan.php';
use Stundenplan\Stundenplan as Stundenplan;

class PrintStundenplan extends Stundenplan {
	public function loadTamplate($res, $raum, $sArr){
		include_once  BASIS_DIR.'/Templates/Stundenplan/PrintStundenplanListe.tmpl.php';
	}
}