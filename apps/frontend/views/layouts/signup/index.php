<?php
/** @var $this \rock\template\Template */


use rock\helpers\StringHelper;
use rock\widgets\Captcha;


?>
<?=$this->getChunk('@frontend.views/sections/top')?>
<main class="container main form-container" role="main"><?php

    echo $this->getSnippet(
        'activeForm',
        [
            'model' => $this->getPlaceholder('$root.model', false),
            'validate' => true,
            'config' => [
                'action' =>   '@link.home/signup.html',
                'validateOnChanged' => true,
                'options' => [
                    'class' => 'form-signup',
                    'novalidate' => 'novalidate',
                ],
                'fieldConfig' => [
                    'template' => "<div class='inline-column'>{hint}\n{input}</div>\n{error}",
                ],
            ],
            'fields' => [
                '<h2>[[%signup:upperFirst]]</h2>',
                'email' => [
                    'options' => [
                        'inputOptions' => [
                            'class' => 'form-control form-input',
                            'maxlength' => 30,
                            'autofocus' => '',
                            'placeholder'=> StringHelper::upperFirst(\rock\i18n\i18n::t('email')),
                        ],
                        'required' => true
                    ],
                ],
                'username' => [
                    'options' => [
                        'inputOptions' => [
                            'class' => 'form-control form-input',
                            'maxlength' => 80,
                            'placeholder'=> StringHelper::upperFirst(\rock\i18n\i18n::t('username'))
                        ]
                    ],
                ],
                'password' => [
                    'options' => [
                        'template' => "<div class='inline-column'>{hint}\n{input}<div class='strong-password' data-rock-password-strong='SignupForm.values.password'></div></div>\n{error}",
                        'inputOptions' => [
                            'class' => 'form-control form-input',
                            'maxlength' => 20,
                            'value' => '',
                            'placeholder'=> StringHelper::upperFirst(\rock\i18n\i18n::t('password')),
                        ]
                    ],
                    'passwordInput',
                ],
                'password_confirm' => [
                    'options' => [
                        'inputOptions' => [
                            'class' => 'form-control form-input',
                            'maxlength' => 20,
                            'value' => '',
                            'placeholder' => StringHelper::upperFirst(\rock\i18n\i18n::t('confirmPassword')),
                            'data' => ['rock-match' => 'password']
                        ]
                    ],
                    'passwordInput',
                ],
                'captcha' => [
                    'options' => [
                        'required' => true,
                    ],
                    'widget' => [
                        Captcha::className(), [
                            'template' => '<div class="block-center">{image}</div>{input}',
                            'output'=> Captcha::BASE64,
                            'options' => [
                                'class' => 'form-control form-input',
                                'maxlength' => 7,
                                'value' => '',
                                'placeholder'=> StringHelper::upperFirst(\rock\i18n\i18n::t('captcha')),
                            ],
//                        'imageOptions' => [
//                            'class' => 'cursor-pointer',
//                            'data' => [
//                                'ng-src' => '{{getCaptcha()}}',
//                                'ng-click' => 'reloadCaptcha("'.\rock\base\Alias::getAlias('@link.ajax').'/captcha/", $event)'
//                            ],
//                        ]
                        ]
                    ]
                ],
            ],
            'submitButton' => [
                \rock\i18n\i18n::t('signup'), [
                    'class' => 'btn btn-primary', 'name' => 'signup-button',
                    //'wrapperTpl' => '@INLINE <div class="form-button-wrapper">[[!+output]]</div>'
                ]
            ],
            'wrapperTpl' => '@INLINE
                    [[!+alerts:notEmpty&then=`@common.views\\elements\\alert-danger`]]
                    [[!+output]]'
        ]
    );
?></main>
<?=$this->getChunk('@frontend.views/sections/footer')?>