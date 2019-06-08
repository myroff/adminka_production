<?php
include_once 'routing.php';

$routingList = getRoutingList();

$requestUri = str_replace(BASIS_URL, '', $_SERVER['REQUEST_URI']);
$requestUri = trim($requestUri, '/');

//routing Process
if($requestUri !== '')
{
	$hasParameterAt = strpos($requestUri, '?');
	if($hasParameterAt !== FALSE)
	{
		$requestUri = substr($requestUri, 0, $hasParameterAt);
	}
	//Request URL Array
	$RUArray = explode('/', $requestUri);
	$RULength = count($RUArray);
	
	foreach ($routingList as $key => $value)
	{
		$key = trim($key, '/');
		$keyArray = explode('/', $key);
		
		if($RULength == count($keyArray))
		{
			// $t -> Anzahl der treffer
			$t = 0;
			
			//$argumentArray -> da werden gefundende in URL "Variablen" gespeichert
			$argumentArray = array();
			
			for($i=0; $i<$RULength; $i++)
			{
				if($RUArray[$i] === $keyArray[$i])
					++$t;
				elseif($keyArray[$i][0] === '$')
				{
					$argumentArray[$keyArray[$i]] = $RUArray[$i];
					++$t;
				}
				else
					break;
			}
//Zugriffsrechte überprüfen
			if($t === $RULength)
			{
				//$validation = new \Authentication\Validation();
				require_once 'Authentication.php';
				$validation = new MVC\Authentication();
				if(!$validation->isValid($requestUri))//BASIS_URL
				{
					include_once BASIS_DIR.'/BLogic/Login/LoginController.php';
					
					$Login = new LoginController();
					$Login->loginForm('/'.$requestUri);
					
					return false;
				}
				
				$call = explode(':', $value);
				include_once BASIS_DIR.'/BLogic/'.$call[0].'/'.$call[1].'.php';
				
				if(empty($argumentArray))
				{
					$nc = $call[0].'\\'.$call[1];
					$tmpObj = new $nc();
					//$tmpFnct = $call[2];

					$tmpObj->{$call[2]}();
					return true;
				}
				else
				{
					$nc = $call[0].'\\'.$call[1];
					$tmpObj = new $nc();
					#$tmpObj->{$call[2]}($argumentArray);
					call_user_func_array(array($tmpObj, $call[2]), $argumentArray);
					//call_user_func_array(array($call[0].'\\'.$call[1], $call[2]), $argumentArray);
					//call_user_func_array(array($tmpObj, $call[2]), $argumentArray);
					return true;
				}
				
				break;
			}
			else
			{
				
			}
		}
	}
	//Wenn die Seite NICHT gefunden sei
	echo "<h2>Seite nicht gefunden.</h2>";
	return;
}
else
{
	//Startseite
	include_once BASIS_DIR.'/Templates/startPage.tmpl.php';
	return;
}
