<?php

namespace Score\Google;

use General\Models\LanguageSupportTranslate;
use Google\Cloud\Translate\V3\GcsSource;
use Google\Cloud\Translate\V3\Glossary;
use Google\Cloud\Translate\V3\Glossary\LanguageCodesSet;
use Google\Cloud\Translate\V3\GlossaryInputConfig;
use Google\Cloud\Translate\V3\TranslateTextGlossaryConfig;
use Google\Cloud\Translate\V3\TranslationServiceClient;
use Score\Models\ForexcecTranslateHistory;
use Phalcon\Mvc\User\Component;

class GoogleTranslate
{
    const LIMIT_CUT_CONTENT = 30000;
    /*private $projectId = '503817953383';
    private $bucketName = 'forexcectranslations';
    private $fileNameGlosary = 'Scoreglossary.csv';
    private $locationForGlosary = 'us-central1';
    private $locationFormat = 'global';
    private $glosaryIdPrefix = 'sc_glossary_';*/
    private $projectId = '250414336140';
    private $bucketName = 'forexcectranslations';
    private $fileNameGlosary = 'Scoreglossary.csv';
    private $locationForGlosary = 'us-central1';
    private $locationFormat = 'global';
    private $glosaryIdPrefix = 'sc_glossary_';
    private $glossary_id = '';
    private $site_translate = 'admin.forexcec.com';

    public function setGlossaryId ($source='en',$tran_lang_code){
        $this->glossary_id = $this->glosaryIdPrefix.$tran_lang_code;
        if ($source != 'en'){
            $this->glossary_id = $this->glosaryIdPrefix.$source.'_'.$tran_lang_code;
        }

        /*----- create all glossaries -----
        $repoLang = new LanguageSupportTranslate();
        $data = $repoLang->getAll();
        foreach ($data->toArray() as $key => $item) {
            if ($item['language_code'] != 'en') {
                //$this->createGlossary('en',$item['language_code']);
               // $this->deleteGlossary($this->glosaryIdPrefix.$item['language_code']);
            }
        }*/



    }
    public function translate($data, $target, $format = 'text/html', $source = 'en',$table='',$record_history_id=null) {

        ini_set('max_execution_time', 60);
        if (defined('TRANSLATE_CLOUD_MODE') && TRANSLATE_CLOUD_MODE) {
            $count_data_lengh = strlen($data);
            $delay_time = self::process_delay_time($count_data_lengh);
            if($count_data_lengh > self::LIMIT_CUT_CONTENT){
                $arr_result = $this->translateMutliContent($data,$target,$delay_time,$format,$source,$table,$record_history_id);
            }else{
                $arr_result = $this->translateByApiSubtring($data,$target,$delay_time,$format,$source,$table,$record_history_id);
            }
            return $arr_result;
        } else {
            return array(
                "status" => "true",
                "data"   => $data
            );
        }
    }
    private function translateByApiSubtring($data,$target,$delay_time,$format,$source,$table,$record_history_id){
        if(strlen($data)<2 || is_numeric($data) || $data=="<p>&#160;</p>" || $data=="<p></p>" || $data == "gx"){
            $arr_result = array(
                "status"       => "true",
                "errorcode"    => '',
                "errormessage" => '',
                "responsecode" => '',
                "data"         => $data
            );
            return $arr_result;
        }
        $response_data = '';
        $message_json = array();
        $translationClient = new TranslationServiceClient();
        $projectId = $this->projectId;
        $glossaryPath = $translationClient->glossaryName(
            $projectId,
            $this->locationForGlosary,
            $this->glossary_id
        );
        $formattedParent = $translationClient->locationName(
            $projectId,
            $this->locationForGlosary
        );
        $glossaryConfig = new TranslateTextGlossaryConfig();
        $glossaryConfig->setGlossary($glossaryPath);
        try {
            $response = $translationClient->translateText(
                [$data],
                $target,
                $formattedParent,
                [
                    'sourceLanguageCode' => $source,
                    'glossaryConfig' => $glossaryConfig,
                    'mimeType' => $format
                ]
            );
            foreach ($response->getGlossaryTranslations() as $translation) {
                $response_data .= $translation->getTranslatedText();
            }
            sleep($delay_time);
        } catch (\Exception $e) {
            $message_json = (array)json_decode($e->getMessage());
        } finally {
            $translationClient->close();
        }
//        if(strlen($glossary_id) > 0){
//
//        }else{
//            $formattedParent = $translationClient->locationName($projectId, 'global');
//            try {
//                $response = $translationClient->translateText(
//                    [$data],
//                    $target,
//                    $formattedParent
//                );
//                foreach ($response->getTranslations() as $translation) {
//                    $response_data .= $translation->getTranslatedText();
//                }
//            } finally {
//                $translationClient->close();
//            }
//        }
        if(count($message_json)>0){
            $arr_result = array(
                "status"       => "fail",
                "errorcode"    => $message_json['code'],
                "errormessage" => $message_json['message'],
                "responsecode" => $message_json['code'],
                "data"         => ''
            );
            $this->saveTranslateHistory($data,$format,$source,$target,ForexcecTranslateHistory::STATUS_FAIL,$this->site_translate,$table,$record_history_id,json_encode($message_json));
        }else{
            $arr_result = array(
                "status"       => "true",
                "errorcode"    => '',
                "errormessage" => '',
                "responsecode" => '',
                "data"         => $response_data
            );
            $this->saveTranslateHistory($data, $format, $source, $target,ForexcecTranslateHistory::STATUS_SUCCESS, $this->site_translate, $table, $record_history_id);
        }
        return $arr_result;
    }
    private function saveTranslateHistory($data_source,$format,$source_lang_code,$target_lang_code,$status,$site,$table,$record_history_id,$message=null){
        $globalVariable = new \GlobalVariable();
        $new_record = new ForexcecTranslateHistory();
        $new_record->setHistorySite($site);
        $new_record->setHistoryTable($table);
        $new_record->setHistoryRecordId($record_history_id);
        $new_record->setHistorySourceLangCode($source_lang_code);
        $new_record->setHistoryTargetLangCode($target_lang_code);
        $new_record->setHistoryFormat($format);
        $new_record->setHistoryStatus($status);
        $new_record->setHistoryDataSource($data_source);
        $new_record->setHistoryInsertTime($globalVariable->curTime);
        $new_record->setHistoryMessage($message);
        $new_record->save();
    }
    private function cutText1($text,$html)
    {
        $temp = '';
        $result = [];
        $resultTmp = [];
        while(strpos($text, $html) !== false) {
            $pos =  strpos($text, $html);
            $pos += strlen($html);
            $resultTmp[]=substr($text, 0, $pos);
            $text = substr($text, $pos);
        }
        if (strlen($text) > 0) {
            $resultTmp[] = $text;
        }
        foreach ($resultTmp as $item) {
            if (strlen($temp) + strlen($item) < self::LIMIT_CUT_CONTENT) {
                $temp .= $item;
            } else {
                $result[] = $temp;
                $temp = $item;
            }
        }
        if (strlen($temp) > 0) {
            $result[] = $temp;
        }
        return $result;
    }
    public function cutText($text)
    {
        $result = array();
        $array_character = self::cutText1($text, "</p>");
        foreach ($array_character as $key => $content_item) {
            if (strlen($content_item) > self::LIMIT_CUT_CONTENT) {
                $array_character_td = self::cutText1($content_item, "</td>");
                foreach ($array_character_td as $item) {
                    array_push($result, $item);
                }
            } else {
                array_push($result, $content_item);
            }
        }
        return $result;
    }
    private function translateMutliContent($content,$code_lang,$delay_time,$format,$source,$table,$record_history_id)
    {
        $message_error = "";
        $array_character = array();
        $is_check = true;
        $status = "true";

        $array_character = self::cutText($content);
        $content_translate="";
        foreach ($array_character as $content_item)
        {
            if (strlen($content_item) > self::LIMIT_CUT_CONTENT){
                $responsecode = '{ "@type": 0, "data": [ { "field": "contents", "description": "The total codepoints in the request must be less than 30720, actual: 32415" } ] } ] }';
                $message_error .= " Content  = 3 - Text is too long" . $responsecode . "<br>";
                $is_check = false;
                $status = "fail";
                $this->saveTranslateHistory($content_item,$format,$source,$code_lang,ForexcecTranslateHistory::STATUS_FAIL,$this->site_translate,$table,$record_history_id,json_encode(array("status" => "fail", "message" => "The total codepoints in the request must be less than 30720, actual: 32415")));
                break;
            }
            $ar_content = $this->translateByApiSubtring($content_item,$code_lang,$delay_time,$format,$source,$table,$record_history_id);
            if ($ar_content["status"] == "fail") {
                $message_error .= " Content  = " . $ar_content["errorcode"] . " - " . $ar_content["errormessage"] . $ar_content["responsecode"] . "<br>";
                $is_check = false;
                $status = "fail";
                break;
            }
            if($is_check)
            {
                $content_translate .= $ar_content["data"];
            }
        }
        $ar_status = array(
            "status"       => $status,
            "errorcode"    => '',
            "errormessage" => $message_error,
            "responsecode" => '',
            "data"         => $content_translate
        );
        return $ar_status;
    }
    private function process_delay_time($string_lenght)
    {
        $result = 0;
        if($string_lenght >= 25000)
        {
            $result = 6;
        }
        else if (15000 <= $string_lenght && $string_lenght < 25000)
        {
            $result = 5;
        }
        else if (10000 <= $string_lenght && $string_lenght < 15000)
        {
            $result = 2;

        }
        else if ($string_lenght < 10000 )
        {
            $result = 1;
        }
        return  $result ;
    }
    public function listLanguage($target){
        $arr_code_result = array();
        $translationServiceClient = new TranslationServiceClient();
        $formattedParent = $translationServiceClient->locationName($this->projectId, 'us-central1');
        try {
            $response = $translationServiceClient->getSupportedLanguages(
                $formattedParent,
                ['displayLanguageCode' => $target]
            );
            foreach ($response->getLanguages() as $language) {
                $arr_code_result[$language->getLanguageCode()] = $language->getDisplayName();
            }
        } finally {
            $translationServiceClient->close();
        }
        return $arr_code_result;
    }
    public function createGlossary($languageCodesElement = 'en',$languageCodesElement2){
        $translationServiceClient = new TranslationServiceClient();
        $projectId = $this->projectId;
        $glossaryId = $this->glosaryIdPrefix.$languageCodesElement2;
        if ($languageCodesElement != 'en'){
            $glossaryId = $this->glosaryIdPrefix.$languageCodesElement.'_'.$languageCodesElement2;
        }
        $inputUri = 'gs://'.$this->bucketName.'/'.$this->fileNameGlosary;
        $formattedParent = $translationServiceClient->locationName(
            $projectId,
            $this->locationForGlosary
        );
        $formattedName = $translationServiceClient->glossaryName(
            $projectId,
            $this->locationForGlosary,
            $glossaryId
        );
        $languageCodes = [$languageCodesElement, $languageCodesElement2];
        $languageCodesSet = new LanguageCodesSet();
        $languageCodesSet->setLanguageCodes($languageCodes);
        $gcsSource = (new GcsSource())
            ->setInputUri($inputUri);
        $inputConfig = (new GlossaryInputConfig())
            ->setGcsSource($gcsSource);
        $glossary = (new Glossary())
            ->setName($formattedName)
            ->setLanguageCodesSet($languageCodesSet)
            ->setInputConfig($inputConfig);
//        $operationResponse = $translationServiceClient->createGlossary(
//            $formattedParent,
//            $glossary
//        );
        try {
            $operationResponse = $translationServiceClient->createGlossary(
                $formattedParent,
                $glossary
            );
            //$operationResponse->pollUntilComplete();
            if ($operationResponse->operationSucceeded()) {
                $response = $operationResponse->getResult();

                printf('Created Glossary.' . PHP_EOL);
                printf('Glossary name: %s' . PHP_EOL, $response->getName());
                printf('Entry count: %s' . PHP_EOL, $response->getEntryCount());
                printf(
                    'Input URI: %s' . PHP_EOL,
                    $response->getInputConfig()
                        ->getGcsSource()
                        ->getInputUri()
                );
            }
            else {
//                echo 'abc';exit();
                $error = $operationResponse->getError();
                // handleError($error)
            }
        }
        finally {
            $translationServiceClient->close();
        }
    }
    public function getlistGlossaries(){
        $translationServiceClient = new TranslationServiceClient();
        $formattedParent = $translationServiceClient->locationName(
            $this->projectId,
            $this->locationForGlosary
        );
        try {
            $pagedResponse = $translationServiceClient->listGlossaries($formattedParent);
            foreach ($pagedResponse->iterateAllElements() as $responseItem) {
                printf('<br>');
                printf('Glossary name: %s' . PHP_EOL, $responseItem->getName());
                printf('Entry count: %s' . PHP_EOL, $responseItem->getEntryCount());
                printf(
                    'Input URI: %s' . PHP_EOL,
                    $responseItem->getInputConfig()
                        ->getGcsSource()
                        ->getInputUri()
                );
            }
        } finally {
            $translationServiceClient->close();
        }
    }
    public function getFirstGlossary(){
        $glossary_id = '';
        $translationServiceClient = new TranslationServiceClient();
        $formattedParent = $translationServiceClient->locationName(
            $this->projectId,
            $this->locationForGlosary
        );
        try {
            $pagedResponse = $translationServiceClient->listGlossaries($formattedParent);
            $key = 0;
            foreach ($pagedResponse->iterateAllElements() as $responseItem) {
                $key ++;
                if($key == 1){
                    $arr_name = explode('/',$responseItem->getName());
                    $glossary_id = $arr_name[count($arr_name)-1];
                }
            }
        } finally {
            $translationServiceClient->close();
        }
        return $glossary_id;
    }
    public function deletelistGlossaries(){
        $translationServiceClient = new TranslationServiceClient();
        $formattedParent = $translationServiceClient->locationName(
            $this->projectId,
            $this->locationForGlosary
        );
        try {
            $pagedResponse = $translationServiceClient->listGlossaries($formattedParent);
            foreach ($pagedResponse->iterateAllElements() as $responseItem) {
                $arr_name = explode('/',$responseItem->getName());
                $glossary_id = $arr_name[count($arr_name)-1];
                $this->deleteGlossary($glossary_id);
            }
        }
        finally {
            $translationServiceClient->close();
        }
    }
    public function deleteGlossary($glossaryId){
        $translationServiceClient = new TranslationServiceClient();
        $formattedName = $translationServiceClient->glossaryName(
            $this->projectId,
            $this->locationForGlosary,
            $glossaryId
        );
        try {
            $operationResponse = $translationServiceClient->deleteGlossary($formattedName);
            //$operationResponse->pollUntilComplete();
            if ($operationResponse->operationSucceeded()) {
                $response = $operationResponse->getResult();
                printf('Deleted Glossary.' . PHP_EOL);
            } else {
                $error = $operationResponse->getError();
            }
        } finally {
            $translationServiceClient->close();
        }
    }
}