<?php

/**
 * Services are globally registered in this file
 */

use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\Router;
use Phalcon\DI\FactoryDefault;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Loader;
use Phalcon\Mvc\Model\Manager;


if (!defined('MyS3Key')) {
    define('MyS3Key', 'AKIAZPULICYWP6EU5M4F');
}
if (!defined('MyS3Secret')) {
    define('MyS3Secret', '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');
}

if (!defined('MyS3Bucket')) {
    define('MyS3Bucket', 'Score');
}
if (!defined('MyCloudFrontURL')) {
    define('MyCloudFrontURL', 'https://dovyy1zxit6rl.cloudfront.net/');
}
include __DIR__ . '/../vendor/autoload.php';

/**
 * Read configuration
 */
$config = include __DIR__ . "/config.php";
if (!defined('URL_SITE')) {
    define('URL_SITE', 'https://www.forexcec.com');
}
/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader = new Loader();
$loader->registerNamespaces(array(
    'General\Models' => __DIR__ . '/../apps/models/general/',
    'Score\Models' => __DIR__ . '/../apps/models/',
    'Score\Repositories' => [
    __DIR__ . '/../apps/repositories/',
    __DIR__ . '/../apps/repositories/Crawler/',
],
    'Score\Utils' => __DIR__ . '/../apps/library/Utils/'
));

$loader->registerDirs(
    array(
        __DIR__ . '/../apps/library/',
        __DIR__ . '/../apps/library/SMTP/'

    )
);
$loader->register();

/**
 * Cloud Flare Fix CUSTOMER IP
 */
function ip_in_range($ip, $range)
{
    if (strpos($range, '/') !== false) {
        // $range is in IP/NETMASK format
        list($range, $netmask) = explode('/', $range, 2);
        if (strpos($netmask, '.') !== false) {
            // $netmask is a 255.255.0.0 format
            $netmask = str_replace('*', '0', $netmask);
            $netmask_dec = ip2long($netmask);
            return ((ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec));
        } else {
            // $netmask is a CIDR size block
            // fix the range argument
            $x = explode('.', $range);
            while (count($x) < 4) $x[] = '0';
            list($a, $b, $c, $d) = $x;
            $range = sprintf("%u.%u.%u.%u", empty($a) ? '0' : $a, empty($b) ? '0' : $b, empty($c) ? '0' : $c, empty($d) ? '0' : $d);
            $range_dec = ip2long($range);
            $ip_dec = ip2long($ip);

            # Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
            #$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

            # Strategy 2 - Use math to create it
            $wildcard_dec = pow(2, (32 - $netmask)) - 1;
            $netmask_dec = ~$wildcard_dec;

            return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
        }
    } else {
        // range might be 255.255.*.* or 1.2.3.0-1.2.3.255
        if (strpos($range, '*') !== false) { // a.b.*.* format
            // Just convert to A-B format by setting * to 0 for A and 255 for B
            $lower = str_replace('*', '0', $range);
            $upper = str_replace('*', '255', $range);
            $range = "$lower-$upper";
        }

        if (strpos($range, '-') !== false) { // A-B format
            list($lower, $upper) = explode('-', $range, 2);
            $lower_dec = (float)sprintf("%u", ip2long($lower));
            $upper_dec = (float)sprintf("%u", ip2long($upper));
            $ip_dec = (float)sprintf("%u", ip2long($ip));
            return (($ip_dec >= $lower_dec) && ($ip_dec <= $upper_dec));
        }
        return false;
    }
}

if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    $cf_ip_ranges = array('204.93.240.0/24',
        '204.93.177.0/24',
        '199.27.128.0/21',
        '172.64.0.0/13',
        '173.245.48.0/20',
        '103.21.244.0/22',
        '103.22.200.0/22',
        '103.31.4.0/22',
        '104.16.0.0/12',
        '131.0.72.0/22',
        '141.101.64.0/18',
        '108.162.192.0/18',
        '190.93.240.0/20',
        '188.114.96.0/20',
        '197.234.240.0/22',
        '198.41.128.0/17',
        '162.158.0.0/15',
        '2400:cb00::/32',
        '2606:4700::/32',
        '2803:f800::/32',
        '2405:b500::/32',
        '2405:8100::/32',
        '2c0f:f248::/32',
        '2a06:98c0::/29');
    foreach ($cf_ip_ranges as $range) {
        if (ip_in_range($_SERVER['REMOTE_ADDR'], $range)) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
            break;
        }
    }
}

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di['db'] = function () use ($config) {
    return new DbAdapter(array(
        "host" => $config->database->host,
        "username" => $config->database->username,
        "password" => $config->database->password,
        "dbname" => $config->database->name,
        "schema" => $config->database->name,
        'charset' => $config->database->charset
    ));
};




/**
 * Registering a router
 */

$di['router'] = function () {
    $router = new Router(false);
    $router->removeExtraSlashes(true);
    $router->setDefaultModule("backend");

    //Set 404 paths
    $router->notFound(array(
        "module" => "backend",
        "controller" => "notfound",
        "action" => "index"
    ));

    $router->add("/", array(
        "module" => "backend",
        "controller" => "index",
        "action" => "index"
    ));
    $router->add("/crawler", array(
        "module" => "backend",
        "controller" => "crawler",
        "action" => "index"
    ));
    $router->add("/crawler-detail", array(
        "module" => "backend",
        "controller" => "crawlerdetail",
        "action" => "index"
    ));
    $router->add("/crawler-detail-live", array(
        "module" => "backend",
        "controller" => "crawlerdetaillive",
        "action" => "index"
    ));
    $router->add("/logout", array(
        "module" => "backend",
        "controller" => "login",
        "action" => "logout"
    ));
    $router->add("/accessdenied", array(
        "module" => "backend",
        "controller" => "index",
        "action" => "accessdenied"
    ));

    //   Page Controller
    $router->add('/list-page', array(
        "module" => "backend",
        "controller" => "page",
        "action" => "index"
    ));
    $router->add('/create-page', array(
        "module" => "backend",
        "controller" => "page",
        "action" => "create"
    ));
    $router->add('/edit-page', array(
        "module" => "backend",
        "controller" => "page",
        "action" => "edit"
    ));
    $router->add('/delete-page', array(
        "module" => "backend",
        "controller" => "page",
        "action" => "delete"
    ));
    $router->add('/insert-data-page', array(
        "module" => "backend",
        "controller" => "page",
        "action" => "insertdata"
    ));

    // Role Controller
    $router->add("/list-role", array(
        "module" => "backend",
        "controller" => "role",
        "action" => "index"
    ));
    $router->add("/create-role", array(
        "module" => "backend",
        "controller" => "role",
        "action" => "create"
    ));
    $router->add("/edit-role", array(
        "module" => "backend",
        "controller" => "role",
        "action" => "edit"
    ));
    $router->add("/delete-role", array(
        "module" => "backend",
        "controller" => "role",
        "action" => "delete"
    ));

    // Language Controller
    $router->add('/list-language', array(
        "module" => "backend",
        "controller" => 'language',
        "action" => "index"
    ));
    $router->add('/create-language', array(
        "module" => "backend",
        "controller" => 'language',
        "action" => "create"
    ));
    $router->add('/edit-language', array(
        "module" => "backend",
        "controller" => 'language',
        "action" => "edit"
    ));
    $router->add('/delete-language', array(
        "module" => "backend",
        "controller" => 'language',
        "action" => "delete"
    ));

    // Location Controller
    $router->add('/list-location', array(
        "module" => "backend",
        "controller" => 'location',
        "action" => "index"
    ));
    $router->add('/create-location', array(
        "module" => "backend",
        "controller" => 'location',
        "action" => "create"
    ));
    $router->add('/edit-location', array(
        "module" => "backend",
        "controller" => 'location',
        "action" => "edit"
    ));
    $router->add('/delete-location', array(
        "module" => "backend",
        "controller" => 'location',
        "action" => "delete"
    ));
    $router->add('/getlangbycode-location', array(
        "module" => "backend",
        "controller" => "location",
        "action" => "getlangbycode"
    ));

    // User controller
    $router->add('/list-user', array(
        "module" => "backend",
        "controller" => "user",
        "action" => "index"
    ));
    $router->add('/create-user', array(
        "module" => "backend",
        "controller" => "user",
        "action" => "create"
    ));
    $router->add('/view-user', array(
        "module" => "backend",
        "controller" => "user",
        "action" => "view"
    ));
    $router->add('/delete-user', array(
        "module" => "backend",
        "controller" => "user",
        "action" => "delete"
    ));
    $router->add('/password-user', array(
        "module" => "backend",
        "controller" => "user",
        "action" => "password"
    ));
    $router->add('/information-user', array(
        "module" => "backend",
        "controller" => "user",
        "action" => "information"
    ));
    $router->add('/role-user', array(
        "module" => "backend",
        "controller" => "user",
        "action" => "role"
    ));

    // partnership controller
    $router->add('/list-partnership', array(
        "module" => "backend",
        "controller" => "partnership",
        "action" => "index"
    ));
    $router->add('/view-partnership', array(
        "module" => "backend",
        "controller" => "partnership",
        "action" => "view"
    ));

    // Email template
    $router->add('/list-emailtemplate', array(
        "module" => "backend",
        "controller" => "emailtemplate",
        "action" => "index"
    ));
    $router->add('/create-emailtemplate', array(
        "module" => "backend",
        "controller" => "emailtemplate",
        "action" => "create"
    ));
    $router->add('/edit-emailtemplate', array(
        "module" => "backend",
        "controller" => "emailtemplate",
        "action" => "edit"
    ));
    $router->add('/delete-emailtemplate', array(
        "module" => "backend",
        "controller" => "emailtemplate",
        "action" => "delete"
    ));

    // Email Auto template
    $router->add('/list-template-auto-email', array(
        "module" => "backend",
        "controller" => "templateautoemail",
        "action" => "index"
    ));
    $router->add('/create-template-auto-email', array(
        "module" => "backend",
        "controller" => "templateautoemail",
        "action" => "create"
    ));
    $router->add('/edit-template-auto-email', array(
        "module" => "backend",
        "controller" => "templateautoemail",
        "action" => "edit"
    ));
    $router->add('/delete-template-auto-email', array(
        "module" => "backend",
        "controller" => "templateautoemail",
        "action" => "delete"
    ));

    // Upload File Controller
    $router->add('/cloud-upload', array(
        "module" => "backend",
        "controller" => 'cloudupload',
        "action" => 'index'
    ));
    $router->add('/dashboard/form-upload', array(
        "module" => "backend",
        "controller" => 'formupload',
        "action" => 'index'
    ));
    // Config Controller
    $router->add('/list-config', array(
        "module" => "backend",
        "controller" => "config",
        "action" => "index"
    ));
    $router->add('/create-config', array(
        "module" => "backend",
        "controller" => "config",
        "action" => "create"
    ));
    $router->add('/edit-config', array(
        "module" => "backend",
        "controller" => "config",
        "action" => "edit"
    ));
    $router->add('/delete-config', array(
        "module" => "backend",
        "controller" => "config",
        "action" => "delete"
    ));

    // Type Controller
    $router->add('/list-type', array(
        "module" => "backend",
        "controller" => "type",
        "action" => "index"
    ));
    $router->add('/create-type', array(
        "module" => "backend",
        "controller" => "type",
        "action" => "create"
    ));
    $router->add('/edit-type', array(
        "module" => "backend",
        "controller" => "type",
        "action" => "edit"
    ));
    $router->add('/delete-type', array(
        "module" => "backend",
        "controller" => "type",
        "action" => "delete"
    ));

    // controller  Article
    $router->add('/list-article', array(
        "module" => "backend",
        "controller" => "article",
        "action" => "index"
    ));
    $router->add('/create-article', array(
        "module" => "backend",
        "controller" => "article",
        "action" => "create"
    ));
    $router->add('/edit-article', array(
        "module" => "backend",
        "controller" => "article",
        "action" => "edit"
    ));
    $router->add('/delete-article', array(
        "module" => "backend",
        "controller" => "article",
        "action" => "delete"
    ));
    $router->add('/lession-article', array(
        "module" => "backend",
        "controller" => "article",
        "action" => "lession"
    ));
    $router->add('/replace-article', array(
        "module" => "backend",
        "controller" => "article",
        "action" => "replace"
    ));

    $router->add('/article/update-content', array(
        "module" => "backend",
        "controller" => "article",
        "action"	=> "updatecontent"
    ));


    //    ------------Banner Controller
    $router->add('/list-banner', array(
        "module" => "backend",
        "controller" => "banner",
        "action" => "index"
    ));
    $router->add('/create-banner', array(
        "module" => "backend",
        "controller" => "banner",
        "action" => "create"
    ));
    $router->add('/edit-banner', array(
        "module" => "backend",
        "controller" => "banner",
        "action" => "edit"
    ));
    $router->add('/delete-banner', array(
        "module" => "backend",
        "controller" => "banner",
        "action" => "delete"
    ));

    // Country Controller
    $router->add('/list-country', array(
        "module" => "backend",
        "controller" => "country",
        "action" => "index"
    ));
    $router->add('/create-country', array(
        "module" => "backend",
        "controller" => "country",
        "action" => "create"
    ));
    $router->add('/edit-country', array(
        "module" => "backend",
        "controller" => "country",
        "action" => "edit"
    ));
    $router->add('/delete-country', array(
        "module" => "backend",
        "controller" => "country",
        "action" => "delete"
    ));

    // controller glossary
    $router->add('/list-glossary', array(
        "module" => "backend",
        "controller" => "glossary",
        "action" => "index"
    ));
    $router->add('/create-glossary', array(
        "module" => "backend",
        "controller" => "glossary",
        "action" => "create"
    ));
    $router->add('/edit-glossary', array(
        "module" => "backend",
        "controller" => "glossary",
        "action" => "edit"
    ));
    $router->add('/delete-glossary', array(
        "module" => "backend",
        "controller" => "glossary",
        "action" => "delete"
    ));

    // controller office
    $router->add('/list-office', array(
        "module" => "backend",
        "controller" => "office",
        "action" => "index"
    ));
    $router->add('/create-office', array(
        "module" => "backend",
        "controller" => "office",
        "action" => "create"
    ));
    $router->add('/edit-office', array(
        "module" => "backend",
        "controller" => "office",
        "action" => "edit"
    ));
    $router->add('/delete-office', array(
        "module" => "backend",
        "controller" => "office",
        "action" => "delete"
    ));

    // Communication Channel
    $router->add('/list-communication-channel', array(
        "module" => "backend",
        "controller" => "communicationchannel",
        "action"	=> "index"
    ));
    $router->add('/create-communication-channel', array(
        "module" => "backend",
        "controller" => "communicationchannel",
        "action"	=> "create"
    ));
    $router->add('/edit-communication-channel', array(
        "module" => "backend",
        "controller" => "communicationchannel",
        "action"	=> "edit"
    ));
    $router->add('/delete-communication-channel', array(
        "module" => "backend",
        "controller" => "communicationchannel",
        "action"	=> "delete"
    ));
    $router->add('/country-communication-channel', array(
        "module" => "backend",
        "controller" => 'communicationchannel',
        "action"	=> "country"
    ));

      $router->add('/list-leadform', array(
        "module" => "backend",
        "controller" => "leadform",
        "action" => "index"
    ));
    $router->add('/view-leadform', array(
        "module" => "backend",
        "controller" => "leadform",
        "action" => "view"
    ));

    $router->add('/list-lw-user', array(
        "module" => "backend",
        "controller" => "listlwuser",
        "action" => "index"
    ));
    $router->add('/view-lw-user', array(
        "module" => "backend",
        "controller" => "listlwuser",
        "action" => "view"
    ));
    
    $router->add('/list-contactus', array(
        "module" => "backend",
        "controller" => "contactus",
        "action" => "index"
    ));
    $router->add('/view-contactus', array(
        "module" => "backend",
        "controller" => "contactus",
        "action" => "view"
    ));
    $router->add("/delete-cache-tool", array(
        "module" => "backend",
        "controller" => "deletecachetool",
        "action" => "index"
    ));
    $router->add("/delete-all-cache", array(
        "module" => "backend",
        "controller" => "deletecachetool",
        "action" => "deleteallcache"
    ));
    $router->add("/delete-footer-cache", array(
        "module" => "backend",
        "controller" => "deletecachetool",
        "action" => "deletefootercache"
    ));
    $router->add("/delete-register-cache", array(
        "module" => "backend",
        "controller" => "deletecachetool",
        "action" => "deleteregistercache"
    ));
    $router->add("/delete-promotion-cache", array(
        "module" => "backend",
        "controller" => "deletecachetool",
        "action" => "deletepromotioncache"
    ));

    $router->add("/cron/translate", array(
        "module"	=> "backend",
        "controller" => "cron",
        "action"     => "translatedata"
    ));

    //cron dashboard
    $router->add("/list-cron", array(
        "module" => "backend",
        "controller" => "listcron",
        "action" => "index"
    ));
    $router->add("/detail-cron", array(
        "module" => "backend",
        "controller" => "listcron",
        "action" => "detail"
    ));

    //translate dashboard
    $router->add("/list-translate", array(
        "module" => "backend",
        "controller" => "translate",
        "action" => "index"
    ));
    $router->add("/create-translate", array(
        "module" => "backend",
        "controller" => "translate",
        "action" => "create"
    ));
    $router->add("/edit-translate", array(
        "module" => "backend",
        "controller" => "translate",
        "action" => "edit"
    ));
    $router->add("/delete-translate", array(
        "module" => "backend",
        "controller" => "translate",
        "action" => "delete"
    ));
    $router->add("/tool-translate", array(
        "module" => "backend",
        "controller" => "tooltranslate",
        "action" => "index"
    ));
    $router->add("/tool-translate-table", array(
        "module" => "backend",
        "controller" => "tooltranslatetable",
        "action" => "index"
    ));
    $router->add("/get-column-table", array(
        "module" => "backend",
        "controller" => "tooltranslatetable",
        "action" => "getcolumntable"
    ));
    $router->add("/copy-data", array(
        "module" => "backend",
        "controller" => "copydata",
        "action" => "index"
    ));
    $router->add("/insert-data-translate", array(
        "module" => "backend",
        "controller" => "copydata",
        "action" => "inserttranslate"
    ));
    //translate dashboard
    $router->add("/list-make-payment", array(
        "module" => "backend",
        "controller" => "payment",
        "action" => "index"
    ));
    $router->add("/detail-make-payment", array(
        "module" => "backend",
        "controller" => "payment",
        "action" => "detail"
    ));
    $router->add("/list-translate-history", array(
        "module" => "backend",
        "controller" => "translatehistory",
        "action" => "index"
    ));
    $router->add("/view-translate-history", array(
        "module" => "backend",
        "controller" => "translatehistory",
        "action" => "view"
    ));

    // Register page Controller
    $router->add('/list-register-page', array(
        "module" => "backend",
        "controller" => 'registerpage',
        "action" => "index"
    ));
    $router->add('/create-register-page', array(
        "module" => "backend",
        "controller" => 'registerpage',
        "action" => "create"
    ));
    $router->add('/edit-register-page', array(
        "module" => "backend",
        "controller" => 'registerpage',
        "action" => "edit"
    ));
    $router->add('/delete-register-page', array(
        "module" => "backend",
        "controller" => 'registerpage',
        "action" => "delete"
    ));
    $router->add('/cron/update-user-forexcec', array(
        "module" => "backend",
        "controller" => 'cron',
        "action" => "updateuserlwapi"
    ));
    $router->add('/cron/send-email-auto', array(
        "module" => "backend",
        "controller" => 'cron',
        "action" => "sendemailauto"
    ));
    $router->add('/cron/report-email-auto', array(
        "module" => "backend",
        "controller" => 'cron',
        "action" => "reportemailsent"
    ));
    $router->add('/cron/delete-email', array(
        "module" => "backend",
        "controller" => 'cron',
        "action" => "deleteEmailExist"
    ));

    $router->add('/list-email-log', array(
        "module" => "backend",
        "controller" => 'sentemaillog',
        "action" => "index"
    ));
    $router->add('/view-email-log', array(
        "module" => "backend",
        "controller" => 'sentemaillog',
        "action" => "view"
    ));
    $router->add('/change-is-subscribe', array(
        "module" => "backend",
        "controller" => 'sentemaillog',
        "action" => "changeissubscribe"
    ));

    $router->add('/get-list-match', array(
        "module" => "api",
        "controller" => 'match',
        "action" => "list"
    ));

    $router->add('/get-match', array(
        "module" => "api",
        "controller" => 'match',
        "action" => "detail"
    ));

    $router->handle();
    return $router;
};

/**
 * Start the session the first time some component request the session service
 */
$di['session'] = function () {
    $session = new SessionAdapter();
    $session->start();
    return $session;
};
/**
 * Register My component
 */
$di->set('my', function () {
    return new \My();
});

/**
 * Register GlobalVariable component
 */
$di->set('globalVariable', function () {
    return new \GlobalVariable();
});

/**
 * Register cookie
 */
$di->set('cookies', function () {
    $cookies = new \Phalcon\Http\Response\Cookies();
    $cookies->useEncryption(false);
    return $cookies;
}, true);

/**
 * Register key for cookie encryption
 */
$di->set('crypt', function () {
    $crypt = new \Phalcon\Crypt();
    $crypt->setKey('binmedia123@@##'); //Use your own key!
    return $crypt;
});

/**
 * Register models manager
 */
$di->set('modelsManager', function () {
    return new Manager();
});

/**
 * Register PHPMailer manager
 */
// $di->set('myMailer', function () {
//     require_once(__DIR__ . "/../apps/library/SMTP/class.phpmailer.php");
//     $mail = new \PHPMailer();
//     $mail->IsSMTP();//telling the class to use SMTP
//     $mail->SMTPAuth = true;
//     $mail->SMTPSecure = "tls";
//     $mail->Host = "email-smtp.us-west-2.amazonaws.com";
//     $mail->Username = "AKIAZPULICYWJ7UI6GUZ";
//     $mail->Password = "BNr0S+jBNdlDm0RtmJIIN8L1dFia0Y5r1R9Sk6Xg2a9r";
//     $mail->CharSet = 'utf-8';
//     return $mail;
// });
