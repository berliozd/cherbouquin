<?php

namespace Sb\Exception;
/**
 * Description of \Sb\Exception\Exception
 *
 * @author Didier
 */
class Exception extends \Exception {

    //put your code here

    function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }

}

?>
