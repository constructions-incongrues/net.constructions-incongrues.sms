<?php

namespace ConstructionsIncongrues\Sms;

/**
 * Sms system integrity tests
 * Test class.
*/
class SmsTest
{
    public $config = null;
    public $db = null;

    public function __construct(\stdClass $conf)
    {
        $this->config = $conf;
        $this->dbConnect();
    }

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


    public function error($e, $exit = 0)
    {
        echo $e."\n";
        //error must be logged
        $this->log('error', $e);

        if ($exit == 1) {
            exit($exit);
        }
    }
}
