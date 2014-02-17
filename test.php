<?php
/**
 * Test sms incongru install
 */
require "./class.curl.php";
require "./class.smspi.php";

$config = json_decode( file_get_contents('./config.json') );




// Perform db check
$smsCheck = new smsCheck( $config );



class smsCheck {
	
	var $config = null;
	var $db = null;//db connection

	function __construct( $conf='' ) {
		if ( !$conf ) {
			$this->error("Error no config data\n");
		} else {
			
			$this->config=$conf;	
		}

		echo "SMS Test config.json\n";
		echo "-------------------------\n";
			
		echo "Gammu detection:\n";
		if( $this->gammuDetect() ){
			echo "Ok : " . $this->config->gammu . "\n";
		}else{
			echo "Error : gammu not found\n";
		}

		//echo "Modem\n";
		if( $this->checkModem() ){
			echo "Ok : Modem is writeable\n";
		}else{
			if(!is_file( $this->config->modem ) )die("Error : Modem '" . $this->config->modem . "' not found\n");
			echo "Error : Modem '" . $this->config->modem . "' not writable\n";
			echo "try : gammu identify\n";
			exit;
		}
		//var_dump( $config );exit;
		//die($this->config->db->host);
		
		echo "DB:\n";
		if( !$this->dbConnect()){
			echo "No db connection, check config\n";
			var_dump($config);	
		}else{
			echo "Ok : db connection\n";
			//echo "Checking tables\n";
			if($this->checkTables()){
				echo "Ok : check tables\n";
			}else{
				echo "Error : missing tables\n";
			}
		} 
		
		echo "done\n";
	}


	/**
	 * Detect gammu installation
	 * @return [type] [description]
	 */
	private function gammuDetect()
	{
		if (!file_exists( $this->config->gammu ) ){
			$this->error("Gammu not found <b><u>{$this->config->gammu}</u></b> or not installed\r\n");
			return false;
		}
		return true;
	}

	private function dbConnect(){
		
		$this->db = new mysqli( $this->config->db->host, $this->config->db->user, $this->config->db->pass , $this->config->db->name );
		if(!$this->db)die("No database connection");
		return true;
	}


	/**
	 * [checkModem description]
	 * @return [type] [description]
	 */
	private function checkModem(){
		// Detect modem //
		if(!is_writable( $this->config->modem ))
		{    
		    //$this->error("Error : $config->modem is not writable\n");
		    return false;
		}
		return true;
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

		$check = Array( 'inbox' , 'phonebook' , 'log_errors', 'log_sent' ,'services');

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



