<?php
error_reporting(E_ERROR);

/*
$srv = 'PIDSANSVSQL001';
$uid = 'sa';
$pwd = 'Softland2015';
		

*/

$srv = 'ITC-0013V10\SOSQL2014';
$uid = 'Disofi';
$pwd = 'diS-4$67Fi';
//'dsparam'	=>	'[DSBSN].[dbo]',

$bd = array
	(
	'dsparam' =>'[DSBSN].[dbo]',
	'softland'	=>	'[BSNMEDICAL].[softland]'
	);
	
$connectionInfo = array
	(
	'UID' => $uid,
	'PWD' => $pwd,
	'CharacterSet' => 'UTF-8',
	'Database' => 'DSBSN'
	);
$conn = sqlsrv_connect($srv, $connectionInfo);
if( $conn === false )
	{
	echo 'No es posible conectarse al servidor :<br />';
	die(print_r(sqlsrv_errors(), true));
	}
ini_set('max_execution_time', 300);
?>
