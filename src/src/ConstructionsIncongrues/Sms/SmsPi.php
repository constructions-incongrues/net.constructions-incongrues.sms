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
            $this->config->db->pass,
            $this->config->db->name
        );
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
        echo "saveSms();\n";//print_r( $r );

        $ID   = trim($r['ID']);
        $sent   = strtotime($r['sent']);
        $sent   = date('Y-m-d H:i:s', $sent);
        $coding = trim($r['coding']);
        $remote = trim($r['remote_number']);
        $body   = trim($r['body']);

        $sql = "INSERT INTO msg_in ( ID, sent, coding, remote_number, status, body ) ";
        $sql.= " VALUES ( '$ID','$sent', '$coding', '$remote', 'unread', '" . $this->db->escape_string($body) . "' ) ";
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

        return $phoneNumber;
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
     * Return the name of the owner of the given phoneNumber
     * @return [type] [description]
     */
    public function numberName($num = '')
    {
        $num = trim($num);

        if (!$num) {
            return false;
        }

        $sql = "SELECT name FROM phonebook WHERE phonenumber LIKE '" . $this->db->escape_string($num) . "';";
        $q = $this->db->query($sql) or $this->error($this->db->error);
        $r = $q->fetch_assoc();

        if ($q->num_rows) {
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
    public function numberSave($id = 0, $name = '', $comment = '')
    {

        $id*=1;
        if (!$id) {
            return false;
        }

        $sql = "UPDATE phonebook SET ";
        $sql.= "name='".$this->db->escape_string($name)."', ";
        $sql.= "comment='".$this->db->escape_string($comment)."' ";
        $sql.= "WHERE id=$id LIMIT 1;";

        //$q = $this->db->query($sql) or die($sql);
        $q = $this->db->query($sql) or $this->error($this->db->error);

        return $this->db->affected_rows;
    }


    /**
     * Return the name of the owner of the given phoneNumber
     * @return [type] [description]
     */
    public function numberData($num = '')
    {
        $num = trim($num);

        if (!$num) {
            return false;
        }

        $sql = "SELECT * FROM phonebook WHERE phonenumber LIKE '" . $this->db->escape_string($num) . "';";
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
        if (!is_writable($this->config->modem))
        {
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


    /**
     * Return a funny error messages
     */
    public function error_message()
    {
        $errors = array();
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
        $errors[] = "je ne te derange pas au moins ?";

        // bonus //
        $errors[] = "Wesh tes con ou cest comment ? Tes sur que tu parle a la bonne personne ??";
        //$errors[] = "Heiin ok";
        $errors[] = "De quoii ?";
        $errors[] = "Lol quoii ?";
        $errors[] = "Wesh geoffrezzz ca va ou quoii ??";
        shuffle($errors);
        return $errors[0];
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
            return false;
        }

        $date = 'NOW()';

        $sql = "INSERT INTO msg_queue (q_number, q_body, q_sendtime) ";
        $sql.= "VALUES ('" . $this->db->escape_string($number) . "', '".$this->db->escape_string($body)."', $date );";

        $this->db->query($sql) or die($this->db->error);

        return $this->db->insert_id;
    }


    /**
     * Return the full msg queue
     * @return [type] [description]
     */
    public function queue_get()
    {

        $sql = "SELECT * FROM msg_queue WHERE 1;";
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
    function queue_del($id = 0)
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
        //Get last x numbers
        $sub="SELECT DISTINCT number FROM log_spam WHERE 1 ORDER BY id DESC LIMIT 20;";
        //$q = $this->db->query($sub) or die( $this->db->error );

        $sql="SELECT phonenumber FROM phonebook WHERE phonenumber NOT IN ( 1 ) ORDER BY RAND() LIMIT 1;";
        $q = $this->db->query($sql) or die( $this->db->error );

        if (!$q->num_rows) {
            return false;
        }

        $r=$q->fetch_assoc();
        return $r['phonenumber'];

    }

    /**
     * Log spamed number and message (to avoid repetition later)
     * @return [type] [description]
     */
    public function spamLog($number, $body)
    {
        $sql="INSERT INTO log_spam (number, body, time) ";
        $sql.=" VALUES (\"$number\",\"$body\",NOW());";
        $q = $this->db->query($sql) or die( $this->db->error );
        return true;
    }
}
