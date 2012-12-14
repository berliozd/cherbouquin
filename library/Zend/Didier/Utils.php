<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zend_Didier_Utils
 *
 * @author Didier
 */
class Zend_Didier_Utils {

    public static function RegisterNameSpace(DOMXPath &$xpath) {

        $xpath->registerNamespace('az', 'http://webservices.amazon.com/AWSECommerceService/2011-08-01');
        //$xpath->registerNamespace('az', 'http://webservices.amazon.com/AWSECommerceService/2005-10-05');
    }

}

?>
