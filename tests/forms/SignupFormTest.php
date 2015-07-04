<?php

namespace rockunit\forms;

use rock\csrf\CSRF;
use rock\di\Container;
use rock\Rock;
use rockunit\common\CommonTestTrait;
use rockunit\db\DatabaseTestCase;
use rockunit\db\models\Users;
use rockunit\forms\models\SignupFormMock;

/**
 * @group forms
 * @group db
 */
class SignupFormTest extends DatabaseTestCase
{
    use  CommonTestTrait;

    public function setUp()
    {
        parent::setUp();
        Users::$connection = $this->getConnection();
        static::sessionUp();
        static::activeSession();
    }

    public function tearDown()
    {
        parent::tearDown();
        static::sessionDown();
        static::activeSession(false);
    }

    /**
     * @dataProvider providerFail
     * @param array $post
     * @param array $errors
     */
    public function testFail(array $post, array $errors)
    {
        $model = new SignupFormMock();
        $post[Rock::$app->csrf->csrfParam] = call_user_func($post[Rock::$app->csrf->csrfParam]);
        $_POST = [$model->formName() => $post];
        $model->load($_POST);
        $this->assertFalse($model->validate());
        $this->assertEquals($errors, $model->getErrors());
    }

    public function providerFail()
    {
        return [
            [
                [
                    'email' => ' FOOgmail.ru    ',
                    'username' => '',
                    'password' => 'abc',
                    Rock::$app->csrf->csrfParam => function(){ return Rock::$app->csrf->get(/*(new SignupForm())->formName()*/);},
                    'password_confirm' => 'abc',
                    'captcha' => '12345'
                ],
                [
                    'username' =>
                        [
                            'username must not be empty',
                        ],
                    'email' =>
                        [
                            'e-mail must be valid',
                        ],
                    'password' =>
                        [
                            'password must have a length between 6 and 20',
                        ],
                    'captcha' =>
                        [
                            'captcha must be valid',
                        ],
                ]
            ],
            [
                [
                    'email' => '',
                    'username' => 'foo',
                    'password' => '123456',
                    'password_confirm' => '123456',
                    Rock::$app->csrf->csrfParam => function(){ return Rock::$app->csrf->get(/*(new SignupForm())->formName()*/);},
                    'captcha' => ''
                ],
                [
                    'email' =>
                        [
                            0 => 'e-mail must not be empty',
                        ],
                    'captcha' =>
                        [
                            0 => 'captcha must not be empty',
                        ],
                ]
            ],
            [
                [
                    'email' => 'foo@gmail',
                    'username' => 'foo',
                    'password' => 'abc',
                    'password_confirm' => 'abcde',
                    Rock::$app->csrf->csrfParam => function(){ return Rock::$app->csrf->get(/*(new SignupForm())->formName()*/);},
                ],
                [
                    'captcha' =>
                        [
                            0 => 'captcha must not be empty',
                        ],
                    'email' =>
                        [
                            0 => 'e-mail must be valid',
                        ],
                    'password' =>
                        [
                            0 => 'password must have a length between 6 and 20',
                        ],
                    'password_confirm' =>
                        [
                            0 => 'values must be equals',
                        ],
                ]
            ],
            [
                [
                    'email' => 'foogmail.ru',
                    'username' => '',
                    'password' => 'abc',
                    Rock::$app->csrf->csrfParam => function () {
                        return '';
                    },
                    'password_confirm' => 'abc',
                    'captcha' => '12345'
                ],
                array (
                    'alerts' =>
                        array (
                            0 => 'CSRF-token must not be empty',
                        ),
                )
            ],
        ];
    }

    public function testExistsUserByUsernameFail()
    {
        /** @var CSRF $csrf */
        $csrf = Container::load(CSRF::className());
        $post = [
            'email' => 'foo@gmail.ru',
            'username' => 'Jane',
            'password' => '123456',
            'password_confirm' => '123456',
            $csrf->csrfParam => $csrf->get(),
            'captcha' => '12345'
        ];
        static::getSession()->setFlash('captcha', '12345');
        $model = new SignupFormMock();
        $_POST = [$model->formName() => $post];
        $model->load($_POST);
        $this->assertFalse($model->validate());
        $expected = ['alerts' =>['User with this name/e-mail already exists.',],];
        $this->assertEquals($expected, $model->getErrors());
    }

    public function testExistsUserByEmailFail()
    {
        /** @var CSRF $csrf */
        $csrf = Container::load(CSRF::className());
        $post = [
            'email' => 'jane@hotmail.com',
            'username' => 'Chuck',
            'password' => '123456',
            'password_confirm' => '123456',
            $csrf->csrfParam => $csrf->get(),
            'captcha' => '12345'
        ];
        static::getSession()->setFlash('captcha', '12345');
        $model = (new SignupFormMock());
        $_POST = [$model->formName() => $post];
        $model->load($_POST);
        $this->assertFalse($model->validate());

        $expected = ['alerts' =>['User with this name/e-mail already exists.',],];
        $this->assertEquals($expected, $model->getErrors());
    }

    public function testCaptchaFail()
    {
        /** @var CSRF $csrf */
        $csrf = Container::load(CSRF::className());
        $post = [
            'email' => 'foo@gmail.ru',
            'username' => 'Jane',
            'password' => '123456',
            'password_confirm' => '123456',
            $csrf->csrfParam => $csrf->get(),
            'captcha' => '1234'
        ];
        static::getSession()->setFlash('captcha', '12345');
        $model = new SignupFormMock();
        $_POST = [$model->formName() => $post];
        $model->load($_POST);
        $this->assertFalse($model->validate());

        $expected = ['captcha' =>['captcha must be valid',],];
        $this->assertEquals($expected, $model->getErrors());
    }


    public function testSuccess()
    {
        /** @var CSRF $csrf */
        $csrf = Container::load(CSRF::className());
        $post = [
            'email' => 'chuck@gmail.ru',
            'username' => 'Chuck',
            'password' => '123456',
            'password_confirm' => '123456',
            $csrf->csrfParam => $csrf->get(),
            'captcha' => '12345'
        ];
        static::getSession()->setFlash('captcha', '12345');
        $model = new SignupFormMock();
        $_POST = [$model->formName() => $post];
        $model->load($_POST);
        $this->assertTrue($model->validate());
        $this->assertTrue(Users::find()->byUsername('Chuck')->exists());
        $this->assertTrue((bool)Users::deleteByUsername('Chuck'));
    }
}