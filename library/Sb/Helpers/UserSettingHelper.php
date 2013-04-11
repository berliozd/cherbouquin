<?php

namespace Sb\Helpers;

class UserSettingHelper {

    const EMAIL_ME_YES = "Yes";
    const DATA_USER_YES = "Yes";

    public static function loadDefaultSettings(\Sb\Db\Model\UserSetting &$settings) {
        $settings->setDisplayProfile(\Sb\Entity\UserDataVisibility::MEMBERS_ONLY);
        $settings->setDisplayBirthday(\Sb\Entity\UserDataVisibility::FRIENDS);
        $settings->setSendMessages(\Sb\Entity\UserDataVisibility::MEMBERS_ONLY);
        $settings->setDisplayEmail(\Sb\Entity\UserDataVisibility::FRIENDS);
        $settings->setEmailMe(self::EMAIL_ME_YES);
        $settings->setDataUser(self::DATA_USER_YES);
        $settings->setAllowFollowers("");
        $settings->setAccept_newsletter(true);
    }
}