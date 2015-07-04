<?php

namespace apps\frontend\controllers;


use apps\common\models\users\Users;
use apps\frontend\models\SigninForm;
use rock\components\ModelEvent;
use rock\core\Controller;
use rock\csrf\CSRF;
use rock\events\Event;
use rock\helpers\ArrayHelper;
use rock\i18n\i18n;
use rock\url\Url;
use rock\user\User;

class SigninController extends Controller
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
        Event::on($model, SigninForm::EVENT_AFTER_LOGIN, function(ModelEvent $event) use($user){
            $this->login($user, $event->result);
            $this->redirect();
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

    protected function redirect()
    {
        if (!isset($this->redirectUrl)) {
            $this->response->refresh()->send(true);
            return;
        }
        $this->response->redirect($this->redirectUrl)->send(true);
    }

    protected function getMessageLogout(CSRF $CSRF, $key, $layout = '@common.views/elements/alert-info')
    {
        $args = [
            $CSRF->csrfParam => $CSRF->get(),
            'service' => 'logout'
        ];
        $content = i18n::t($key, ['url' => Url::set()->addArgs($args)->getRelativeUrl(true)]);
        return $this->template->getChunk($layout, ['output' => $content]);
    }
}