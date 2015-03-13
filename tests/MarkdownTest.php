<?php

namespace rockunit;


use rock\markdown\Markdown;
use rockunit\db\DatabaseTestCase;
use rockunit\db\models\Users;

/**
 * @group db
 */
class MarkdownTest extends DatabaseTestCase
{
    protected function setUp()
    {
        parent::setUp();
        Users::$connection = $this->getConnection();
    }

    public function testUsernameLinkSuccess()
    {
        $markdown = $this->getMarkdown(['handlerLinkByUsername' =>
            function($username){
                return Users::findUrlByUsername($username);
            }
        ]);
        $result = $markdown->parse('@Linda');
        $this->assertSame('<p><a href="/linda/" title="Linda">@Linda</a></p>', $result);

        $result = $markdown->parse('Hi @Linda, foo');
        $this->assertSame('<p>Hi <a href="/linda/" title="Linda">@Linda</a>, foo</p>', $result);
    }

    public function testUsernameLinkFail()
    {
        $markdown = $this->getMarkdown(['handlerLinkByUsername' =>
        function($username){
            return Users::findUrlByUsername($username);
        }
                                                    ]);
        $result = $markdown->parse('@Tom');
        $this->assertSame('<p>@Tom</p>', $result);
    }

    protected function getMarkdown(array $config = [])
    {
        return new Markdown($config);
    }
}