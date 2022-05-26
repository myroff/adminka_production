<?php
function getSecurityArray()
{
	$securityArray = array(
		//'/second/' => array('Group' => 'Admins', 'User' => 'admin'),
		'/admin' => array('group' => array('Administrator','Editor'), 'login' => 'admin')
	);
	
	return $securityArray;
}
