<?php
namespace Payment;

require_once BASIS_DIR.'/BLogic/Payment/PaymentApi.php';
use Payment\PaymentApi as PaymentApi;

//twig
require_once BASIS_DIR . '/Vendor/autoload.php';

class PaymentInterface 
{
	
	public $css = <<<CSS
#paymentInterface-table .logo-image {max-width: 100px;}
CSS;


	public function editPaymentsInterface()
	{
		$vars = array();
		
		$paymentApi = new PaymentApi();
		
		$vars['pageName'] = "Zahlungsmethoden.";
		$vars['customStyleContent'] = $this->css;
		$vars['payments'] = $paymentApi->getPaymentMethods();
		
		$options = []; #array('cache' => TWIG_CACHE_DIR);
		$loader = new \Twig_Loader_Filesystem(TWIG_TEMPLATE_DIR);
		$twig = new \Twig_Environment($loader, $options);
		$twigTmpl = $twig->load('/Payment/PaymentUserInterface.twig');
		echo $twigTmpl->render($vars);
	}
}
