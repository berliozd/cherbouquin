<?php

namespace Sb\Helpers;

use Sb\Db\Model\User;

/**
 * Description of UserHelper
 *
 * @author Didier
 */
class UserHelper {

    public static function getSmallImageTag(User $user) {
        return sprintf("<div><img src='%s' border='0' class='image-thumb-small image-frame' title=\"" . $user->getFriendlyName() . "\"/>", $user->getGravatar());
    }
    
    public static function getMediumImageTag(User $user) {
        return sprintf("<div class='chronicle-user-background'><img src='%s' border='0' title=\"" . $user->getFriendlyName() . "\"/></div>", $user->getGravatar());
    }
}