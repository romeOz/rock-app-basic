<?php

namespace apps\common\configs;


use apps\common\models\users\BaseUsers;
use rock\base\ClassName;

class MarkdownProperties
{
    use ClassName;
    
    public static function handlerLinkByUsername($username)
    {
        return BaseUsers::findUrlByUsername($username);
    }
}