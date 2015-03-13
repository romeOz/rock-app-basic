<?php

namespace rockunit\common;


//use League\Flysystem\Adapter\Local;
//use rock\cache\CacheFile;
//use rock\file\FileManager;
use rock\di\Container;
use rock\helpers\FileHelper;
use rockunit\mocks\SessionMock;

trait CommonTestTrait
{
    protected static $session = [];
    protected static $cookie = [];
    public static $post = [];

    /**
     * @param string $config config of session
     * @return SessionMock
     * @throws \rock\di\ContainerException
     */
    public static function getSession($config = 'session')
    {
        return Container::load($config);
    }

    public static function activeSession($active = true)
    {
        SessionMock::$isActive = $active;
    }

    protected static function sessionUp()
    {
        $_SESSION = static::$session;
        $_COOKIE = static::$cookie;
        $_POST = static::$post;
//        /** @var Cookie $cookie */
//        $cookie = Container::load('cookie');
//        $cookie->removeAll();
//        static::getSession()->removeAll();
    }

    protected static function clearRuntime()
    {
        $runtime = ROCKUNIT_RUNTIME;
        FileHelper::deleteDirectory($runtime);
    }

    protected static function sort($value)
    {
        ksort($value);
        return $value;
    }

//    /**
//     * @param array $config
//     * @return \rock\cache\CacheInterface
//     */
//    protected static function getCache(array $config = [])
//    {
//        if (empty($config)) {
//            $config = [
//                'adapter' => new FileManager([
//                    'adapter' => new Local(ROCKUNIT_RUNTIME),
//                ])
//            ];
//        }
//        return new CacheFile($config);
//    }

    protected static function sessionDown()
    {
        static::$session = $_SESSION;
        static::$cookie = $_COOKIE;
        static::$post = $_POST;
    }
}