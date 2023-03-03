<?php

namespace Score\Repositories;

use Exception;
use Score\Models\ForexcecConfig;
use Phalcon\Mvc\User\Component;
use Score\Models\ScMatch;
use Symfony\Component\DomCrawler\Crawler;

class CrawlImage extends Component
{
    public static function getImage($url, $image_link)
    {

        $dir = __DIR__ . "/../../public";
        $temp = "";
        if (!file_exists($dir . $image_link)) {

            foreach (explode("/", $image_link) as $key =>  $folder) {
                if ($key == count(explode("/", $image_link)) - 1) {
                    break;
                }
                if ($key == 0) {
                    continue;
                }
                $temp .= "/" . $folder;
                if (!file_exists($dir . $temp)) {
                    mkdir($dir . $temp, 0777, true);
                }
            }
        }
        $file_image = $dir . $image_link . ".png";

        try {
            $context = stream_context_create(array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ),
            ));
            $content = file_get_contents($url, false, $context);
            $result_put_file =  file_put_contents($file_image, $content);

            if (!$result_put_file) {
                return $dataReturn = [
                    'uploadFiles' => "Can't put content image this team ",
                    'status' => "fail"
                ];
            }
        } catch (Exception $e) {
            return $dataReturn = [
                'uploadFiles' => "Can't get content image this team ",
                'status' => "fail"
            ];
        }
        return $dataReturn = [
            'uploadFiles' => $image_link,
            'status' => "success"
        ];
    }
}
