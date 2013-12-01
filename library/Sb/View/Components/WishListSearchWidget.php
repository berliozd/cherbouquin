<?php

namespace Sb\View\Components;

use Sb\Templates\Template;
use Sb\ZendForm\PressReviewsSusbcriptionForm;
use Sb\ZendForm\WishListSearchForm;

class WishListSearchWidget extends \Sb\View\AbstractView {

    function __construct() {

        parent::__construct();
    }

    public function get() {

        $baseTpl = "components/wishListSearchWidget";
        $tpl = new Template($baseTpl);
        
        $form = new WishListSearchForm();
        $params = array(
                "form" => $form
        );
        
        $tpl->setVariables($params);
        
        return $tpl->output();
    }

}