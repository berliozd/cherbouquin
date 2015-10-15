<?php

class BookControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {

    public function setUp() {
        $_SERVER['HTTP_HOST'] = 'cherbouquin';
        $_SERVER['REQUEST_URI'] = 'test_request_uri';
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

    /**
     * @runInSeparateProcess
     */
    public function testBook() {
        $this->dispatch('/default/book/index/bid/18713');
        $this->assertController('book');
    }


}

