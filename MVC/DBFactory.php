<?php
namespace MVC;
use PDO as PDO;

class DBFactory
{
	private static $dbf = NULL;
	private static $host = "localhost";
	private static $db = "swiff.crm.production";
	private static $username = "swiff_root";
	private static $password = "4eburashka";

	private function __construct()
	{
		
	}
	
	public function __clone()
    {
		
    }

	public static function getDBH()
	{
		//$dbf = false;
		if(!self::$dbf)
		{
			try
			{
				//$dbf = new PDO('mysql:host='.$this->host.';dbname='.$this->db.';charset=UTF8', $this->username, $this->password, array(PDO::ATTR_PERSISTENT => true));
				self::$dbf = new PDO('mysql:host='
				.self::$host
				.';dbname='
				.self::$db
				.';charset=UTF8'
				,self::$username
				,self::$password
				);
				self::$dbf->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}catch (Exception $e)
			{
				print $e;
				return false;
			}
		}
		return self::$dbf;
	}
}
