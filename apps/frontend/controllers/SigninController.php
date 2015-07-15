<?php

namespace apps\frontend\controllers;


use apps\common\models\users\Users;
use apps\frontend\models\SigninForm;
use rock\components\ModelEvent;
use rock\csrf\CSRF;
use rock\events\Event;
use rock\helpers\ArrayHelper;
use rock\user\User;

class SigninController extends BaseAuthController
{
    public function actionSignin(User $user, CSRF $CSRF)
    {
        $placeholders = [];
        if ($user->isLogged()) {
            $placeholders['content'] = $this->getMessageLogout($CSRF, 'loginLogout');

            return $this->render('success', $placeholders);
        }

        $model = new SigninForm();
        // redirect
        Event::on($model, SigninForm::EVENT_AFTER_LOGIN, function (ModelEvent $event) use ($user) {
            $this->login($user, $event->result);
            $this->response->refresh()->send(true);
        });
        $model->load($_POST);
        $placeholders['model'] = $model;

        return $this->render('index', $placeholders);
    }

    protected function login(User $user, Users $users)
    {
        $data = $users->toArray();
        $user->addMulti(ArrayHelper::intersectByKeys($data, ['id', 'username', 'url']));
        $user->login();
    }
}