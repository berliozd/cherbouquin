<?php

namespace Sb\ZendValidator;

use Sb\Db\Dao\BookDao;

/**
 *
 * @author Didier
 *
 */
class BookExists extends \Zend_Validate_Abstract {

    const BOOK_NO_EXISTS = "BOOK_NO_EXISTS";

    protected $_messageTemplates = array (
            self::BOOK_NO_EXISTS => "Le livre n'existe pas." 
    );

    public function isValid($value) {

        $valid = true;
        $this->_setValue($value);
        
        $result = BookDao::getInstance()->get($value);
        if (! $result) {
            $valid = false;
            $this->_error(self::BOOK_NO_EXISTS);
        }
        
        return $valid;
    
    }

}