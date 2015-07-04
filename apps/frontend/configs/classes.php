<?php
use rock\route\Route;

return [
    'route' => [
        'class' => \rock\route\Route::className(),
        'rules' =>
            [
                [
                    \rock\route\Route::GET,
                    [
                        Route::FILTER_GET => [
                            'service' => 'logout'
                        ]
                    ],
                    [\apps\frontend\controllers\LogoutController::className(), 'actionLogout']
                ],
                [
                    rock\route\Route::GET,
                    '/' ,
                    [\apps\frontend\controllers\MainController::className(), 'actionIndex']
                ],
                [
                    [rock\route\Route::GET, \rock\route\Route::POST],
                    '/signin.html' ,
                    [\apps\frontend\controllers\SigninController::className(), 'actionSignin']
                ],
                [
                    [rock\route\Route::GET, \rock\route\Route::POST],
                    '/signup.html' ,
                    [\apps\frontend\controllers\SignupController::className(), 'actionSignup']
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
];