<?php
/**
 * Sms pi, deal with sms
 * Just for fun
*/

class smspi {

	var $config = null;
	var $db = null;//db connection

	function __construct( $conf='' )
	{
		if (!$conf) {
			$this->error("Error : no config\n");
		} else {
			$this->config = $conf;
		}
		@$this->dbConnect();
	}


	private function dbConnect() {	
		$this->db = new mysqli( $this->config->db->host, $this->config->db->user, $this->config->db->pass , $this->config->db->name );
		//if(!$this->db)die("No database connection");
		return $this->db;
	}





	/**
	 * Save sms's to db
	 * @return [type] [description]
	 */
	function saveSms( $r = Array() )
	{
		/*
	    [ID] => 54e09dcfdd3d44cad8feb1ce8ec47094
	    [sent] => 2014-01-22 20:35:17
	    [coding] => Default GSM alphabet (no compression)
	    [remote_number] => +33781623250
	    [body] => Coucou
		*/
		echo "saveSms();\n";//print_r( $r );

		$ID   = trim( $r['ID'] );
		$sent   = strtotime( $r['sent'] );
		$sent   = date( 'Y-m-d H:i:s' , $sent );
		$coding = trim( $r['coding'] );
		$remote = trim( $r['remote_number'] );
		//$status = trim( $r['status'] );//no
		$body   = trim( $r['body'] );

		$sql = "INSERT INTO inbox ( ID, sent, coding, remote_number, status, body ) ";
		$sql.= " VALUES ( '$ID','$sent', '$coding', '$remote', 'unread', '" . $this->db->escape_string($body) . "' ) ";
		$sql.= " ON DUPLICATE KEY UPDATE sent=NOW();";
		
		
		//echo "$sql\n";
		$this->db->query( $sql ) or $this->error( $this->db->error );
		
		$this->numberAdd( $r['remote_number'] );

		return true;
	}



	/**
	 * register a phone Number
	 * @param  string $phoneNumber [description]
	 * @return bool              [description]
	 */
	function numberAdd( $phoneNumber = "" )
	{
		$phoneNumber = trim( $phoneNumber );

		// UPSERT HERE //

		$sql = "INSERT INTO phonebook ( phonenumber, registered ) ";
		$sql.= " VALUES ( '$phoneNumber', NOW() ) ";
		$sql.= " ON DUPLICATE KEY UPDATE calls = calls+1, lastcall=NOW();";
		
		$this->db->query( $sql ) or $this->error( $this->db->error );
	
		return true;
	}

	/**
	 * Return the name of the owner of the given phoneNumber
	 * @return [type] [description]
	 */
	function numberName( $num )
	{
		$num = trim( $num );
		$sql = "SELECT name FROM phonebook WHERE phonenumber LIKE '" . $this->db->escape_string( $num ) . "';";
		$q = $this->db->query( $sql ) or $this->error( $this->db->error );
		$r = $q->fetch_assoc();
		if($q->num_rows)return $r['name'];
		return false;
	}


	/**
	 * Return the blocked status as bool
	 * @param  [type]  $phoneNumber [description]
	 * @return boolean              [blocked]
	 */
	function isBlocked( $phoneNumber="" )
	{
		
		$sql = "SELECT blocked FROM phonebook WHERE phonenumber LIKE '$phoneNumber';";
		$this->db->query( $sql ) or $this->error( $this->db->error );
		
	}



	//this is a bit shitty
	function markAsRead( $msgId=0 )
	{
		
		$msgId*=1;
		if(!$msgId)return false;
		
		$q = $this->db->query("UPDATE inbox SET status='read' WHERE `i`=$msgId LIMIT 1;") or $this->error( $db->error );
		
		return true;
	}



	/**
	 * Detect gammu installation
	 * @return [type] [description]
	 */
	public function gammuDetect()
	{
		if (!file_exists( $this->config->gammu ) ){
			$this->error("Gammu not found <b><u>{$this->config->gammu}</u></b> or not installed\r\n");
			return false;
		}
		return true;
	}



	/**
	 * Return N unread message
	 * @param  integer $limit [description]
	 * @return [type]         [description]
	 */
 	function getUnread( $limit=0)
 	{
 		$q = $this->db->query("SELECT * FROM inbox WHERE status LIKE 'unread';");
 		$dat=Array();
 		while( $r = $q->fetch_assoc()){
 			$dat[]=$r;
 		}
 		return $dat;
 	}
 

 	/**
 	 * Logs
 	 */
	function logs(){

		$sql = "SELECT * FROM log_errors WHERE 1 ORDER BY id DESC LIMIT 30;";
 		$q = $this->db->query( $sql ) or $this->error( $this->db->error );
		
		$dat = Array();
		while( $r = $q->fetch_assoc() ){
			$dat[] = $r;
		}		

		return $dat;
	} 

	/**
	 * Clear logs like msg
	 * @param  string $msg [description]
	 * @return [type]      [description]
	 */
	function logClear( $msg='' )
	{
		$sql = "DELETE FROM log_errors WHERE error LIKE '" . $this->db->escape_string( $msg ) ."';";
 		$q = $this->db->query( $sql ) or $this->error( $this->db->error );
		return true;	
	}


	/**
	 * Write a log Sent
	 * @param  string $remote_number [description]
	 * @param  string $message       [description]
	 * @param  string $response      [description]
	 * @return [type]                [description]
	 */
	function logSent( $remote_number='', $message='' , $response='' )
	{
		$remote_number = trim( $remote_number );
		$message = trim( $message );
		$response = trim( $response );

		$sql = "INSERT INTO log_sent ( number, message, response, time) ";
		$sql.= "VALUES ( '" . $this->db->escape_string( $remote_number ) . "' , '".$this->db->escape_string( $message )."' , '".$this->db->escape_string( $response )."' , NOW() );";
		
		$q = $this->db->query( $sql ) or $this->error( $this->db->error );
		return $this->db->insert_id;
	}


	/**
	 * Check if the modem is writable
	 * @return [type] [description]
	 */
	public function modemWritable(){
		// Detect modem //
		if(!is_writable( $this->config->modem ))
		{    
		    //$this->error("Error : $config->modem is not writable\n");
		    return false;
		}
		return true;
	}

 	/*
 	functions service
 	*/
 
 	function serviceList(){
		
		$sql = "SELECT * FROM services WHERE 1 ORDER BY name;";
 		$q = $this->db->query( $sql ) or $this->error( $this->db->error );
		
		$dat = Array();
		while( $r = $q->fetch_assoc() ){
			$dat[] = $r;
		}		

		return $dat;
 	}


 	function serviceRegister( $serviceName='' ){
 		
 		$serviceName = trim( $serviceName );
 		if(!$serviceName)return false;
 		
 		$sql = "INSERT INTO services ( name, created ) VALUES ( '" . $this->db->escape_string( $serviceName ) . "' , NOW() );";
 		$q = $this->db->query( $sql ) or $this->error( $this->db->error );	

		return $this->db->insert_id;	
 	}

 	/**
 	 * Return one service record for a given service name, or false if the service is not found.
 	 * @param  string $serviceName [description]
 	 * @return [type]              [description]
 	 */
 	function serviceGet( $serviceName="" )
 	{
 		$serviceName = trim( $serviceName );
 		if(!$serviceName)return false;

 		$sql = "SELECT * FROM services WHERE name LIKE '" . $this->db->escape_string( $serviceName ) . "';";
 		$q = $this->db->query( $sql ) or $this->error( $this->db->error );
		if(!$q->num_rows)return false;

		return $q->fetch_assoc();
 	}

 	/**
 	 * Increment service call
 	 * @return [type] [description]
 	 */
 	function serviceUpdate( $serviceId=0 ){
 		$serviceId*=1;
 		if(!$serviceId)return false;
 		$sql = "UPDATE services SET calls=calls+1 WHERE id=$serviceId;";
		$q = $this->db->query( $sql ) or $this->error( $this->db->error );
		return true;
 	}



	function error($e,$exit=0) {
		echo $e."\n";
		//error must be logged
		$this->log( 'error' , $e );
		
		if($exit == 1){ exit; }
	}

	function log( $status='', $msg='' ){

		//echo "log( $status , $msg )\n";
 		
 		$sql = "INSERT INTO log_errors ( status, error, time ) ";
 		$sql.= "VALUES ( '".$this->db->escape_string( $status )."' , '" . $this->db->escape_string( $msg ) . "' , NOW() );";
 		
 		//$q = $this->db->query( $sql ) or $this->error( $this->db->error );
 		$q = $this->db->query( $sql ) or die( $this->db->error . "\n" . $sql );
 		
 		return true;
	}


	/**
	 * Return a funny error messages
	 */
	function error_message()
	{
		$errors = Array();
		$errors[] = "on se connait ?";
		$errors[] = "Ca va sinon ?";
		$errors[] = "va te faire foutre";
		$errors[] = "cette fois c'est fini";
		$errors[] = "n'insiste pas";
		$errors[] = "ouais c'est ca";
		$errors[] = "comment ca ?";
		$errors[] = "c'est ton nouveau numero ?";
		$errors[] = "kevin ?";
		$errors[] = "c'est une blague ?";
		$errors[] = "laisse tomber";
		$errors[] = "si tu continue, j'appelle les flics";
		$errors[] = "mauvais numero ?";
		$errors[] = "lol";
		$errors[] = "ca va toi ? Bisou";
		$errors[] = "ce soir je peux pas";
		$errors[] = "mon frere va te peter la gueule";
		$errors[] = "arrete ca tout de suite";
		$errors[] = "encore un mot et je porte plainte";
		$errors[] = "je suis juste derriere toi";
		//$errors[] = "il y a une limite a ne pas depasser";

		// bonus //
		$errors[] = "Wesh tes con ou cest comment ? Tes sur que tu parle a la bonne personne ??";
		//$errors[] = "Heiin ok";
		$errors[] = "De quoii ?";
		$errors[] = "Lol quoii ?";
		$errors[] = "Wesh geoffrezzz ca va ou quoii ??";
		shuffle( $errors );
		return $errors[0];
	}


	//Queue
	
	/**
	 * Add a message to the queue
	 * @param  string $number [description]
	 * @param  string $body   [description]
	 * @return [type]         [description]
	 */
	function queue_add($number='',$body='', $datetime='')
	{
		$number=trim($number);
		$body = trim($body);

		if(!$number)return false;
		if(!$body)return false;
		
		$date = 'NOW()';

		$sql = "INSERT INTO queue (q_number, q_body, q_sendtime) ";
		$sql.= "VALUES ('" . $this->db->escape_string( $number ) . "', '".$this->db->escape_string( $body )."', $date );";
		
		$this->db->query( $sql ) or die( $this->db->error );
		
		return $this->db->insert_id;
	}


	/**
	 * Return the full msg queue
	 * @return [type] [description]
	 */
	function queue_get(){

		$sql = "SELECT * FROM queue WHERE 1;";
		$q = $this->db->query( $sql ) or die( $this->db->error );
		$DAT = Array();
		while($r=$q->fetch_assoc()){
			$DAT[$r['q_id']] = $r;
		}
		return $DAT;
	}

	/**
	 * Delete one message from the queue
	 * @param  integer $id [description]
	 * @return [type]      [description]
	 */
	function queue_del($id=0)
	{
		$sql = "DELETE FROM queue WHERE q_id=$id LIMIT 1;";
		$q = $this->db->query( $sql ) or die( $this->db->error );
		return true;
	}


}

