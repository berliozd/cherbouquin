<?php

namespace Sb\View;

/**
 *
 * @author Didier
 */
class UserProfile extends \Sb\View\AbstractView {

    private $user;
    private $userSettings;
    private $addStatus;
    private $addLinks;
    private $isOtherUser; // defines if it is an ohther user that the connected one

    function __construct($user, $userSettings, $addStatus, $addLinks, $isOtherUser) {
        parent::__construct();
        $this->user = $user;
        $this->userSettings = $userSettings;
        $this->addStatus = $addStatus;
        $this->addLinks = $addLinks;
        $this->isOtherUser = $isOtherUser;
    }

    public function get() {

        $tpl = new \Sb\Templates\Template("userProfile");

        // Set variables
        $tpl->setVariables(array("user" => $this->user,
            "userSettings" => $this->userSettings,
            "addStatus" => $this->addStatus,
            "addLinks" => $this->addLinks,
            "isOtherUser" => $this->isOtherUser));

        return $tpl->output();
    }

}