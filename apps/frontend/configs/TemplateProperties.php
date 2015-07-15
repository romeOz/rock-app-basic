<?php

namespace apps\frontend\configs;


use rock\core\Properties;

class TemplateProperties extends Properties
{
    public static function head()
    {
        return '<!DOCTYPE html><html lang="'.\rock\Rock::$app->language .'" data-ng-app="app">';
    }
}