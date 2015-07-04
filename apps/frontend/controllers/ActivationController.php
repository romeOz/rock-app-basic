<?php

namespace apps\frontend\controllers;


use apps\common\models\users\Users;
use rock\core\Controller;
use rock\i18n\i18n;
use rock\request\Request;
use rock\session\Session;
use rock\url\Url;
use rock\user\User;

class ActivationController extends Controller
{
    protected $keySessionFlash  = 'successActivate';

    public function actionIndex(User $user, Session $session)
    {
        $placeholders = [];
        if ($session->hasFlash($this->keySessionFlash)){
            $placeholders['content'] = i18n::t('successActivate');
            return $this->render('success', $placeholders);

        } elseif ($user->isGuest() && ($users = Users::activate(Request::get('token')))) {
            // auto-login
            $user->addMulti($users->toArray(['id', 'username', 'url']));
            $user->login();

            $session->setFlash($this->keySessionFlash);
            $this->response->redirect(Url::set()->removeAllArgs()->getAbsoluteUrl(true))->send(true);
            return null;
        }

        return $this->notPage('@frontend.views/layouts/notPage');
    }
}