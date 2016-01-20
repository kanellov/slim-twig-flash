<?php
/**
 * Test for Slim Twig Flash.
 * 
 * @link https://github.com/kanellov/slim-twig-flash for the canonical source repository
 *
 * @copyright Copyright (c) 2016 Vassilis Kanellopoulos <contact@kanellov.com>
 * @license GNU GPLv3 http://www.gnu.org/licenses/gpl-3.0-standalone.html
 */
namespace Knlv\Slim\Test\Views;

use Knlv\Slim\Views\TwigMessages;
use Twig_Environment;
use Twig_Loader_Filesystem;

class TwigMessagesTest extends \PHPUnit_Framework_TestCase
{
    protected $extension;

    protected $flash;

    protected $dummyMessages = [
        'key1' => [
            'my first message',
            'another message',
        ],
        'key2' => [
            'only one message',
        ],
    ];

    protected function setup()
    {
        $this->flash = $this->getMockBuilder('Slim\Flash\Messages')
            ->disableOriginalConstructor()
            ->getMock();
        $this->flash->expects($this->any())
            ->method('getMessages')
            ->will($this->returnValue($this->dummyMessages));
        $this->flash->expects($this->any())
            ->method('getMessage')
            ->will($this->returnCallback(function ($key) {
                return isset($this->dummyMessages[$key]) ? $this->dummyMessages[$key] : null;
            }));

        $this->extension = new TwigMessages($this->flash);
        $this->view = new Twig_Environment(
            new Twig_Loader_Filesystem(__DIR__.'/templates')
        );
        $this->view->addExtension($this->extension);
    }

    public function testMessagesInTemplateUsingKey()
    {
        $result = $this->view->render('with-key.twig');
        $expected = implode("\n", $this->dummyMessages['key1'])."\n";
        $this->assertEquals($expected, $result);
    }

    public function testMessagesInTemplateWithoutKey()
    {
        $result = $this->view->render('without-key.twig');
        $expected = <<< EOF
key1: my first message
key1: another message
key2: only one message

EOF;
        $this->assertEquals($expected, $result);
    }
}
