<?php
/**
 * Created by MrDevNET.
 * User: mrdevnet
 * Date: 8/26/2018 AD
 * Time: 5:02 PM
 */


class ShareInfo
{
    function getOS(){
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;
        $OS = array(
            "Windows 10" => "/Windows nt 10/i",
            "Windows 8.1" => "/Windows nt 6.3/i",
            "Windows 8" => "/Windows nt 6.2/i",
            "Windows 7" => "/Windows nt 6.1/i",
            "Windows Vista" => "/Windows nt 6.0/i",
            "Windows XP" => "/Windows nt 5.1/i",
            "Mac OS X" => "/macintosh|mac os x/i",
            "Mac OS 9" => "/mac_powerpc/i",
            "Ubuntu" => "/ubuntu/i",
            "iPhone" => "/iphone/i",
            "iPod" => "/ipod/i",
            "iPad" => "/ipad/i",
            "Android" => "/android/i",
            "BlackBerry" => "/blackberry/i",
            "Mobile" => "/webos/i",
            "Linux" => "/Linux/i",
            "Unix" => "/Unix/i",
            "Mac OS" => "/Mac/i"
        );
        $info = array();
        foreach ($OS as $key => $value) {
            if (preg_match($value, $agent)) {
                $info = array_merge($info, array("os" => $key));
                break;
            }
        }
        return $info['os'];
    }

    function getDevice(){

        $detect = new \MobileDetect_YBAI();
        return ($detect->isMobile() ? ($detect->isTablet() ? 'Tablet' : 'Mobile') : 'Desktop');
    }

    function getCountry(){
        $ip = $_SERVER['REMOTE_ADDR'];
        $xml = simplexml_load_file("http://ip-api.com/xml/".$ip);
        return $xml->countryCode?$xml->countryCode[0]->__toString():'VN';
    }
}