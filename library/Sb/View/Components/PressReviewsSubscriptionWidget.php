<?php

namespace Sb\View\Components;

use Sb\Templates\Template;
use Sb\ZendForm\PressReviewsSusbcriptionForm;

class PressReviewsSubscriptionWidget extends \Sb\View\AbstractView {

    function __construct() {

        parent::__construct();
    }

    public function get() {

        $baseTpl = "components/pressReviewsSubscriptionWidget";
        $tpl = new Template($baseTpl);
        
        $form = new PressReviewsSusbcriptionForm();
        $params = array(
                "form" => $form
        );
        
        $tpl->setVariables($params);
        
        return $tpl->output();
    }

}