<?php

namespace Sb\View\Components;

class TwitterWidget extends \Sb\View\AbstractView {

    private $twitterSvc;

    function __construct($twitterSvc) {

        $this->twitterSvc = $twitterSvc;
        parent::__construct();
    }

    public function get() {

        if ($this->getConfig()->getIsProduction()) {

            $content = $this->twitterSvc->getContent();
            $user_image = "https://pbs.twimg.com/profile_images/2583277032/yxdfpkrxzfqs5et4s5vt.png";
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