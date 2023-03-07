<?php

namespace Score\Repositories;

use Phalcon\Mvc\User\Component;
use Phalcon\Cache\Backend\File as BackFile;
use Phalcon\Cache\Frontend\Data as FrontData;

class CacheTeam extends Component
{
    const PRE_SESSION_CACHE = "";
    const filePath = __DIR__ . "/../Cache/Team/";
    static $frontCache = null;
    static $backCache = null;
    public function __construct()
    {
        if (!is_dir(__DIR__ . "/../Cache")) {
            mkdir(__DIR__ . "/../Cache");
        }
        if (!is_dir(self::filePath)) {
            mkdir(self::filePath);
        }
    }
    public function clearCacheExpried()
    {
        $cacheKeys = self::$backCache->queryKeys();
        foreach ($cacheKeys as $key) {
            $content = self::$backCache->get($key);
            if ($content === null) {
                self::$backCache->delete($key);
            }
        }
    }
    public  function getCache()
    {
        $sessionId = self::PRE_SESSION_CACHE . "_Team";

        $cache = self::getBackCache();
        $cacheKey = self::cacheKeyClients($sessionId);
        $clients = $cache->get($cacheKey);

        return json_decode($clients,true);
    }
    public  function deleteCache()
    {
        $sessionId = self::PRE_SESSION_CACHE . "_Team";
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
    public function setCache($arrTeam)
    {
        $sessionId = self::PRE_SESSION_CACHE . "_Team";
        $cache = self::getBackCache();
        $cacheKey = $this->cacheKeyClients($sessionId);
        try {
            if (!is_dir(self::filePath)) {
                mkdir(self::filePath);
            }
            $result =  $cache->save($cacheKey, $arrTeam);
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
            self::$backCache = new BackFile($frontCache, ['cacheDir' => self::filePath,]);
        }

        return self::$backCache;
    }

    public static function getFrontCache()
    {
        if (self::$frontCache == null) {
            self::$frontCache = new FrontData(['lifetime' => 24 * 60 * 60]);
        }

        return self::$frontCache;
    }

    //Load cache keys
    public function cacheKeyClients($sessionId)
    {
        return $sessionId . 'cls.data';
    }
}
