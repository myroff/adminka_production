<?php
namespace MVC;

class Authentication
{
    public function isValid($link)
    {
        include_once 'security.php';
        $securityArray = getSecurityArray();

        if (empty($securityArray)) {
            return true;
        }

        $requestUri = trim($link, '/');
        $requestUriArray = explode('/',$requestUri);
        $requestUriArrayLength = count($requestUriArray);
        $ruleIsFounded = false;
        $currentTmpUser = false;
        $currentUsersGroups = array();

        if(isset($_COOKIE['uTmpK']) && isset($_COOKIE['uTmpId']))
        {
            $currentTmpUser = $this->getTmpUserFrom_uTmpId($_COOKIE['uTmpId']);

            if((int)$currentTmpUser['mtId']!==(int)$_COOKIE['uTmpId'] || $currentTmpUser['tmpPswd']!==$_COOKIE['uTmpK'])
            {
                return FALSE;
            }
            $currentUsersGroups = $this->getTmpUsersGroup($currentTmpUser['mtId']);
        }

        //key is URI, value is array with group and user
        foreach ($securityArray as $key => $value) {
            $key = trim($key, '/');
            $keyArray = explode('/', $key);
            $keyArrayLength = count($keyArray);
            $t=0;

            if($requestUriArrayLength >= $keyArrayLength)
            {
                $t = $requestUriArrayLength - $keyArrayLength;

                for($i=0; $i<$keyArrayLength; $i++)
                {
                    if($requestUriArray[$i] === $keyArray[$i])
                    {
                        $t++;
                    }
                    elseif($keyArray[$i][0] === '$')
                    {
                        $t++;
                    }
                    elseif($keyArray[$i][0] === '*')
                    {
                        $t++;
                    }
                    else
                    {
                        break;
                    }
                }
                if($t === $requestUriArrayLength)
                {
                    $ruleIsFounded = true;

                    if($currentTmpUser)
                    {
                        //get group and user
                        $user = isset($value['login']) ? $value['login'] : false;
                        $group = isset($value['group']) ? $value['group'] : false;

                    //if group does match
                        if($group!==false)
                        {
                            foreach($group as $g)
                            {
                                if(in_array($g, $currentUsersGroups))
                                {
                                    /*
                                    if ($user !== false) {
                                        if ($currentTmpUser['login'] === $user)
                                            return true;
                                        else
                                            continue;
                                    }
                                    else {
                                        return true;
                                    }
                                    */
                                   return true;
                                }
                            }
                        }
                        //if user does match
                        //echo '<br>user='.$user;
                        if($user!==false)
                        {
                            if($currentTmpUser['login'] === $user)
                                return true;
                        }
                    }
                    //if user is NOT logged in
                    return FALSE;
                }
            }
        }
        //if  there is no security rules for this URI get - return TRUE
        //if there is some rules, but current user doesn't match - return false
        if($ruleIsFounded)
            return false;
        else
            return true;
    }

    private function getTmpUserFrom_uTmpId($cookiesTmpId=FALSE)
    {
        if(!$cookiesTmpId)
        {
            return FALSE;
        }

        $dbh = DBFactory::getDBH();
        if(!$dbh){
            return false;
        }

        try
        {
            $q = "SELECT l.login, t.mtId, t.tmpPswd"
                ." FROM tmplogin AS t LEFT JOIN mtblogin as l USING(mtId)"
                ." WHERE t.mtId=:mtId";
            $sth = $dbh->prepare($q);
            $sth->execute(array(':mtId'=>$cookiesTmpId));
            $res = $sth->fetch(\PDO::FETCH_ASSOC);

            if(count($res)>0)
            {
                return $res;
            }
            else
            {
                return FALSE;
            }
        } catch (Exception $ex)
        {
            return false;
        }
    }

    private function getTmpUsersGroup($uId = FALSE)
    {
        if($uId === FALSE)
        {
            return FALSE;
        }

        $dbh = DBFactory::getDBH();
        if(!$dbh){
            return false;
        }

        try
        {
            $q = "SELECT g.grpName"
                ." FROM mtbingrp AS m LEFT JOIN groups AS g USING(grpId)"
                ." WHERE m.mtId=:mtId";

            $sth = $dbh->prepare($q);
            $sth->execute(array(':mtId'=>$uId));
            $res = $sth->fetchAll(\PDO::FETCH_COLUMN);//FETCH_ASSOC

            if(count($res)>0)
            {
                return $res;
            }else
            {
                return FALSE;
            }
        } catch (Exception $ex)
        {
            print $e;
            return false;
        }
    }

}
