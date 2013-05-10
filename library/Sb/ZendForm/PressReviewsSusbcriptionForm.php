<?php

namespace Sb\ZendForm;

use Sb\ZendValidator\PressReviewsSubscriberExists;

/**
 *
 * @author Didier
 */
class PressReviewsSusbcriptionForm extends \Zend_Form {

    /**
     *
     * @param mixed $options
     *
     * @return void
     *
     */
    public function __construct($options = null) {

        parent::__construct($options = null);
        
        $this->setAction('/default/press-reviews-subscriber/post')
            ->setMethod('post');
        
        // Email element
        $emailElement = new \Zend_Form_Element_Text("email");
        $emailElement->addValidator("emailAddress", false, array(
                "messages" => array(
                        \Zend_Validate_EmailAddress::INVALID => __("Le type reçu est invalide. Une chaine de caractère est attendue.","s1b"),
                        \Zend_Validate_EmailAddress::INVALID_FORMAT => __("'%value%' n'est pas un email valide.","s1b"),
                        \Zend_Validate_EmailAddress::INVALID_HOSTNAME => __("'%hostname%' n'est pas un nom d'hôte valide pour l'adresse email '%value%'.","s1b"),
                        \Zend_Validate_EmailAddress::INVALID_MX_RECORD => __("'%hostname%' ne semble pas avoir d'enregistrement MX valide pour l'adresse email '%value%'.","s1b"),
                        \Zend_Validate_EmailAddress::INVALID_SEGMENT => __("'%hostname%' n'est pas un segment réseau accessible. L'adresse email %value%' n'est pas accessible pour les réseaux publics.","s1b"),
                        \Zend_Validate_EmailAddress::LENGTH_EXCEEDED => __("'%value%' a dépassé la taille maximale.","s1b")
                )
        ))
            ->addValidator(new PressReviewsSubscriberExists(), false)
            ->setRequired(true)
            ->setValue(__("Email", "s1b"));
        $this->removeAllDecorators($emailElement);
        $emailElement->addDecorator("HtmlTag", array(
                "tag" => "div",
                "class" => "prsf-text"
        ));
        $emailElement->class = "input-item";
        
        // Submit button element
        $subscribeButtonElement = new \Zend_Form_Element_Submit("subscribe");
        $subscribeButtonElement->setOptions(array(
                'label' => __("S'abonner", "s1b")
        ));
        $this->removeAllDecorators($subscribeButtonElement);
        $subscribeButtonElement->class = "button bt-blue-m";
        $subscribeButtonElement->addDecorator("HtmlTag", array(
                "tag" => "div",
                "class" => "prsf-button"
        ));
        
        // Email default label element
        $emailDefaultLabelHidden = new \Zend_Form_Element_Hidden("emailDefaultLabel");
        $emailDefaultLabelHidden->setValue(__("Email", "s1b"));
        $emailDefaultLabelHidden->class = "pressReviewFormEmailDefaultLabel";
        $this->removeAllDecorators($emailDefaultLabelHidden);
        
        $this->addElements(array(
                $emailElement,
                $emailDefaultLabelHidden,
                $subscribeButtonElement
        ));
        
        $this->removeDecorator("HtmlTag");
    }

    public function getEmail() {

        return $this->getValue("email");
    }

    /**
     * Remove all decorators to the specified zend form element
     * @param \Zend_Form_Element $element the element to remove the decorators from
     */
    private function removeAllDecorators(\Zend_Form_Element $element) {

        $element->removeDecorator("Description");
        $element->removeDecorator("HtmlTag");
        $element->removeDecorator("Label");
        $element->removeDecorator("DtDdWrapper");
        $element->removeDecorator("Errors");
    }

}
