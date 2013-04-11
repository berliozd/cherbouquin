<?php

namespace Sb\Helpers;

class SecurityHelper {

    /**
     * Tell if a user profile is accessible or not
     * IMPORTANT : a user profile can only be viewed by connected users.
     * @param \Sb\Db\Model\User $requestedUser
     * @param \Sb\Db\Model\User $requestingUser
     * @return boolean
     */
    public static function IsUserAccessible(\Sb\Db\Model\User $requestedUser, \Sb\Db\Model\User $requestingUser) {

        // Is the user account has been deleted the profile can not be viewed
        if ($requestedUser->getDeleted())
            return false;

        // If a user has choosen to let his profile accessible by all members, then return true
        $requestedUserSettings = $requestedUser->getSetting();
        if ($requestedUserSettings) {
            if ($requestedUserSettings->getDisplayProfile() == \Sb\Entity\UserDataVisibility::MEMBERS_ONLY) {
                return true;
            }
        }

        // If the users are friend, return true
        $requestingUserFriendShips = $requestingUser->getFriendships_as_source();
        foreach ($requestingUserFriendShips as $friendShip) {
            if (($friendShip->getUser_target()->getId() == $requestedUser->getId()) && ($friendShip->getAccepted()))
                return true;
        }
        
        // Otherwise returns false
        return false;
    }

}