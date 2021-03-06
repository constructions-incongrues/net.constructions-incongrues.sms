<?php

namespace ConstructionsIncongrues\Sms;

/**
 * Sms pi, deal with sms
 * Just for fun
*/
class SmsPi
{
    public $config = null;
    public $db = null;

    public function __construct(\stdClass $conf)
    {
        $this->config = $conf;
        $this->dbConnect();
        if (!$this->db->select_db($this->config->db->name)) {
            throw new \Exception("Error selecting db : ".$this->config->db->name, 1);
        }
    }

    /**
     * Mysqli db connection
     * @return [type] [description]
     */
    private function dbConnect()
    {
        $this->db = new \mysqli(
            $this->config->db->host,
            $this->config->db->user,
            $this->config->db->pass
            //$this->config->db->name
        ) or die($this->db->error);
        return $this->db;
    }

 
    /**
     * Return a sms conversation for a given number
     * @return [type] [description]
     */
    public function conversation($number = '', $limit = 10)
    {
        $conv=[];

        $sql = "SELECT sent as t, body as message FROM msg_in ";
        $sql.= "WHERE remote_number LIKE '$number' ORDER BY t DESC LIMIT $limit;";

        $q = $this->db->query($sql) or die($this->db->error);
        //echo "<pre>$sql</pre>";
        while ($r=$q->fetch_assoc()) {
            $t = strtotime($r['t']);
            $conv[$t]['in'] = $r['message'];
        }

        $sql = "SELECT message, time as t FROM msg_out ";
        $sql.= "WHERE `number` LIKE '$number' ORDER BY t DESC LIMIT $limit;";

        $q = $this->db->query($sql) or die($this->db->error);
        //echo "<pre>$sql</pre>";
        while ($r=$q->fetch_assoc()) {
            $t = strtotime($r['t']);
            //print_r($r);
            $conv[$t]['out'] = $r['message'];
        }

        ksort($conv);
        return $conv;
    }

    /**
     * Save sms's to db
     * @return [type] [description]
     */
    public function saveSms($r = array())
    {
        /*
        [ID] => 54e09dcfdd3d44cad8feb1ce8ec47094
        [sent] => 2014-01-22 20:35:17
        [coding] => Default GSM alphabet (no compression)
        [remote_number] => +33781623250
        [body] => Coucou
        */
        echo "saveSms();\n";
        //print_r( $r );exit;

        $ID   = trim($r['ID']);
        
        //echo "sent=$sent";//bug 1970 ?
        //$sent   = strtotime($r['sent']);
        //$sent   = date('Y-m-d H:i:s', $sent);
        
        $coding = trim($r['coding']);
        $remote = trim($r['remote_number']);
        $body   = trim($r['body']);

        $sql = "INSERT INTO msg_in ( ID, sent, coding, remote_number, status, body ) ";
        $sql.= " VALUES ( '$ID', NOW(), '$coding', '$remote', 'unread', '" . $this->db->escape_string($body) . "' ) ";
        $sql.= " ON DUPLICATE KEY UPDATE sent=NOW();";

        $this->db->query($sql) or $this->error($this->db->error);

        $this->numberAdd($r['remote_number']);

        return true;
    }



    /**
     * register a valid, french mobile phone Number
     * @param  string $phoneNumber [description]
     * @return bool              [description]
     */
    public function numberAdd($phoneNumber = "", $comment = "")
    {

        $phoneNumber=$this->numberCheck($phoneNumber);
        if (!$phoneNumber) {
            return false;
        }

        $sql = "INSERT INTO phonebook ( phonenumber, comment, registered ) ";
        $sql .= " VALUES ( '$phoneNumber', '" . $this->db->escape_string($comment) . "', NOW() ) ";
        $sql .= " ON DUPLICATE KEY UPDATE calls = calls+1, lastcall=NOW();";

        $this->db->query($sql) or $this->error($this->db->error);

        return $this->db->insert_id;
    }


    /**
     * Check phone number
     * Number should be in mobile, french format
     * Number may be transformed from "06" to "+336"
     * @param  string $phoneNumber [description]
     * @return string $phoneNumber [description]
     */
    public function numberCheck($phoneNumber = "")
    {
        $phoneNumber = trim($phoneNumber);
        //correction du numero en ajoutant +33 si necessaire
        if (preg_match("/^0[67][0-9]{8}$/", $phoneNumber)) {
            $phoneNumber = preg_replace("/^0([67])/", "+33$1", $phoneNumber);
        }

        //correction format long (0033)
        if (preg_match("/^0033[67][0-9]{8}$/", $phoneNumber)) {
            $phoneNumber = preg_replace("/^0033/", "+33", $phoneNumber);
        }

        // verification du format final '+33000000000'
        if (!preg_match("/^\+33[67][\d]{8}$/", $phoneNumber)) {
            return false;
        }
        return $phoneNumber;
    }

    /**
     * Produce a random french mobile phone number
     * @return string [description]
     */
    public function randomPhoneNumber()
    {
        $r=rand(10000000, 99999999);
        return "+336$r\n";
    }


    /**
     * Return the name of the owner of the given phoneNumber
     * @return [type] [description]
     */
    public function numberName($numberId = 0)
    {
        $numberId*=1;

        if (!$numberId) {
            return false;
        }

        $sql = "SELECT phonenumber, name FROM phonebook WHERE id=$numberId;";
        $q = $this->db->query($sql) or $this->error($this->db->error);
        $r = $q->fetch_assoc();

        if ($q->num_rows) {
            if (!$r['name']) {
                return $r['phonenumber'];
            }
            return $r['name'];
        }

        return false;
    }

    /**
     * Save a phone number details (name, comments)
     * @param  integer $id      [description]
     * @param  string  $name    [description]
     * @param  string  $comment [description]
     * @return [type]           [description]
     */
    public function numberSave($id = 0, $name = '', $comment = '', $email = '')
    {

        $id*=1;
        if (!$id) {
            return false;
        }

        $sql = "UPDATE phonebook SET ";
        $sql.= "name='".$this->db->escape_string($name)."', ";
        $sql.= "comment='".$this->db->escape_string($comment)."', ";
        $sql.= "email='".$this->db->escape_string($email)."' ";
        $sql.= "WHERE id=$id LIMIT 1;";

        //$q = $this->db->query($sql) or die($sql);
        $q = $this->db->query($sql) or $this->error($this->db->error);

        return $this->db->affected_rows;
    }



    /**
     * Return the name of the owner of the given phoneNumber
     * @return [type] [description]
     */
    public function numberId($phonenumber = '')
    {
        //echo __FUNCTION__."($id);";
        //$phonenumber="+".trim($phonenumber);
        
        $check=$this->numberCheck($phonenumber);//check and convert
        
        if (!$check) {
            echo __FUNCTION__." :: invalid phone number $phonenumber\n";
            return false;
        }

        $sql = "SELECT id FROM phonebook WHERE phonenumber LIKE '$check';";
        $q = $this->db->query($sql) or $this->error($this->db->error);

        //echo "<pre>$sql</pre>";

        $r = $q->fetch_assoc();

        if ($q->num_rows) {
            return $r['id'];
        }

        return false;
    }



    /**
     * Return the name of the owner of the given phoneNumber
     * @return [type] [description]
     */
    public function numberData($id = 0)
    {
        //$num = trim($num);
        $id*=1;

        if (!$id) {
            return false;
        }

        $sql = "SELECT * FROM phonebook WHERE id='$id';";
        $q = $this->db->query($sql) or $this->error($this->db->error);
        $r = $q->fetch_assoc();

        if ($q->num_rows) {
            return $r;
        }

        return false;
    }

    /**
     * Delete the given phoneNumber id
     * @return bool [description]
     */
    public function numberDelete($id = 0)
    {
        $id*=1;

        if (!$id) {
            return false;
        }

        $sql = "DELETE FROM phonebook WHERE id='$id' LIMIT 1;";
        $q = $this->db->query($sql) or $this->error($this->db->error);

        return true;
    }


    /**
     * Return the blocked status as bool
     * @param  [type]  $phoneNumber [description]
     * @return boolean              [blocked]
     */
    public function isBlocked($phoneNumber = "")
    {
        $sql = "SELECT blocked FROM phonebook WHERE phonenumber LIKE '$phoneNumber';";
        $this->db->query($sql) or $this->error($this->db->error);
    }

    /**
     * this is a bit shitty, mark a inbox message as read (processed)
     * @param  [type] $msgId [description]
     * @return [type]        [description]
     */
    public function markAsRead($msgId = 0)
    {
        $q = $this->db->query("UPDATE msg_in SET status='read' WHERE `i`=$msgId LIMIT 1;") or $this->error($db->error);
        return true;
    }




    /**
     * Detect gammu installation
     * @return [type] [description]
     */
    public function gammuDetect()
    {
        if (!file_exists($this->config->gammu)) {
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
    public function getUnread($limit = 0)
    {
        $q = $this->db->query("SELECT * FROM msg_in WHERE status LIKE 'unread';");
        $dat = array();
        while ($r = $q->fetch_assoc()) {
            $dat[] = $r;
        }
        return $dat;
    }


    /**
     * Sms system Logs (errors/warnings/notice)
     */
    public function logs($filter = '', $limit = 30)
    {

        $limit*=1;
        $WHERE=[];
        $WHERE[]=1;

        if ($filter) {
            $WHERE[]="error LIKE '%".$this->db->escape_string($filter)."%'";
        }

        $sql = "SELECT id, status, error, time FROM log_errors ";
        $sql.= "WHERE ".implode(" AND ", $WHERE);
        $sql.= " ORDER BY id DESC LIMIT $limit;";

        $q = $this->db->query($sql) or $this->error($this->db->error . "<hr />$sql");

        $dat = array();
        while ($r = $q->fetch_assoc()) {
            $dat[] = $r;
        }

        return $dat;
    }

    /**
     * Clear logs like msg
     * @param  string $msg [description]
     * @return [type]      [description]
     */
    public function logClear($msg = '')
    {
        if (!$msg) {
            return false;
        }
        
        $sql = "DELETE FROM log_errors WHERE error LIKE '" . $this->db->escape_string($msg) ."';";
        $q = $this->db->query($sql) or $this->error($this->db->error);
        return true;
    }


    /**
     * Write a log Sent
     * @param  string $remote_number [description]
     * @param  string $message       [description]
     * @param  string $response      [description]
     * @return [type]                [description]
     */
    public function logSent($remote_number = '', $message = '', $response = '')
    {
        $remote_number = trim($remote_number);
        $message = trim($message);
        $response = trim($response);

        $sql = "INSERT INTO msg_out ( number, message, response, time) ";
        $sql.= "VALUES ( '" . $this->db->escape_string($remote_number) . "' , '".$this->db->escape_string($message)."' , '".$this->db->escape_string($response)."' , NOW() );";

        $q = $this->db->query($sql) or $this->error($this->db->error);
        return $this->db->insert_id;
    }


    /**
     * Check if the modem is writable
     * @return [type] [description]
     */
    public function modemWritable()
    {
        // Detect modem //
        if (!is_writable($this->config->modem)) {
            //$this->error("Error : $config->modem is not writable\n");
            return false;
        }
        return true;
    }

    /**
     * Check Curl php extension
     * @return boolean [description]
     */
    public function isCurlInstalled()
    {
        if (in_array('curl', get_loaded_extensions())) {
            return true;
        } else {
            return false;
        }
    }



    /**
    * Return the list of registered services
    */
    public function serviceList()
    {
        $sql = "SELECT * FROM services WHERE 1 ORDER BY name;";
        $q = $this->db->query($sql) or $this->error($this->db->error);

        $dat = array();
        while ($r = $q->fetch_assoc()) {
            $dat[] = $r;
        }

        return $dat;
    }

    /**
    * Return the list of services names
    */
    public function serviceNames()
    {
        $sql = "SELECT name FROM services WHERE 1 ORDER BY name;";
        $q = $this->db->query($sql) or $this->error($this->db->error);

        $dat = array();
        while ($r = $q->fetch_assoc()) {
            $dat[] = $r['name'];
        }

        return $dat;
    }

    /**
    * Return one service name for a given service id
    */
    public function serviceName($id = 0)
    {
        $id*=1;
        
        if (!$id) {
            return false;
        }
        
        $sql = "SELECT name FROM services WHERE id=$id ORDER BY name;";
        $q = $this->db->query($sql) or $this->error($this->db->error);
        if ($name=$q->fetch_assoc()['name']) {
            return $name;
        }

        return false;
    }


    /**
     * Register a new service
     * @param  string $serviceName [description]
     * @return [type]              [description]
     */
    public function serviceRegister($serviceName = '')
    {
        $serviceName = trim($serviceName);
        if (!$serviceName) {
            return false;
        }

        $sql = "INSERT INTO services (name,created) VALUES ('".$this->db->escape_string($serviceName)."', NOW() );";
        $q = $this->db->query($sql) or $this->error($this->db->error);

        return $this->db->insert_id;
    }


    /**
     * Return one service record for a given service name, or false if the service is not found.
     * @param  string $serviceName [description]
     * @return [type]              [description]
     */
    public function service($serviceId = 0)
    {
        $serviceId*=1;

        if (!$serviceId) {
            return false;
        }

        $sql = "SELECT * FROM services WHERE id=$serviceId LIMIT 1;";
        $q = $this->db->query($sql) or $this->error($this->db->error);

        if (!$q->num_rows) {
            return false;
        }

        return $q->fetch_assoc();
    }


    /**
     * Return one service record for a given service name, or false if the service is not found.
     * @param  string $serviceName [description]
     * @return [type]              [description]
     */
    public function serviceByName($serviceName = "")
    {
        $serviceName = trim($serviceName);
        if (!$serviceName) {
            return false;
        }

        $sql = "SELECT * FROM services WHERE name LIKE '" . $this->db->escape_string($serviceName) . "';";
        $q = $this->db->query($sql) or $this->error($this->db->error);
        
        if (!$q->num_rows) {
            return false;
        }

        return $q->fetch_assoc();
    }

    public function serviceSave($id = 0, $name = "", $url = "", $comment = "")
    {
        $id*=1;
        $name=trim($name);

        if (!$id || !$name || !$this->service($id)) {
            return false;
        }

        $url=trim($url);
        $comment=trim($comment);

        $sql = "UPDATE services SET ";
        $sql.= "name=\"".$this->db->escape_string($name)."\", ";
        $sql.= "url=\"".$this->db->escape_string($url)."\", ";
        $sql.= "comment=\"".$this->db->escape_string($comment)."\" ";
        $sql.= "WHERE id=$id LIMIT 1;";

        //$q = $this->db->query($sql) or die($sql);
        $q = $this->db->query($sql) or $this->error($this->db->error);
        return true;
    }


    /**
     * Increment service call
     * TODO : make the method private
     * @return [type] [description]
     */
    public function serviceUpdate($serviceId = 0)
    {
        $serviceId *= 1;
        if (!$serviceId) {
            return false;
        }
        $sql = "UPDATE services SET calls=calls+1 WHERE id=$serviceId;";
        $q = $this->db->query($sql) or $this->error($this->db->error);
        return true;
    }

    /**
     * Return table info
     * @param  string $tableName [description]
     * @return [type]            [description]
     */
    public function tableInfo($tableName = '')
    {
        $tableName = trim($tableName);

        if (!$tableName) {
            return false;
        }

        $sql = "SELECT * FROM information_schema.tables ";//TABLE_NAME, UPDATE_TIME, TABLE_ROWS
        $sql.= "WHERE  TABLE_SCHEMA = 'sms' AND TABLE_NAME LIKE '$tableName';";

        $q = $this->db->query($sql) or $this->error($this->db->error);

        if (!$q->num_rows) {
            return false;
        }

        return $q->fetch_assoc();
    }


    public function error($e, $exit = 0)
    {
        echo $e."\n";
        //error must be logged
        $this->log('error', $e);

        if ($exit == 1) {
            exit($exit);
        }
    }


    /**
     * Write a database log ('notice'|'warning'|'error')
     * @param  string $status [description]
     * @param  string $msg    [description]
     * @return [type]         [description]
     */
    public function log($status = '', $msg = '')
    {

        $sql = "INSERT INTO log_errors ( status, error, time ) ";
        $sql.= "VALUES ( '".$this->db->escape_string($status)."' , '" . $this->db->escape_string($msg) . "' , NOW() );";

        $q = $this->db->query($sql) or die($this->db->error . "\n" . $sql);

        return true;
    }



    //Queue

    /**
     * Add a message to the queue
     * @param  string $number [description]
     * @param  string $body   [description]
     * @return [type]         [description]
     */
    public function queue_add($number = '', $body = '', $datetime = '')
    {
        $number = trim($number);
        $body = trim($body);

        if (!$number) {
            return false;
        }
        if (!$body) {
            $smspi->log('error', "queue_add() -> no body");
            return false;
        }

        $date = 'NOW()';

        $sql = "INSERT INTO msg_queue (q_number, q_body, q_sendtime) ";
        $sql.= "VALUES ('" . $this->db->escape_string($number) . "', '".$this->db->escape_string($body)."', $date );";

        $this->db->query($sql) or die($this->db->error);

        return $this->db->insert_id;
    }


    /**
     * Return the current msg queue (the future messages are not returned)
     * @return [type] [description]
     */
    public function queue_get()
    {

        $sql = "SELECT * FROM msg_queue WHERE q_sendafter<NOW();";
        $q = $this->db->query($sql) or die( $this->db->error );
        $DAT = array();
        while ($r = $q->fetch_assoc()) {
            $DAT[$r['q_id']] = $r;
        }
        return $DAT;
    }


    /**
     * Delete one message from the queue
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function queue_del($id = 0)
    {
        $id*=1;

        if (!$id) {
            return false;
        }

        $sql = "DELETE FROM msg_queue WHERE q_id='$id' LIMIT 1;";
        $q = $this->db->query($sql) or die($this->db->error);
        return true;
    }

    /**
     * Delete one message from the queue
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function queue_clear()
    {
        $sql = "DELETE FROM msg_queue WHERE 1;";
        $q = $this->db->query($sql) or die($this->db->error);
        return true;
    }




    /**
     * [phoneBook description]
     * @param  string $filter [description]
     * @return [type]         [description]
     */
    public function phoneBook($filter = '', $limit = 30)
    {
        $WHERE=[];
        $WHERE[]=1;

        if ($filter) {
            if (preg_match("/^[0-9]+/", $filter)) {
                $WHERE[]="phonenumber LIKE '%".$this->db->escape_string($filter)."%'";
            } else {
               $WHERE[]="(name LIKE '%".$this->db->escape_string($filter)."%' OR comment LIKE '%".$this->db->escape_string($filter)."%')";
            }
        }

        $sql = "SELECT * FROM phonebook ";
        $sql.= " WHERE " . implode(" AND ", $WHERE);
        $sql.= " ORDER BY name LIMIT $limit;";

        $q = $this->db->query($sql) or die( $this->db->error );

        $dat=[];

        while ($r = $q->fetch_assoc()) {
            $dat[]=$r;
        }
        return $dat;
    }

    /**
     * Return the number of phonenumbers
     * @return [type] [description]
     */
    public function phoneBookCount()
    {
        $sql = "SELECT COUNT(*) FROM phonebook;";
        $q = $this->db->query($sql) or die( $this->db->error );
        $r= $q->fetch_assoc();
        //print_r($r);
        return $r['COUNT(*)'];
    }


    /**
     * SPAM Functions
     * SPAM Functions
     * SPAM Functions
     */


    /**
     * Return the last spam record
     * @return [type] [description]
     */
    public function spamGetLast()
    {
        $sql="SELECT * FROM log_spam ORDER BY id DESC LIMIT 1;";
        $q = $this->db->query($sql) or die( $this->db->error );

        if (!$q->num_rows) {
            return false;
        }
        return $q->fetch_assoc();
    }



    /**
     * Return a random number to spam
     * @return [type] [description]
     */
    public function spamGetDest()
    {

        $sql="SELECT * FROM `phonebook` WHERE spammed<CURDATE() - INTERVAL 30 DAY LIMIT 1;";
        $q = $this->db->query($sql) or die( $this->db->error );

        if (!$q->num_rows) {
            return false;
        }

        $r=$q->fetch_assoc();
        return $r;
    }



    /**
     * Mark 
     * @param  integer $phone_id [description]
     * @return [type]            [description]
     */
    public function spammed($phone_id = 0)
    {
        $phone_id*=1;
        
        if (!$phone_id) {
            return false;
            //throw new Exception("Error Processing Request", 1);
        }

        $sql="UPDATE `phonebook` SET spammed=NOW() WHERE id=$phone_id LIMIT 1;";
        $q = $this->db->query($sql) or die( $this->db->error );
        return true;
    }
    


    /**
     * Subscriptions
     */


    /**
     * Return the complete list of subscripbers
     * @return [type] [description]
     */
    public function subscribe($phone_id = 0, $service_id = 0)
    {
        //echo __FUNCTION__."($phone_id, $service_id)";
        
        $phone_id*=1;
        $service_id*=1;
          
        //$phoneid=$this->numberId($phonenumber);
        
        $serviceName=$this->serviceName($service_id);
        
        if (!$serviceName) {
            return false;
        }

        if (!$this->numberData($phone_id)) {
            return false;
        }

        $sql ="INSERT INTO sms.subscriptions (phonenumber, service) ";
        $sql.="VALUES ('$phone_id','$serviceName');";
        
        $q=$this->db->query($sql) or die($db->error);
        return $this->db->insert_id;
    }

    
    /**
     * Return the complete list of subscripbers
     * @return [type] [description]
     */
    public function unsubscribe($id = 0)
    {
        $id*=1;

        $sql ="DELETE FROM sms.subscriptions WHERE id=$id LIMIT 1;";
        
        $q=$this->db->query($sql) or die($db->error);
        return true;
    }

    /**
     * Return the complete list of subscripbers
     * @return [type] [description]
     */
    public function getSubscribers()
    {
        //global $db;

        $sql="SELECT * FROM sms.subscriptions WHERE 1 AND last_call<CURDATE();";
        $q=$this->db->query($sql) or die($db->error);
        $dat=[];
        while ($r=$q->fetch_assoc()) {
            $dat[]=$r;
        }
        return $dat;
    }

    /**
     * Update one subscription 'lastcall'
     * @param  integer $subscription_id [description]
     * @return [type]                   [description]
     */
    public function updateSubscription($subscription_id = 0)
    {
        //global $db;
        $subscription_id*=1;
        $sql = "UPDATE sms.subscriptions SET last_call=NOW() WHERE id=$subscription_id";
        $q=$this->db->query($sql) or die($db->error);
        return true;
    }

}
