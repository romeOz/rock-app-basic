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
                    [rock\route\Route::GET, \rock\route\Route::POST],
                    '/recovery.html' ,
                    [\apps\frontend\controllers\RecoveryController::className(), 'activeRecovery']
                ],
                [
                    \rock\route\Route::GET,
                    '/activation.html' ,
                    [\apps\frontend\controllers\ActivationController::className(), 'actionIndex']
                ],
                [
                    \rock\route\Route::GET,
                    '*',
                    [\apps\frontend\controllers\MainController::className(), 'notPage']
                ],
            ],
    ],
    'template' => [
        'head' => [\apps\frontend\configs\TemplateProperties::className(), 'head'],
        'cssFiles' => [
            rock\template\Template::POS_HEAD => [
                '<link href="/assets/css/demo.min.css" rel="stylesheet"/>'
            ],
        ],

        'jsFiles' => [
            rock\template\Template::POS_END => [
                // vendor
                //'<script src="/assets/js/jquery.min.js"></script>',
                //'<script src="/assets/js/bootstrap.min.js"></script>',
                '<!--[if !(IE) | (gt IE 8) ]>-->',

//                '<script src="/assets/js/vendor/underscore/underscore.js"></script>',
//                '<script src="/assets/js/vendor/angular/angular.min.js"></script>',
//                '<script src="/assets/js/vendor/angular-animate/angular-animate.min.js"></script>',
//                '<script src="/assets/js/vendor/angular-bootstrap/ui-bootstrap.min.js"></script>',
//                '<script src="/assets/js/vendor/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>',
//                '<script src="/assets/js/vendor/angular-translate/angular-translate.min.js"></script>',
//                '<script src="/assets/js/vendor/angular-rock/angular-rock.js"></script>',

                // app
                '<script src="/assets/js/app.min.js"></script>',

                '<![endif]-->'
            ]
        ]
    ],
];