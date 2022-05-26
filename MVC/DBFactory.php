<?php
namespace MVC;
use PDO as PDO;

class DBFactory
{
    private static $dbf = NULL;

    private function __construct()
    {

    }

    public function __clone()
    {

    }

    public static function getDBH()
    {
        if (!self::$dbf) {

            try {
                self::$dbf = new PDO('mysql:host='.DB_HOST
                                        .';port='.DB_PORT
                                        .';dbname='.DB_NAME
                                        .';charset=UTF8'
                                    ,DB_USER
                                    ,DB_PSWD
                                );

                self::$dbf->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch (Exception $e) {
                print $e;
                return false;
            }
        }
        return self::$dbf;
    }
}
