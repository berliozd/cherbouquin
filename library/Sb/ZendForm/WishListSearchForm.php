<?php

namespace Sb\ZendForm;

/**
 *
 * @author Didier
 */
class WishListSearchForm extends ZendForm {

    /**
     *
     * @param mixed $options
     *
     * @return void
     *
     */
    public function __construct($options = null) {

        parent::__construct($options = null);
        
        $this->setAction('/default/wished-userbook/search-list')
            ->setMethod('get');
        
        $defaultValue = __("Nom, prénom, email...", "s1b");
        
        // Email element
        $searchTermElement = new \Zend_Form_Element_Text("wishedListSearchTerm");
        $searchTermElement->class = "input-item";
        $searchTermElement->setValue($defaultValue);
        $searchTermElement->setAttrib("auto-restore", "");
        $searchTermElement->addValidator("stringlength", false, array(
                "min" => 3,
                "messages" => array(
                        \Zend_Validate_StringLength::TOO_SHORT => __("'%value%' doit faire plus de %min% caractères de long", "s1b")
                )
        ));
        
        $this->removeAllDecorators($searchTermElement);
        
        // Submit button element
        $searchButtonElement = new \Zend_Form_Element_Submit("search");
        $searchButtonElement->setOptions(array(
                'label' => __("Rechercher", "s1b")
        ));
        $searchButtonElement->class = "button bt-red-m";
        $this->removeAllDecorators($searchButtonElement);
        
        // Email default label element
        $defaultLabelHidden = new \Zend_Form_Element_Hidden("emailDefaultLabel");
        $defaultLabelHidden->setValue($defaultValue);
        $defaultLabelHidden->class = "default-value";
        $this->removeAllDecorators($defaultLabelHidden);
        
        $this->addElements(array(
                $searchTermElement,
                $searchButtonElement,
                $defaultLabelHidden
        ));
        
        $this->removeDecorator("HtmlTag");
    }

}
