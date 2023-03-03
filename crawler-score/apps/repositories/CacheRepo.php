<?php

namespace Travelnercom\Repositories;

use Phalcon\Mvc\User\Component;
use Phalcon\Cache\Backend\File as BackFile;
use Phalcon\Cache\Frontend\Data as FrontData;

class CacheTeam extends Component
{
    const PRE_SESSION_CACHE = "";
    const filePath = __DIR__."/../cache/";
    static $frontCache = null;
    static $backCache = null;
    public function clearCacheExpried() {
        $cacheKeys = self::$backCache->queryKeys();
        foreach ($cacheKeys as $key) {
            $content = self::$backCache->get($key);
            if ($content === null) {
                self::$backCache->delete($key);
            }
        }
    }
    public static function getCache($file_name)
    {
        $sessionId = self::PRE_SESSION_CACHE.$file_name;

        $cache = self::getBackCache();
        $cacheKey = self::cacheKeyClients($sessionId);
        $clients = $cache->get($cacheKey);

        return json_decode($clients);
    }
    public function deleteCache($file_name) {
        $sessionId = self::PRE_SESSION_CACHE.$file_name;
        $cache = self::getBackCache();
        $cacheKey = $this->cacheKeyClients($sessionId);
      
        try {
            if (!is_dir(self::filePath)) {
                mkdir(self::filePath);
            }
            $result =  $cache->delete($cacheKey);
           
        } catch (\Exception $e) {
            return false;
        }
 
       // $result =  $cache->set($cacheKey);
        return $result;
    }
    public function setCache($file_name,$data) {
        $sessionId = self::PRE_SESSION_CACHE.$file_name;
        $cache = self::getBackCache();
        $cacheKey = $this->cacheKeyClients($sessionId);
        try {
            if (!is_dir(self::filePath)) {
                mkdir(self::filePath);
            }
            $result =  $cache->save($cacheKey,$data);
        } catch (\Exception $e) {
            return false;
        }
 
       // $result =  $cache->set($cacheKey);
        return $result;
    }
    public static function getBackCache()
    {
        $frontCache = self::getFrontCache();
        if (self::$backCache == null) {
            self::$backCache = new BackFile($frontCache, ['cacheDir' => self::filePath, ]);
        }

        return self::$backCache;
    }

    public static function getFrontCache()
    {
        if (self::$frontCache == null) {
            self::$frontCache = new FrontData(['lifetime' => 24 * 60 *60 ]);
        }

        return self::$frontCache;
    }

    //Load cache keys
    public function cacheKeyClients($sessionId)
    {
        return $sessionId.'cls.data';
    }

}




