<?php

namespace Score\Repositories;

use Score\Models\ForexcecConfig;
use Phalcon\Mvc\User\Component;
use Score\Models\ScTournament;
use Symfony\Component\DomCrawler\Crawler;
use CloudflareBypass\CFCurlImpl;
use CloudflareBypass\Model\UAMOptions;
use DOMDocument;
use DOMXPath;
use Exception;

class MyRepo extends Component
{
    public static function replace_space($string)
    {
        return str_replace(" ", "", $string);
    }
    public static function create_slug($string)
    {
        $search = array(
            '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#',
            '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#',
            '#(ì|í|ị|ỉ|ĩ)#',
            '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#',
            '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#',
            '#(ỳ|ý|ỵ|ỷ|ỹ)#',
            '#(đ)#',
            '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#',
            '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#',
            '#(Ì|Í|Ị|Ỉ|Ĩ)#',
            '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#',
            '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#',
            '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#',
            '#(Đ)#',
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
    public static function saveText($text, $key)
    {
        $dir_test = __DIR__ . "/../test";
        if (!is_dir($dir_test)) {
            mkdir($dir_test);
        }
        $fp = fopen($dir_test . "/div_$key.html", 'w'); //mở file ở chế độ write-only
        fwrite($fp, $text);
        fclose($fp);
    }
    public static function getHtmlByPassCloudFalre($url)
    {
        // init curl object        
        $ch = curl_init();

        // I just copied and pasted the headers from the original request
        // then removed the cookies
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = array();
        $headers[] = 'Authority: www.sofascore.com';
        $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7';
        $headers[] = 'Accept-Language: en-US,en;q=0.9';
        $headers[] = 'Cache-Control: max-age=0';
        $headers[] = 'Cookie: _pbjs_userid_consent_data=3524755945110770; _gcl_au=1.1.982035052.1674806415; _scid=90534566-e777-456d-a9f8-74d783ae0068; _lr_env_src_ats=false; _cc_id=bec014456c119adb900a49451fbaae03; __gads=ID=fb8889d632559509:T=1674806414:S=ALNI_MZHWH3DTTCkVFQedzqlE5aE7HVRyQ; pbjs-unifiedid=^%^7B^%^22TDID^%^22^%^3A^%^2266a63c4c-f55e-4c29-a0f7-878bdd01f9a6^%^22^%^2C^%^22TDID_LOOKUP^%^22^%^3A^%^22TRUE^%^22^%^2C^%^22TDID_CREATED_AT^%^22^%^3A^%^222022-12-27T08^%^3A30^%^3A20^%^22^%^7D; _sctr=1^|1676566800000; _gid=GA1.2.961332601.1676796099; panoramaId_expiry=1677410419916; panoramaId=39e18c75f0dc90591adcf6bc5dea16d5393856547a2dd7d45589cae13bc0dab7; __gpi=UID=00000badd56f52b1:T=1674806414:RT=1676821329:S=ALNI_MYyF3UikZPqEXOvJ_0Jh1G7eTN98w; cto_bidid=w50HXF94QVZJN1I1TjRyRFdLczBPbG9xZ1BNa0preE1uamN4NUNJYVY0aDU1VUFwSmxBM0JWb2xkZG9yN1J0cTFwUGJyUUo3dnZrenB6TVlEQlJ0ZXo4WXhnMXhxTXpiQnpuNHExRlNDQUlyQ1pzRSUzRA; cto_bundle=vnDf919abiUyRmViUVNRcE9ONVJCTXFuZTlGcHFLVyUyRmJTJTJGSTBOMUFpRWxqVG84bHRzTDZ0ZjZsUk9mdDREbzg4aDlRbU5yUTJWTVNWWGpybGQ2NnVVVkN0aDNjeGV5Q3N1YnQlMkZ0MVp4bUhvbUE0UmkwVXVKbkhhYk1hQjJLTDd4M2lNMDExNDlNOXMlMkJ0TlozR3lFRVJiTlplcTB3JTNEJTNE; _ga_QH2YGS7BB4=GS1.1.1676821329.32.1.1676828591.0.0.0; _ga=GA1.1.1928180340.1674806416; FCNEC=^%^5B^%^5B^%^22AKsRol_AoiUsrfJEQvS9GvK-TQ4fscmVwI4sqqRx3hA6-mSRasbQrzgA466HvNRuw2-_MlgTikjIJbSayqnq09dPsaTdeOeSbYPZ1sNkAQ5NtbrQv65xGu0mxUB4guDQtu-X_NHNkvy-Rc_vPT6DEbOncyDoGaVc2Q^%^3D^%^3D^%^22^%^5D^%^2Cnull^%^2C^%^5B^%^5D^%^5D; _ga_HNQ9P9MGZR=GS1.1.1676821330.29.1.1676828611.34.0.0';
        $headers[] = 'Sec-Ch-Ua: ^^Chromium^^\";v=^^\"110^^\",';
        $headers[] = 'Sec-Ch-Ua-Mobile: ?0';
        $headers[] = 'Sec-Ch-Ua-Platform: ^^Windows^^\"\"';
        $headers[] = 'Sec-Fetch-Dest: document';
        $headers[] = 'Sec-Fetch-Mode: navigate';
        $headers[] = 'Sec-Fetch-Site: none';
        $headers[] = 'Sec-Fetch-User: ?1';
        $headers[] = 'Upgrade-Insecure-Requests: 1';
        $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        // also get the error and response code
        // $errors = curl_error($ch);
        // $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // dump the results
        return $result;
    }
    public static function  make_slug($string, $separator = '-')
    {
        $string = trim($string);
        $string = mb_strtolower($string, 'UTF-8');

        // Make alphanumeric (removes all other characters)
        // this makes the string safe especially when used as a part of a URL
        // this keeps latin characters and Persian characters as well
        $string = preg_replace("/[^a-z0-9_\s\-ءاآؤئبپتثجچحخدذرزژسشصضطظعغفقكکگلمنوهی]/u", '', $string);

        // Remove multiple dashes or whitespaces or underscores
        $string = preg_replace("/[\s\-_]+/", ' ', $string);

        // Convert whitespaces and underscore to the given separator
        $string = preg_replace("/[\s_]/", $separator, $string);

        return $string;
    }
}
