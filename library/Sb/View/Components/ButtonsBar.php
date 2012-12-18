<?php

namespace Sb\View\Components;

use Sb\Helpers\HTTPHelper;

class ButtonsBar extends \Sb\View\AbstractView {

    private $buttonText = "";
    private $addButton = false;

    function __construct($addButton = false, $buttonText = "") {
        parent::__construct();
        $this->buttonText = $buttonText;
        $this->addButton = $addButton;
    }

    public function get() {

        $referer = HTTPHelper::getReferer();

        $tpl = new \Sb\Templates\Template("book/buttonsBar");
        $tpl->setVariables(array("buttonText" => $this->buttonText,
            "addButton" => $this->addButton,
            "referer" => $referer));
        return $tpl->output();
    }

}