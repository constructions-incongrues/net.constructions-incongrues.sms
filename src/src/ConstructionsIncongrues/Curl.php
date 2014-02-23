<?php

namespace ConstructionsIncongrues;

// https bug ->
// http://stackoverflow.com/questions/316099/cant-connect-to-https-site-using-curl-returns-0-length-content-instead-what-c
// http://curl.haxx.se/ca/cacert.pem
// curl_setopt ($curl_ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");

/**
 * Class cURL
 * http://fr2.php.net/curl
 */
class Curl
{
    public $headers;
    public $user_agent;
    public $compression;
    public $httpCode;
    public $content_type;
    public $content_length;
    public $cookie_file;
    public $proxy;

    public function __construct($cookies = true, $cookie = 'cookies.txt', $compression = 'gzip', $proxy = '')
    {
        $this->headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
        $this->headers[] = 'Connection: Keep-Alive';
        $this->headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
        $this->user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';
        $this->compression=$compression;
        $this->proxy=$proxy;
        $this->cookies=$cookies;
        if ($this->cookies == true) {
            $this->cookie($cookie);
        }
    }


    public function cookie($cookie_file)
    {
        if (file_exists($cookie_file)) {
            $this->cookie_file=$cookie_file;
        } else {
            fopen($cookie_file,'w') or $this->error('The cookie file could not be opened. Make sure this directory has the correct permissions');
            $this->cookie_file=$cookie_file;
            fclose($this->cookie_file);
        }
    }


    function get($url)
    {
        $process = curl_init($url);
        curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
        if ($this->cookies == true) {
            curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
        }
        if ($this->cookies == true) {
            curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
        }
        curl_setopt($process, CURLOPT_ENCODING, $this->compression);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        if ($this->proxy) {
            curl_setopt($process, CURLOPT_PROXY, $this->proxy);
        }
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($process, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
        $return = curl_exec($process);

        $error  = curl_error($process);
        if($error) {
            echo "curl_error :: $error\n";
        }

        $this->httpCode = curl_getinfo($process, CURLINFO_HTTP_CODE);
        $this->content_type = curl_getinfo($process, CURLINFO_CONTENT_TYPE); # get the content type
        $this->content_length = curl_getinfo($process, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        curl_close($process);
        return $return;
    }


    public function post($url, $data)
    {
        $process = curl_init($url);

        curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($process, CURLOPT_HEADER, 1);
        curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
        if ($this->cookies == true) {
            curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
        }
        if ($this->cookies == true) {
            curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
        }
        curl_setopt($process, CURLOPT_ENCODING, $this->compression);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        if ($this->proxy) {
            curl_setopt($process, CURLOPT_PROXY, $this->proxy);
        }
        curl_setopt($process, CURLOPT_POSTFIELDS, $data);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($process, CURLOPT_POST, 1);
        $return = curl_exec($process);

        $this->httpCode = curl_getinfo($process, CURLINFO_HTTP_CODE);
        $this->content_type = curl_getinfo($process, CURLINFO_CONTENT_TYPE); # get the content type
        $this->content_length = curl_getinfo($process, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        curl_close($process);
        return $return;
    }

    public function error($error)
    {
        echo "<center><div style='width:500px;border: 3px solid #FFEEFF; padding: 3px; background-color: #FFDDFF;font-family: verdana; font-size: 10px'><b>cURL Error</b><br>$error</div></center>";
        die;
    }

    public function httpCode()
    {
        return $this->httpCode;
    }

    public function contentType()
    {
        return $this->content_type;
    }

    public function contentLength()
    {
        return $this->content_length;
    }
}
