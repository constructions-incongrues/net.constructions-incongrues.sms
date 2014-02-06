<?php
/**
 * SMS script
 * Debug
 */
header('Content-Type: text/html; charset=utf-8');

$out = print_r( $_GET , true );

die( $out );