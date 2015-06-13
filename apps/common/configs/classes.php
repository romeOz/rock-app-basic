<?php
return [
    'db' =>[
        'class' => 'rock\\db\\Connection',
        'username' => 'rock',
        'password' => 'rock',
        'charset' => 'utf8',
        'dsn' => 'mysql:host=localhost;dbname=rockdemo;charset=utf8',
        'tablePrefix' => 'spt_',
        'aliasSeparator' => '__',
        'enableSchemaCache' => true,
    ],
    'route' => [
        'class' => \rock\route\Route::className(),
        'rules' =>
            [
                [
                    [rock\route\Route::GET],
                    '/' ,
                    [\apps\frontend\controllers\MainController::className(), 'actionIndex']
                ],
                [
                    \rock\route\Route::GET,
                    '*',
                    [\apps\frontend\controllers\MainController::className(), 'notPage']
                ],
            ],
    ],

    'template' => [
        'cssFiles' => [
            rock\template\Template::POS_HEAD => [
                '<link href="/assets/css/bootstrap.min.css" rel="stylesheet"/>',
                '<link href="/assets/css/demo.css" rel="stylesheet"/>'
            ],
        ],
        'jsFiles' => [
            rock\template\Template::POS_HEAD => [
                '<!--[if lt IE 9]><script src="/assets/js/html5shiv.min.js"></script><![endif]-->',
                '<script src="/assets/js/jquery.min.js"></script>',
                '<script src="/assets/js/bootstrap.min.js"></script>'
            ]
        ]
    ],
    'execute' => [
        'class' => \rock\execute\CacheExecute::className(),
        'path' => '@common/runtime/execute'
    ],

    'markdown' =>[
        'class' => \rock\markdown\Markdown::className(),
        'handlerLinkByUsername' => [\apps\common\configs\MarkdownProperties::className(), 'handlerLinkByUsername']
    ],


    //        'sphinx' => [
//            'class' => \rock\sphinx\Connection::className(),
//            'dsn' => 'mysql:host=127.0.0.1;port=9306;charset=utf8;',
//            'username' => '',
//            'password' => '',
//        ],
//        'mongodb' => [
//            'class' => \rock\mongodb\Connection::className(),
//            'dsn' => 'mongodb://developer:password@localhost:27017/mydatabase',
//        ],
//        'cache' => [
//            'class' => \rock\cache\CacheStub::className(),
//        ],
//        'cache' => [
//            'class' => \rock\cache\CacheFile::className(),
//            'adapter' => function () {
//                    return \rock\di\Container::load(
//                        [
//                            'class' => FileManager::className(),
//                            'adapter' => new Local(Alias::getAlias('@common/runtime/cache')),
//                            'config' => ['visibility' => FileManager::VISIBILITY_PRIVATE],
//                            'cache' => new Adapter(new Local(Alias::getAlias('@common/runtime/filesystem')), 'cache.tmp')
//                        ]
//                    );
//                }
//        ],

    // File manager
//        'file' => [
//            'class' => \rock\file\FileManager::className(),
//        ],
//        'uploadedFile' =>[
//            'class' => \rock\file\UploadedFile::className(),
//            'adapter' => [
//                'class' => FileManager::className(),
//                'adapter' => new Local(Alias::getAlias('@assets/images')),
//                'cache' => new Adapter(new Local(Alias::getAlias('@common.runtime/filesystem')), 'images.tmp')
//            ],
//            'calculatePathname' => function(\rock\file\UploadedFile $upload, $path, FileManager $fileManager = null) {
//                $pathname = !empty($path) ? [$path] : [];
//
//                if (isset($fileManager)) {
//                    $num = floor(
//                        count(
//                            $fileManager
//                                ->listContents(
//                                    "~/^\\d+\//",
//                                    true,
//                                    FileManager::TYPE_FILE
//                                )
//                        ) / 500);
//
//                    if (isset($num)) {
//                        $pathname[] =$num;
//                    }
//                }
//
//                $pathname[] = str_shuffle(md5_file($upload->tempName));
//                return implode(DS, $pathname) . ".{$upload->extension}";
//            }
//        ],

//    'imageProvider' => [
//        'class' => \rock\image\ImageProvider::className(),
//        'adapter' => [
//            'class' => FileManager::className(),
//            'adapter' => new Local(Alias::getAlias('@assets/images')),
//            'cache' => new Adapter(new Local(Alias::getAlias('@common.runtime/filesystem')), 'images.tmp')
//        ],
//        'adapterCache' => [
//            'class' => FileManager::className(),
//            'adapter' => new Local(Alias::getAlias('@assets/cache')),
//            'cache' => new Adapter(new Local(Alias::getAlias('@common.runtime/filesystem')), 'image_cache.tmp')
//        ],
//    ],
];