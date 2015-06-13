<?php

namespace apps\frontend\controllers;


use rock\core\Controller;

class MainController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index', ['content' => 'Hello world!']);
    }

    public function notPage($layout = 'index', array $placeholders = [])
    {
        return parent::notPage($layout);
    }
} 