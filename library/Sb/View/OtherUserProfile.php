<?php

namespace Sb\View;

/**
 *
 * @author Didier
 */
class OtherUserProfile extends \Sb\View\AbstractView {

    private $user;
    private $userSettings;
    private $isFriend;
    

    function __construct($user, $userSettings, $isFriend) {
        parent::__construct();
        $this->user = $user;
        $this->userSettings = $userSettings;
        $this->isFriend = $isFriend;
    }

    public function get() {

        $tpl = new \Sb\Templates\Template("otherUserProfile");

        // Set variables
        $tpl->setVariables(array("user" => $this->user,
            "userSettings" => $this->userSettings,
            "isFriend" => $this->isFriend));

        return $tpl->output();
    }

}