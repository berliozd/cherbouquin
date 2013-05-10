<?php

namespace Sb\ZendValidator;

use Sb\Db\Dao\PressReviewsSubscriberDao;

/**
 *
 * @author Didier
 */
class PressReviewsSubscriberExists extends \Zend_Validate_Abstract {

    const PRESS_REVIEW_SUBSCRIBER_EXISTS = "PRESS_REVIEW_SUBSCRIBER_EXISTS";

    protected $_messageTemplates = array(
            self::PRESS_REVIEW_SUBSCRIBER_EXISTS => "L'email saisi est déjà présent dans la liste des abonnés."
    );

    public function isValid($value) {

        $valid = true;
        $this->_setValue($value);
        
        $result = PressReviewsSubscriberDao::getInstance()->getByEmail($value);
        if ($result) {
            $valid = false;
            $this->_error(self::PRESS_REVIEW_SUBSCRIBER_EXISTS);
        }
        
        return $valid;
    }

}