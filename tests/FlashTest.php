<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once 'BadTemplate.php';

class FlashTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function testStaticCall()
    {
        \Tamtamchik\SimpleFlash\Flash::message('Static message');

        $this->assertNotEmpty(\Tamtamchik\SimpleFlash\Flash::display());
    }

    /** @test */
    public function testCreation()
    {
        $flash = new \Tamtamchik\SimpleFlash\Flash();

        $this->assertFalse($flash->hasMessages());
        $this->assertEquals('Tamtamchik\SimpleFlash\Flash', get_class($flash));
    }

    /** @test */
    public function testFunction()
    {
        $flash = flash();

        $this->assertFalse($flash->hasMessages());
        $this->assertEquals('Tamtamchik\SimpleFlash\Flash', get_class($flash));
    }

    /** @test */
    public function testMessageWorkflow()
    {
        $flash = flash('Test info message');

        $this->assertTrue($flash->hasMessages());
        $this->assertContains('Test info message', $flash->display());
        $this->assertFalse($flash->hasMessages());
    }

    /** @test */
    public function testFunctionMessageType()
    {
        $flash = flash('Test info message', 'success');

        $this->assertContains('success', $flash->display());
    }

    /** @test */
    public function testChaining()
    {
        $flash = flash()->message('Test info message 1')->message('Test info message 2');

        $content = $flash->display();
        $this->assertContains('Test info message 1', $content);
        $this->assertContains('Test info message 2', $content);
    }

    /** @test */
    public function testInfoDefaultMessage()
    {
        $flash = flash('Test info message');

        $this->assertContains('info', $flash->display());
    }

    /** @test */
    public function testMessageTypes()
    {
        $flash = flash()
            ->message('Dummy 1', 'success')
            ->message('Dummy 2', 'info')
            ->message('Dummy 2', 'warning')
            ->message('Dummy 2', 'error');

        $content = $flash->display();
        $this->assertContains('success', $content);
        $this->assertContains('info', $content);
        $this->assertContains('success', $content);
        $this->assertContains('danger', $content);
    }

    /** @test */
    public function testPartialDisplay()
    {
        $flash = flash()->message('Dummy 1', 'success')->message('Dummy 2');

        $this->assertTrue($flash->hasMessages('success'));

        $content = $flash->display('success');

        $this->assertContains('Dummy 1', $content);
        $this->assertNotContains('Dummy 2', $content);
    }

    /** @test */
    public function testWrongDisplays()
    {
        $flash = flash()->message('Dummy 1', 'success')->message('Dummy 2');

        $this->assertFalse($flash->hasMessages('wrong'));

        $content = $flash->display('wrong');

        $this->assertEmpty($content);
    }

    /** @test */
    public function testAccessAsString()
    {
        $flash = new \Tamtamchik\SimpleFlash\Flash();
        $flash->clear();

        $flash->message('Test message');
        $this->assertContains('Test message', "{$flash}");
    }

    /** @test */
    public function testWrongMessageType()
    {
        $flash = flash();

        $flash->message('Test message', 'bad');
        $this->assertFalse(flash()->hasMessages());
    }

    /** @test */
    public function testThatSessionIsShared()
    {
        flash('Checking shared');

        $content = flash()->display();
        $this->assertContains('Checking shared', $content);
    }

    /** @test */
    public function testItFlushesChanges()
    {
        flash('First one', 'success')->message('Other one', 'info')->display();
        flash('Third one', 'error')->display();

        $this->assertFalse(flash()->hasMessages());
    }

    /** @test */
    public function testClearFunction()
    {
        flash('I\'ll never see this message', 'success');
        flash()->clear();

        $this->assertFalse(flash()->hasMessages());
    }

    /** @test */
    public function testShortcuts()
    {
        flash()->error('Info message')->warning('Info message')->info('Info message')->success('Info message');

        $content = flash()->display();
        $this->assertContains('danger', $content);
        $this->assertContains('warning', $content);
        $this->assertContains('info', $content);
        $this->assertContains('success', $content);
    }

    /** @test */
    public function testToString()
    {
        flash('Testing toString', 'success');
        $flash1 = new \Tamtamchik\SimpleFlash\Flash();
        $this->assertContains('toString', (string) $flash1);

        flash('Testing toString', 'success');
        $flash2 = flash();
        $this->assertContains('toString', (string) $flash2);
    }

    /** @test */
    public function testEmptyFunction()
    {
        flash('');
        $this->assertFalse(flash()->hasMessages());
    }

    /** @test */
    public function testWorkWithArrays()
    {
        $errors = array(
            'Invalid name',
            'Invalid email',
        );

        flash($errors, 'error');

        $content = flash()->display();
        $this->assertContains('Invalid name', $content);
        $this->assertContains('Invalid email', $content);
    }

    /** @test */
    public function testDefaultTemplate()
    {
        $template = \Tamtamchik\SimpleFlash\TemplateFactory::create();

        $prefix  = $template->getPrefix();
        $postfix = $template->getPostfix();
        $template->setPrefix('');
        $template->setPostfix('');
        $template->setWrapper('<div class="flash flash-%s" role="alert">%s</div>');

        $flash = new \Tamtamchik\SimpleFlash\Flash();

        $contentOriginal = $flash->info('Testing templates')->display();

        $flash->setTemplate($template);

        $content = $flash->info('Testing templates')->display();

        $this->assertEquals('<p>', $prefix);
        $this->assertNotEquals($contentOriginal, $content);
        $this->assertContains('Testing templates', $content);
        $this->assertNotContains($prefix, $content);
        $this->assertNotContains($postfix, $content);
    }

    /** @test */
    public function testClassWithTemplateConstructor()
    {
        $template = \Tamtamchik\SimpleFlash\TemplateFactory::create(\Tamtamchik\SimpleFlash\Templates::FOUNDATION_5);
        $flash    = new \Tamtamchik\SimpleFlash\Flash($template);

        $flash->info('Testing templates');

        $content = $flash->display();
        $this->assertContains('data-alert', $content);
    }

    /** @test */
    public function testFunctionWithTemplateConstructor()
    {
        $template = \Tamtamchik\SimpleFlash\TemplateFactory::create(\Tamtamchik\SimpleFlash\Templates::FOUNDATION_5);

        flash('Testing templates', 'info', $template);

        $content = flash()->display();
        $this->assertContains('data-alert', $content);
    }

    /** @test */
    public function testSetTemplateFunction()
    {
        $template = \Tamtamchik\SimpleFlash\TemplateFactory::create(\Tamtamchik\SimpleFlash\Templates::FOUNDATION_5);
        $flash    = new \Tamtamchik\SimpleFlash\Flash();

        $flash->info('Testing templates');

        $content = $flash->setTemplate($template)->display();
        $this->assertContains('data-alert', $content);
    }

    /** @test */
    public function testGetTemplate()
    {
        $flash = new \Tamtamchik\SimpleFlash\Flash();
        $flash->getTemplate()->setPrefix('AAAAAAAA')->setPostfix('BBBBBBBB');

        $flash->info('Testing templates');

        $content = $flash->display();
        $this->assertContains('AAAAAAAA', $content);
        $this->assertContains('BBBBBBBB', $content);
    }

    /** @test */
    public function testStaticMethods()
    {
        \Tamtamchik\SimpleFlash\Flash::setTemplate(\Tamtamchik\SimpleFlash\TemplateFactory::create());

        \Tamtamchik\SimpleFlash\Flash::info('Testing static');

        $content = \Tamtamchik\SimpleFlash\Flash::display();
        $this->assertContains('Testing static', $content);
    }

    /** @test */
    public function testCloneRestriction()
    {
        $flash      = new \Tamtamchik\SimpleFlash\Flash();
        $reflection = new ReflectionClass($flash);

        $this->assertFalse($reflection->isCloneable());
    }

    /** @test */
    public function testNotSerializable()
    {
        $flash = new \Tamtamchik\SimpleFlash\Flash();

        try {
            serialize($flash);
        } catch (\Tamtamchik\SimpleFlash\Exceptions\FlashSingletonException $e) {
            $this->assertContains('Serialization of Flash is not allowed!', $e->getMessage());
        }
    }

    /**
     * Need to be last - because spoils template.
     *
     * @test
     */
    public function testBadTemplate()
    {
        $template = new BadTemplate();
        $flash    = new \Tamtamchik\SimpleFlash\Flash();

        $flash->info('Testing templates');

        try {
            $flash->setTemplate($template)->display();
        } catch (\Tamtamchik\SimpleFlash\Exceptions\FlashTemplateException $e) {
            $this->assertContains('Please, make sure you have prefix, postfix and wrapper defined!', $e->getMessage());
        }
    }
}
