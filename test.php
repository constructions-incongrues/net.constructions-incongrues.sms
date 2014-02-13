<?php
/**
 * Test sms incongru install
 */
require "./class.curl.php";
require "./class.smspi.php";
include __DIR__."/config.php";


$db = new mysqli( $dbhost, $dbuser, $dbpass , $dbname );
if(!$db)die("No database connection");


// Perform db check
$smsCheck = new smsCheck( $db );



class smsCheck {
	
	var $db=null;//db connection

	function __construct( $dbcon='' ) {
		if (!$dbcon) {
			$this->error("Error no db connection\n");
		} else {
			$this->db = $dbcon;
		}

		$this->checkTables();
		
		echo "done\n";
	}


	/**
	 * Check the existence of the necessary tables:
	 * -inbox
	 * -phonebook
	 * -log_errors
	 * -services
	 * @return bool success
	 */
	function checkTables() {
		
		//echo "checkTables()\n";

		$check = Array('inbox','phonebook','log_errors','services');

		//get list of tables
		$sql = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA LIKE 'sms';";
		$q = $this->db->query( $sql ) or $this->error( $this->db->error );

		while( $r = $q->fetch_assoc() )
		{
			$tables[]=$r['TABLE_NAME'];
		}

		//check tables
		$missing = Array();
		foreach($check as $k=>$table){
			if( !in_array( $table, $tables))$missing[]=$table;
		}
		
		if(count($missing)){
			echo count($missing) . " missing table(s)\n";
			print_r($missing);
			return false;
		}
		return true;
	}


	function error($e,$exit=0) {
		echo $e."\n";
		if($exit == 1){ exit; }
	}

}



