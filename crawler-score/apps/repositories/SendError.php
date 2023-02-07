<?php

namespace Score\Repositories;

use Phalcon\Mvc\User\Component;

class SendError extends Component
{
    /**
     * @property \GlobalVariable globalVariable
     * @property \My my
     */
	public function sendErrorNotfound($message)
	{
		/*********** Sent Error When 404 *************/
				//$this->response->setStatusCode(404, "Not Found");
				//$this->tag->setTitle('Not found - 404');
				//Add 27 - 11
				$message = "URL Notfound : " . $message . $_SERVER['REQUEST_URI']."<br>";
				$message .= "=================================== USER AGENT ======================================================<br>";
				$message .= $_SERVER['HTTP_USER_AGENT'];
				// All Info User
				$indicesServer = array('PHP_SELF', 
				'argv', 
				'argc', 
				'GATEWAY_INTERFACE', 
				'SERVER_ADDR', 
				'SERVER_NAME', 
				'SERVER_SOFTWARE', 
				'SERVER_PROTOCOL', 
				'REQUEST_METHOD', 
				'REQUEST_TIME', 
				'REQUEST_TIME_FLOAT', 
				'QUERY_STRING', 
				'DOCUMENT_ROOT', 
				'HTTP_ACCEPT', 
				'HTTP_ACCEPT_CHARSET', 
				'HTTP_ACCEPT_ENCODING', 
				'HTTP_ACCEPT_LANGUAGE', 
				'HTTP_CONNECTION', 
				'HTTP_HOST', 
				'HTTP_REFERER', 
				'HTTP_USER_AGENT', 
				'HTTPS', 
				'REMOTE_ADDR', 
				'REMOTE_HOST', 
				'REMOTE_PORT', 
				'REMOTE_USER', 
				'REDIRECT_REMOTE_USER', 
				'SCRIPT_FILENAME', 
				'SERVER_ADMIN', 
				'SERVER_PORT', 
				'SERVER_SIGNATURE', 
				'PATH_TRANSLATED', 
				'SCRIPT_NAME', 
				'REQUEST_URI', 
				'PHP_AUTH_DIGEST', 
				'PHP_AUTH_USER', 
				'PHP_AUTH_PW', 
				'AUTH_TYPE', 
				'PATH_INFO', 
				'ORIG_PATH_INFO') ; 
			
				echo '<table cellpadding="10">' ; 
				foreach ($indicesServer as $arg) { 
					if (isset($_SERVER[$arg])) {
					    if (is_array($_SERVER[$arg])) {
                            $message .= '<tr><td>'.$arg.'</td><td>' . json_encode($_SERVER[$arg]) . '</td></tr>' ;
                        } else {
                            $message .= '<tr><td>'.$arg.'</td><td>' . $_SERVER[$arg] . '</td></tr>' ;
                        }
					} 
					else { 
						 $message .= '<tr><td>'.$arg.'</td><td>-</td></tr>' ; 
					} 
				} 
				echo '</table>' ;
				//get url current of user
				$url_current =  $_SERVER['REQUEST_URI'];
				
				if($url_current == "notfound/index" || $url_current == "notfound")
				{
                    if(!isset($_COOKIE['origin_ref']))
                    {
                        setcookie('origin_ref', $_SERVER['HTTP_REFERER']);
                    }
//
				}else
				{
					$title = "URL NOTFOUND";
					$this->my->sendError($message,$title);
					//Add 27 - 11
				}		
	}
	public function senErrorExeption($e)
	{
    $message = get_class($e) . ": " . $e->getMessage() . "<br>";
    $message .= " File = " . $e->getFile() . "<br>";
    $message .= " Line = ". $e->getLine() . "<br>";
    $message .= " Code = ". $e->getCode() . "<br>";
    $message .= $e->getTraceAsString(). "<br>";
	$message .= "=================================== USER AGENT ======================================================<br>";
	$message .= $_SERVER['HTTP_USER_AGENT'];
	// All Info User
	$indicesServer = array('PHP_SELF', 
	'argv', 
	'argc', 
	'GATEWAY_INTERFACE', 
	'SERVER_ADDR', 
	'SERVER_NAME', 
	'SERVER_SOFTWARE', 
	'SERVER_PROTOCOL', 
	'REQUEST_METHOD', 
	'REQUEST_TIME', 
	'REQUEST_TIME_FLOAT', 
	'QUERY_STRING', 
	'DOCUMENT_ROOT', 
	'HTTP_ACCEPT', 
	'HTTP_ACCEPT_CHARSET', 
	'HTTP_ACCEPT_ENCODING', 
	'HTTP_ACCEPT_LANGUAGE', 
	'HTTP_CONNECTION', 
	'HTTP_HOST', 
	'HTTP_REFERER', 
	'HTTP_USER_AGENT', 
	'HTTPS', 
	'REMOTE_ADDR', 
	'REMOTE_HOST', 
	'REMOTE_PORT', 
	'REMOTE_USER', 
	'REDIRECT_REMOTE_USER', 
	'SCRIPT_FILENAME', 
	'SERVER_ADMIN', 
	'SERVER_PORT', 
	'SERVER_SIGNATURE', 
	'PATH_TRANSLATED', 
	'SCRIPT_NAME', 
	'REQUEST_URI', 
	'PHP_AUTH_DIGEST', 
	'PHP_AUTH_USER', 
	'PHP_AUTH_PW', 
	'AUTH_TYPE', 
	'PATH_INFO', 
	'ORIG_PATH_INFO') ; 

	echo '<table cellpadding="10">' ; 
	foreach ($indicesServer as $arg) { 
	    if (isset($_SERVER[$arg])) { 
	        $message .= '<tr><td>'.$arg.'</td><td>' . $_SERVER[$arg] . '</td></tr>' ; 
	    } 
	    else { 
	         $message .= '<tr><td>'.$arg.'</td><td>-</td></tr>' ; 
	    } 
	} 
	echo '</table>' ;
    require __DIR__ . '../library/My.php';
    $repoMy = new My();
    $result = $repoMy->sendError($message);
	}
	
}



