<?php
/**
 * Sms pi, deal with sms
 * Just for fun
*/

class smspi {
	
	var $db=null;//db connection

	function __construct( $dbcon='' )
	{
		if (!$dbcon) {
			$this->error("Error no db connection\n");
		} else {
			$this->db = $dbcon;
		}
	}


	/**
	 * Return a funny error messages
	 */
	function error_message()
	{
		$errors = Array();
		$errors[] = "on se connait ?";
		$errors[] = "?";
		$errors[] = "va te faire foutre";
		$errors[] = "cette fois c'est fini";
		$errors[] = "n'insiste pas";
		$errors[] = "ouais c'est ca";
		$errors[] = "comment ca ?";
		$errors[] = "c'est ton nouveau numero ?";
		$errors[] = "kevin ?";
		$errors[] = "c'est une blague ?";
		$errors[] = "laisse tomber";
		$errors[] = "lol";
		$errors[] = "ca va toi ? Bisou";
		$errors[] = "ce soir je peux pas";
		$errors[] = "ok";

		// bonus //
		$errors[] = "Wesh tes con ou cest comment ? Tes sur que tu parle a la bonne personne ??";
		//$errors[] = "Heiin ok";
		$errors[] = "De quoii ?";
		$errors[] = "Lol quoii ?";
		//$errors[] = "Wesh geoffrezzz ca va ou quoii ??";
		shuffle($errors);
		return $errors[0];
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
		$sql.= " VALUES ( '$ID','$sent', '$coding', '$remote', 'unread', '" . $this->db->escape_string($body) . "' );";
		
		//echo "$sql\n";
		$this->db->query( $sql ) or $this->error( $this->db->error );
		
		$this->registerNumber( $r['remote_number'] );

		return true;
	}



	/**
	 * register a phone Number
	 * @param  string $phoneNumber [description]
	 * @return bool              [description]
	 */
	function registerNumber( $phoneNumber = "" )
	{
		$phoneNumber = trim( $phoneNumber );

		// UPSERT HERE //

		$sql = "INSERT INTO phonebook ( phonenumber, registered ) ";
		$sql.= " VALUES ( '$phoneNumber', NOW() );";
		
		$this->db->query( $sql ) or $this->error( $this->db->error );
	
		return true;
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
	 * Return the total number of messages, -> useful ?
	 * @return [type] [description]
	 */
	function inboxCount(){
		//echo "checkDb()\n";
		$q=$this->db->query("SELECT COUNT(*) FROM inbox;") or $this->error( $db->error );
		$r=$q->fetch_assoc();
		print_r($r);
	}



	/**
	 * Return the number of unread (unreplied) messages
	 * @return [type] [description]
	 */
	function unreadCount()
	{
		$q=$this->db->query("SELECT COUNT(*) FROM inbox;") or $this->error( $this->db->error );
		$r=$q->fetch_assoc();
		return $r['COUNT(*)'];
			
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
 
 
 	/*
 	function serviceRegister(){}
 	*/
 
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



	function error($e,$exit=0) {
		echo $e."\n";
		if($exit == 1){ exit; }
	}
}

