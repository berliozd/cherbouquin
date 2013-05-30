<?php

namespace Sb\View\Components;

use Sb\Service\TwitterSvc;

class TwitterWidget extends \Sb\View\AbstractView {

    function __construct() {

        parent::__construct();
    }

    public function get() {

        if ($this->getConfig()
            ->getIsProduction()) {
            
            $content = TwitterSvc::getInstance()->getContent();
            $user_image = "https://si0.twimg.com/profile_images/2583277032/yxdfpkrxzfqs5et4s5vt.png";
            $user = "Cherbouquin"; /* Nom d'utilisateur sur Twitter */
            
            $tpl = new \Sb\Templates\Template("components/twitterWidget");
            $tpl->setVariables(array(
                    "content" => $content,
                    "user" => $user,
                    "user_image" => $user_image
            ));
            
            return $tpl->output();
        } else
            return "";
    }

}