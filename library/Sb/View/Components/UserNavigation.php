<?php

namespace Sb\View\Components;

class UserNavigation extends \Sb\View\AbstractView {

    function __construct() {
        parent::__construct();
    }

    public function get() {
        $user = $this->getContext()->getConnectedUSer();

        $tpl = new \Sb\Templates\Template("components/userNavigation");
        if ($user && $user->getSetting()) {
            if ($user->getSetting()->getDisplayProfile() == \Sb\Entity\UserDataVisibility::FRIENDS) {
                $userStatus = "Mes amis";
                $statusCssClass = "profile-picto-small-myfriends";
            } elseif ($user->getSetting()->getDisplayProfile() == "s1b_members") {
                $userStatus = "Public";
                $statusCssClass = "profile-picto-small-public";
            } elseif ($user->getSetting()->getDisplayProfile() == \Sb\Entity\UserDataVisibility::NO_ONE) {
                $userStatus = "Privé";
                $statusCssClass = "profile-picto-small-private";
            }
        }
        $tpl->setVariables(array("user" => $user, "userStatus" => $userStatus, "statusCssClass" => $statusCssClass));
        return $tpl->output();
    }
}