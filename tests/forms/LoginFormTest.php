<?php

namespace rockunit\forms;

use rock\csrf\CSRF;
use rock\di\Container;
use rock\Rock;
use rockunit\common\CommonTestTrait;
use rockunit\db\DatabaseTestCase;
use rockunit\db\models\Users;
use rockunit\forms\models\LoginFormMock;

/**
 * @group forms
 * @group db
 */
class LoginFormTest extends DatabaseTestCase
{
    use CommonTestTrait;

    public function setUp()
    {
        parent::setUp();
        Users::$connection = $this->getConnection();
        static::sessionUp();
    }

    public function tearDown()
    {
        parent::tearDown();
        static::sessionDown();
        static::$post = $_POST;
    }

    /**
     * @dataProvider providerFail
     * @param array $post
     * @param array $errors
     * @throws \rock\di\ContainerException
     */
    public function testFail(array $post, array $errors)
    {
        /** @var CSRF $csrf */
        $csrf = Container::load(CSRF::className());
        $model = new LoginFormMock();
        $post[$csrf->csrfParam] = call_user_func($post[$csrf->csrfParam]);
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
                    'password' => 'abc',
                    Rock::$app->csrf->csrfParam => function () {
                        return Rock::$app->csrf->get();
                    },
                ],
                [
                    'email' =>
                        [
                            'e-mail must be valid',
                        ],
                    'password' =>
                        [
                            'password must have a length between 4 and 20',
                        ],
                ]
            ],
            [
                [
                    'email' => 'linda@gmail.com',
                    'password' => '123456f',
                    Rock::$app->csrf->csrfParam => function () {
                        return '';
                    },
                ],
                [
                    'alerts' =>
                        [
                            'CSRF-token must not be empty',
                        ],
                ]
            ],
            [
                [
                    'email' => 'linda@gmail.com',
                    'password' => '123456f',
                    Rock::$app->csrf->csrfParam => function () {
                        return Rock::$app->csrf->get();
                    },
                ],
                [
                    'alerts' =>
                        [
                            'Password or email is invalid.',
                        ],
                ]
            ],
            [
                [
                    'email' => 'jane@hotmail.com',
                    'password' => '123456',
                    Rock::$app->csrf->csrfParam => function () {
                        return Rock::$app->csrf->get();
                    },
                ],
                [
                    'alerts' =>
                        [
                            'Password or email is invalid.',
                        ],
                ]
            ],
        ];
    }

    public function testSuccess()
    {
        /** @var CSRF $csrf */
        $csrf = Container::load(CSRF::className());
        $post = [
            'email' => 'Linda@gmail.com',
            'password' => 'demo',
        ];
        $model = new LoginFormMock();
        $post[$csrf->csrfParam] = $csrf->get();
        $_POST = [$model->formName() => $post];
        $model->load($_POST);
        $this->assertTrue($model->validate());
    }
}
 