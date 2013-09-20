<?php //-->

/*
* Load Hosts File Config
* Return valid hosts for the applications
*
*/
$hosts = include '../../config/host.php';

/*
* Check what Application to load depending on hostname
*
*
*/
switch($_SERVER['HTTP_HOST']) {
	
	/*
	* Load Front Application
	* 
	*/
	case $hosts['app']['dev']:
	case $hosts['app']['prod']:
		include('app.php');
		break;
		
	/*
	* Load Master Application
	*
	*/
	case $hosts['master']['dev']:
	case $hosts['master']['prod']:
		include('app.php');
		break;
		
	/*
	* Print Message if the hostname is not found in the hosts file conguration
	*
	*/
	default:
		echo '<p style="color: #a3a3a3; text-align: center; ">No App Selected.</p>';	
}
