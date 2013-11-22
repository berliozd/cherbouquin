<?php

namespace Sb\Helpers;

use Sb\Entity\UserDataVisibility;
use Sb\Db\Model\UserSetting;

class UserSettingHelper {

    const EMAIL_ME_YES = "Yes";

    const DATA_USER_YES = "Yes";

    public static function loadDefaultSettings(UserSetting &$settings) {

        $settings->setDisplayProfile(UserDataVisibility::MEMBERS_ONLY);
        $settings->setDisplayBirthday(UserDataVisibility::FRIENDS);
        $settings->setSendMessages(UserDataVisibility::MEMBERS_ONLY);
        $settings->setDisplayEmail(UserDataVisibility::FRIENDS);
        $settings->setDisplay_wishlist(UserDataVisibility::ALL);
        $settings->setEmailMe(self::EMAIL_ME_YES);
        $settings->setDataUser(self::DATA_USER_YES);
        $settings->setAllowFollowers(self::DATA_USER_YES);
        $settings->setAccept_newsletter(true);
    }

}