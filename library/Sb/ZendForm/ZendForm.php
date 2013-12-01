<?php

namespace Sb\ZendForm;

use Sb\Helpers\ArrayHelper;
class ZendForm extends \Zend_Form {

    /**
     * Remove all decorators to the specified zend form element
     * @param \Zend_Form_Element $element the element to remove the decorators from
     */
    public function removeAllDecorators(\Zend_Form_Element $element) {

        $element->removeDecorator("Description");
        $element->removeDecorator("HtmlTag");
        $element->removeDecorator("Label");
        $element->removeDecorator("DtDdWrapper");
        $element->removeDecorator("Errors");
    }

    public function getFailureMessages() {
        
        $errors = array();
        
        // Walk through all errors to set the error flash messages
        foreach ($this->getErrors() as $errorKey => $errorValue) {
            if ($errorValue && count($errorValue) > 0) {
                foreach ($errorValue as $key => $value) {
                    $fieldMessages = ArrayHelper::getSafeFromArray($this->getMessages(), $errorKey, null);
                    if ($fieldMessages) {
                        $errorMessage = ArrayHelper::getSafeFromArray($fieldMessages, $value, null);
                        $errors[] = $errorMessage;
                    }
                }
            }
        }
        
        return $errors;
    }

}