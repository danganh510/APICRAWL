<?php
namespace Score\Utils;
use Phalcon\Mvc\User\Component;

class IpApi extends Component {
    static $fields = 65535;     // refer to http://ip-api.com/docs/api:returned_values#field_generator
    static $use_xcache = true;  // set this to false unless you have XCache installed (http://xcache.lighttpd.net/)
    static $api = "http://pro.ip-api.com/php/";
    static $key_ipapi = "?key=t6u34eCh1ztZrEm";

    public $status, $country, $countryCode, $region, $regionName, $city, $zip, $lat, $lon, $timezone, $isp, $org, $as, $reverse, $query, $message;


    /**
     * @return string[]
     */
    public static function getDbKeys() {
        return array('status', 'country', 'countryCode', 'region', 'regionName', 'city', 'zip', 'lat', 'lon', 'timezone', 'isp', 'org', 'as', 'reverse', 'query', 'message');
    }


    /**
     * @return string[]
     */
    public static function getPublicKeys() {
        return array('country', 'countryCode', 'region', 'regionName', 'city', 'zip', 'lat', 'lon', 'timezone', 'isp', 'org', 'as', 'reverse', 'query');
    }
    public static function curl_get_contents($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
    // Fix error - function file_get_contents no working
    public static function info_ip($ip) {
        $ip = trim((string)$ip);
        $result = new static;
        if (in_array($ip, array('127.0.0.1', '::1'))) {
            $result->status = "fail";
            $result->message = 'reserved range';
        }
        else {
            $ip = self::$api.$ip.self::$key_ipapi;
            $ip_info = self::curl_get_contents($ip);
            $data = @unserialize($ip_info);
            if ($data) {
                foreach($data as $key => $val) {
                    $result->$key = $val;
                }
            }
            else {
                //data is null
                $result->status = "fail";
                $result->message = 'Cannot connect to ip server!';
            }
        }

        return $result;
    }


    public static function query($q) {
        $data = self::communicate($q);
        $result = new static;
        foreach($data as $key => $val) {
            $result->$key = $val;
        }
        return $result;
    }

    private static function communicate($q) {
        $q_hash = md5('ipapi'.$q);
        if(self::$use_xcache && @xcache_isset($q_hash)) {
            return xcache_get($q_hash);
        }
        if(is_callable('curl_init')) {
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, self::$api.$q.'?fields='.self::$fields);
            curl_setopt($c, CURLOPT_HEADER, false);
            curl_setopt($c, CURLOPT_TIMEOUT, 30);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            $result_array = unserialize(curl_exec($c));
            curl_close($c);
        } else {
            $result_array = unserialize(file_get_contents(self::$api.$q.'?fields='.self::$fields));
        }
        if(self::$use_xcache) {
            xcache_set($q_hash, $result_array, 86400);
        }
        return $result_array;
    }
}

