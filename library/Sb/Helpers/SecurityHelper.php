<?php

namespace Sb\Helpers;

class SecurityHelper {

    public static function IsUserAccessible(\Sb\Db\Model\User $requestedUser, \Sb\Db\Model\User $requestingUser) {

        if ($requestedUser->getDeleted())
            return false;

        $requestedUserSettings = $requestedUser->getSetting();
        if ($requestedUserSettings) {
            if ($requestedUserSettings->getDisplayProfile() == \Sb\Entity\UserDataVisibility::MEMBERS_ONLY) {
                return true;
            }
        }

        $requestingUserFriendShips = $requestingUser->getFriendships_as_source();
        foreach ($requestingUserFriendShips as $friendShip) {
            if (($friendShip->getUser_target()->getId() == $requestedUser->getId()) && ($friendShip->getAccepted()))
                return true;
        }
        return false;
    }

}