<?php

namespace apps\frontend\controllers;


use apps\common\models\users\Users;
use rock\core\Controller;
use rock\csrf\CSRF;
use rock\i18n\i18n;
use rock\url\Url;

abstract class BaseAuthController extends Controller
{
    protected $emailBodyTpl = '';

    protected function redirect($url = null)
    {
        if (!isset($url)) {
            $this->response->refresh()->send(true);
            return;
        }
        $this->response->redirect($url)->send(true);
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