<?php
namespace Admin;

class AdminMain
{
	public function mainPage()
	{
		header("Location:".BASIS_URL."/admin/kundenListe");
		exit();
		/*
		include_once BASIS_DIR.'/Templates/KundenListe.tmpl.php';
		return;
		 * 
		 */
	}
}
