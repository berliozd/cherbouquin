<?php

namespace Sb\View;

use Sb\Templates\Template;

/**
 *
 * @author Didier
 */
class SocialNetworksBar extends \Sb\View\AbstractView {

    private $imageUrlToPin;
    private $textToPin;

    /**
     * @return String $imageUrlToPin
     */
    public function getImageUrlToPin() {
        return $this->imageUrlToPin;
    }

    /**
     * @param String $imageUrlToPin
     */
    public function setImageUrlToPin($imageUrlToPin) {
        $this->imageUrlToPin = $imageUrlToPin;
    }

    /**
     * @return String $textToPin
     */
    public function getTextToPin() {
        return $this->textToPin;
    }

    /**
     * @param String $textToPin
     */
    public function setTextToPin($textToPin) {
        $this->textToPin = $textToPin;
    }

    function __construct($imageUrlToPin, $textToPin) {

        parent::__construct();
        $this->imageUrlToPin = $imageUrlToPin;
        $this->textToPin = $textToPin;
    }

    public function get() {

        $tpl = new Template("socialNetworksBar");
        $tpl->setVariables(array(
            "imageUrlToPin" => $this->imageUrlToPin, "textToPin" => $this->textToPin
        ));
        return $tpl->output();
    }

}
