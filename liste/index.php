<?php
/**
 * SMS script, 
 * return the list of commands
 */
header('Content-Type: text/html; charset=utf-8');


$g = glob("../*");

$cmds=Array();
foreach($g as $k=>$f)
{
	//echo "<li>$f";
	if( is_dir( $f ) )
	{
		$cmds[]=basename($f);
	}
}

die(implode(" ", $cmds));