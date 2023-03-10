<?php

namespace Score\Api\Controllers;


use Phalcon\Http\Response;
use Phalcon\Mvc\Controller;

/**
 * @property \GlobalVariable globalVariable
 * @property \My my
 */
class ControllerBase extends Controller
{
    public  $requestParams;
    public $payload = [];
    public function initialize()
    {
        header("Content-Type: text/plain; charset=UTF-8");
        header('Connection: keep-alive');
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Credentials: true");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Max-Age: 86400');
        header('Access-Control-Allow-Headers: *');

       
        // var_dump($headerBear);
        // exit;
        $this->requestParams = $_GET;

        // try {
        //     $jwt = file_get_contents("php://input");
        //     // $decoded = JWT::decode($jwt, $keys, self::acceptedAlgs());
        //     $decoded = $jwt;
        //     $this->requestParams = (array)$decoded;
        // } catch (\Exception $e) {
        //     $response = new Response();
        //     $response->setStatusCode(204, 'Unauthorized');
        //     $response->setContent($e->getMessage());

        //     return $response;
        // }
    }
    public function beforeExecuteRoute($dispatcher)
    {

        $headerBear = $this->getAuthorizationHeader();
        
        if ($headerBear != "Beaer ".BEAR_TOKEN) {
            
            $payload = [
                'status' => false,
                'code' => 401,
                'message' => "Unauthorized"
            ];
            $response = new Response();
            $response->setContent(json_encode($payload));
            return $response;
            
        }
    }

    public function afterExecuteRoute($dispatcher)
    {
        $res = $dispatcher->getReturnedValue();
        if (is_null($res)) return;
        $dispatcher->setReturnedValue([]);
        // $res = JWT::encode($res, $this->apiKey, self::algById($this->apiKid), $this->apiKid);
        $res = json_encode($res, JSON_PRETTY_PRINT);

        $this->response->setContent($res);
        return $this->response->send();
    }
    /** 
     * Get header Authorization
     * */
    function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    /**
     * get access token from header
     * */
    function getBearerToken()
    {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
    function create_slug($string)
    {
        $search = array(
            '#(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)#',
            '#(??|??|???|???|???|??|???|???|???|???|???)#',
            '#(??|??|???|???|??)#',
            '#(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)#',
            '#(??|??|???|???|??|??|???|???|???|???|???)#',
            '#(???|??|???|???|???)#',
            '#(??)#',
            '#(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)#',
            '#(??|??|???|???|???|??|???|???|???|???|???)#',
            '#(??|??|???|???|??)#',
            '#(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)#',
            '#(??|??|???|???|??|??|???|???|???|???|???)#',
            '#(???|??|???|???|???)#',
            '#(??)#',
            "/[^a-zA-Z0-9\-\_]/",
        );
        $replace = array(
            'a',
            'e',
            'i',
            'o',
            'u',
            'y',
            'd',
            'A',
            'E',
            'I',
            'O',
            'U',
            'Y',
            'D',
            '-',
        );
        $string = preg_replace($search, $replace, $string);
        $string = preg_replace('/(-)+/', '-', $string);
        $string = strtolower($string);
        return $string;
    }
}
