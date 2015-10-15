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
    private $request;

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

    function __construct($imageUrlToPin, $textToPin, $request) {

        parent::__construct();
        $this->imageUrlToPin = $imageUrlToPin;
        $this->textToPin = $textToPin;
        $this->request = $request;
    }

    public function get() {
        $server = $this->request->getServer();
        $currentPage = "http://" . $server['HTTP_HOST'] . $server['REQUEST_URI'];

        $tpl = new Template("socialNetworksBar");
        $tpl->setVariables(array(
            "imageUrlToPin" => $this->imageUrlToPin,
            "textToPin" => $this->textToPin,
            'currentPage' => $currentPage
        ));
        return $tpl->output();
    }

}
