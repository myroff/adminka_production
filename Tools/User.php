<?php
namespace Tools;
use PDO as PDO;

class User
{
	public static function getCurrentUserId()
	{
		if(isset($_COOKIE['uTmpId']))
		{
			$currentUserId = $_COOKIE['uTmpId'];
			return $currentUserId;
		}
		else
			return false;
	}//end public static getCurrentUser()
	
	public static function getCurrentUserInfo()
	{
		$uId = self::getCurrentUserId();
		
		if(!$uId) return FALSE;
		
		require_once BASIS_DIR.'/MVC/DBFactory.php';
        $dbh = \MVC\DBFactory::getDBH();
        if(!$dbh)
        {
            echo "no Connection to DB";
            return FALSE;
        }
		
		$res = array();
		$q = "SELECT * FROM mitarbeiter WHERE mtId=:mtId";
		
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute(array(':mtId' => $uId));
			$res = $sth->fetch(PDO::FETCH_ASSOC, 1);
			
		} catch (Exception $ex) {
			print($ex);
			return FALSE;
		}
		
		return $res;
	}
	
	public static function getUserByPswd()
	{
		
	}
	
	public static function getUserLogin()
	{
		$tmpKey;
		if(isset($_COOKIE['uTmpK']))
		{
			$tmpKey = $_COOKIE['uTmpK'];
		}
		else
			return false;
		
		require_once BASIS_DIR.'/MVC/DBFactory.php';
        $dbh = \MVC\DBFactory::getDBH();
        if(!$dbh)
        {
            return FALSE;
        }
		
		$q = "SELECT login FROM mtblogin LEFT JOIN tmplogin USING(mtId) WHERE tmpPswd=:tmpPswd";
		$res = array();
		
		try{
			$sth = $dbh->prepare($q);
			$sth->execute(array(':tmpPswd' => $tmpKey));
			$res = $sth->fetch(PDO::FETCH_ASSOC, 1);
		}catch (Exception $ex) {
			return FALSE;
		}
		
		return $res['login'];
	}
	
	public static function getUserGroup()
	{
		$tmpKey;
		if(isset($_COOKIE['uTmpK']))
		{
			$tmpKey = $_COOKIE['uTmpK'];
		}
		else
			return false;
		
		require_once BASIS_DIR.'/MVC/DBFactory.php';
        $dbh = \MVC\DBFactory::getDBH();
        if(!$dbh)
        {
            return FALSE;
        }
		
		$q = "SELECT grpName FROM groups LEFT JOIN mtbingrp USING(grpId) LEFT JOIN mtblogin USING(mtId) LEFT JOIN tmplogin USING(mtId) WHERE tmpPswd=:tmpPswd";
		$res = array();
		
		try{
			$sth = $dbh->prepare($q);
			$sth->execute(array(':tmpPswd' => $tmpKey));
			$res = $sth->fetchAll(PDO::FETCH_ASSOC);
		}catch (Exception $ex) {
			return FALSE;
		}
		$gruppen = [];
		foreach($res as $r){
			$gruppen[] = $r['grpName'];
		}
		return $gruppen;
	}
}
