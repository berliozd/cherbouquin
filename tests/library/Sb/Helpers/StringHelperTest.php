<?php

/**
 * Created by PhpStorm.
 * User: Berlioz
 * Date: 06/10/2015
 * Time: 23:34
 */
class StringHelperTest extends PHPUnit_Framework_TestCase {

    public function testIsValidEmailOK()
    {
        $emailToTest = "abc@aa.fr";
        $validEmail = \Sb\Helpers\StringHelper::isValidEmail($emailToTest);
        $this->assertEquals($validEmail, true, "\Sb\Helpers\StringHelper::isValidEmail failed testing a valid email.");
    }

    public function testIsValidEmailKO()
    {
        $emailToTest = "abc@aa.aaaaaaaaa";
        $validEmail = \Sb\Helpers\StringHelper::isValidEmail($emailToTest);
        $this->assertEquals($validEmail, false, "\Sb\Helpers\StringHelper::isValidEmail failed testing a unvalid email.");
    }

}