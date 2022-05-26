<?php
namespace Login;
use PDO as PDO;

class LoginController
{
    public function loginForm($rLink="")
    {
        if(!empty($_POST['login']) AND !empty($_POST['pswd']))
        {
            $delete = array('<','>');
            $login = str_replace($delete, '', $_POST['login']);
            $pswd = str_replace($delete, '', $_POST['pswd']);
            //echo $rLink;
            $isLogingIn = $this->logUserIn($login, $pswd);

            if($isLogingIn)
            {
                header('Location: '.BASIS_URL.$rLink);
                exit();
            }
            else
            {
                $this->showLoginForm("Benutzername oder Password stimmen nicht.", $rLink);
                return FALSE;
            }
        }
        else
        {
            $this->showLoginForm("", $rLink);
        }

        return;
    }

    private function logUserIn($login, $pswd)
    {
        //echo 'login = '.$login.'<br>';
        //echo 'pswd = '.$pswd.'<br>';
        require_once BASIS_DIR.'/MVC/DBFactory.php';
        $dbh = \MVC\DBFactory::getDBH();
        if (!$dbh) {
            echo "ho dbh<br>";
            return false;
        }

        try
        {
            $q = "SELECT *"
                ." FROM mtblogin"
                ." WHERE login=:login AND istAktiv=TRUE";

            $sth = $dbh->prepare($q);
            $sth->execute(array(':login'=>$login));
            $res = $sth->fetch(PDO::FETCH_ASSOC);

//if the login is founded
            if(count($res)>0) {

                $hashPswd = hash('sha512', $res['salt'].$pswd);

                if($hashPswd === $res['pswd'])
                {
                    $tmpKeyHash = "";
                    $tmpKeyIsOk = FALSE;
                    while (!$tmpKeyIsOk)
                    {
                        $tmpKeyHash = md5(mt_rand());
                        $sth1 = $dbh->query("SELECT * FROM tmplogin WHERE tmpPswd='$tmpKeyHash'");
                        $tmpKeyIsOk = $sth1->fetchColumn() > 0 ? FALSE : TRUE;
                    }
                    $dbh->exec("INSERT INTO `tmplogin`(`mtId`, `tmpPswd`) VALUES ('".$res['mtId']."','".$tmpKeyHash."')ON DUPLICATE KEY UPDATE tmpPswd='".$tmpKeyHash."'");
                    setcookie('uTmpId', $res['mtId'], time()+36000, BASIS_URL ?: '/');//
                    setcookie('uTmpK', $tmpKeyHash, time()+36000, BASIS_URL ?: '/');//

                    return TRUE;
                }
                else
                {
                    return FALSE;
                }
            }
            else
            {
                echo 'kein res<br>';
                return FALSE;
            }
        } catch (Exception $ex)
        {
            print $ex;
            return false;
        }
    }

    private function showLoginForm($text, $returnLink)
    {
        $content = "";
        if (!empty($text)) {
            $content .= '<div>'.$text."</div>";
        }
        $content .= "
        <form id='login_form' name='login' method='post'>
            <label>Benutzername</label><br>
            <input name='login' type='text'/><br>
            <br>
            <label>Passwort</label><br>
            <input name='pswd' type='password'/><br>
            <br>
            <input type='submit' value='Anmelden' class='login'>
        </form>";

        include_once BASIS_DIR.'/Templates/Login.tmpl.php';
        return;
    }

    public function logout($link=BASIS_URL)
    {
        if ($_COOKIE['uTmpK']) {
            setcookie('uTmpK', '', time()-3600, BASIS_URL);
        }
        header('Location: '.$link);
        exit();
    }
}
