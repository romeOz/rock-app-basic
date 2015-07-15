<?php

namespace apps\frontend\controllers;


use rock\core\Controller;
use rock\csrf\CSRF;
use rock\i18n\i18n;
use rock\url\Url;

abstract class BaseAuthController extends Controller
{
    protected $emailBodyTpl = '';

    protected function getMessageLogout(CSRF $CSRF, $key, $layout = '@common.views/elements/alert-info')
    {
        $args = [
            $CSRF->csrfParam => $CSRF->get(),
            'service' => 'logout'
        ];
        $content = i18n::t($key, ['url' => Url::set()->addArgs($args)->getRelative()]);
        return $this->template->getChunk($layout, ['output' => $content]);
    }
}