<?php

namespace ConstructionsIncongrues\Sms;


class SmsAdmin
{

    private $title='Super SMS';
    private $path='../';
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


    public function path($path)
    {
        $this->path = $path;
    }

    public function title($title)
    {
        //echo "title($title);";
        $this->title = $title;
    }


    public function menu()
    {
        $htm=[];
        //<!-- Top Menu -->
        $htm[]="<nav class='navbar navbar-default navbar-fixed-top' role=navigation>";
        $htm[]="<div class=container>";

        // Brand and toggle get grouped for better mobile display -->
        $htm[]="<div class='navbar-header'>";

              $htm[]="<a class='navbar-brand' href=../home/>Super SMS</a>";

              $htm[]="<ul class='nav navbar-nav'>";

                $htm[]="<li title='Services'><a href=../services ><i class='glyphicon glyphicon-list'></i> Services <span class='badge'>".$this->serviceCount()."</span></a></li>";

                $htm[]="<li title='Phonebook'><a href=../phonebook><i class='glyphicon glyphicon-book'></i> <span class=badge>".$this->phoneBookCount()."</span> </a></li>";

                $htm[]="<li title='Subscriptions'><a href=../subscriptions><i class='glyphicon glyphicon-retweet'></i> Subs</a></li>";

                $htm[]="<li><a href=../logs><i class='glyphicon glyphicon-list'></i> Logs</a></li>";
                $htm[]="<li title='Inbox'><a href=../inbox><i class='glyphicon glyphicon-import'></i></a></li>";
                $htm[]="<li title='Sent'><a href=../sent><i class='glyphicon glyphicon-export'></i></a></li>";

                //queue
                $qc=$this->queueCount();
                if ($qc>0) {
                    $htm[]="<li><a href=../queue><i class='glyphicon glyphicon-cog'></i> <span class='label label-danger'>$qc</span></a></li>";
                }
              $htm[]="</ul>";
            $htm[]="</div>";
          $htm[]="</div>";
        $htm[]="</nav>";

        return implode('', $htm);
    }

    public function serviceCount()
    {
        $sql="SELECT COUNT(*) FROM sms.services;";
        $q=$this->db->query($sql) or die("<pre>$sql</pre>");
        $r=$q->fetch_assoc();
        return $r['COUNT(*)'];
    }

    public function queueCount()
    {
        $sql="SELECT COUNT(*) FROM sms.msg_queue;";
        $q=$this->db->query($sql) or die("<pre>$sql</pre>");
        $r=$q->fetch_assoc();
        return $r['COUNT(*)'];

    }

    public function phoneBookCount()
    {
        $sql="SELECT COUNT(*) FROM sms.phonebook;";
        $q=$this->db->query($sql) or die("<pre>$sql</pre>");
        $r=$q->fetch_assoc();
        return $r['COUNT(*)'];
    }

    public function head()
    {
        $htm=[];
        $htm[]="<head>";
        $htm[]="<title>".$this->title."</title>";

        $htm[]=$this->assets();

        $htm[]=$this->scripts();
        $htm[]=$this->autoreload();

        $htm[]="</head>";
        return implode('', $htm);
    }

    public function scripts()
    {
        $htm=[];

        //<!-- Latest compiled and minified JavaScript -->
        $htm[]="<script src='".$this->path."/js/jquery-1.11.0.min.js'></script>";
        $htm[]="<script src='".$this->path."/js/jquery.tablesorter.min.js'></script>";
        $htm[]="<script src='".$this->path."/bootstrap-3.1.1/js/bootstrap.min.js'></script>";

        return implode('', $htm);
    }



    /**
     * Force page reload after a given time in msec
     * @return [type] [description]
     */
    public function autoreload($delay = 60000)
    {
        $delay*=1;

        $JS=[];
        $JS[]="<script>";
        $JS[]="setTimeout(function(){console.log('reload');document.location.href=document.location.href;},$delay);";//ten minutes
        $JS[]="</script>";
        return implode("", $JS);
    }


    public function assets()
    {
        $htm=[];
        $htm[]="<link rel='shortcut icon' href='../favicon.png' type='image/png' />";
        // Latest compiled and minified CSS -->
        $htm[]="<link rel=stylesheet href='".$this->path."/bootstrap-3.1.1/css/bootstrap.min.css'>";

        // Optional theme -->
        $htm[]="<link rel=stylesheet href='".$this->path."/bootstrap-3.1.1/css/bootstrap-theme.min.css'>";
        $htm[]="<link rel=stylesheet href='".$this->path."/index.css'>";

        return implode('', $htm);
    }

    public function printPublic()
    {
        echo "<html>";
        echo $this->head();
        echo "<body>";
        echo $this->menu();
        echo "<div class='container'>";
    }
}
