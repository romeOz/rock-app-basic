<?php

namespace apps\frontend\controllers;


use rock\core\Controller;
use rock\csrf\CSRF;
use rock\request\Request;
use rock\url\Url;
use rock\user\User;

class LogoutController extends Controller
{
    public function actionLogout(User $user, CSRF $CSRF)
    {
        $valid = $CSRF->valid(Request::get($CSRF->csrfParam));
        if ($valid) {
            $user->logout(true);
        }

        $this->response->redirect(Url::set()->removeAllArgs()->getAbsoluteUrl(true))->send(true);
    }
}