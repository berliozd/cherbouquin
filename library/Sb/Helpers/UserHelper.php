<?php

namespace Sb\Helpers;

use Sb\Db\Model\User;

/**
 * Description of UserHelper
 *
 * @author Didier
 */
class UserHelper {

    public static function getXSmallImageTag(User $user) {
        return sprintf("<img src=\"%s\" title=\"%s\" border=\"0\" class=\"image-thumb-square-small\" />", $user->getGravatar(), $user->getUserName());
    }

    public static function getMediumImageTag(User $user, $alt) {
        return sprintf("<img src=\"%s\" title=\"%s\" border='0' class=\"user-thumb\" itemprop=\"image\" alt=\"%s\"/>", $user->getGravatar(), $user->getUserName(), $alt);
    }

    public static function getSmallImageTag(User $user) {
        return sprintf("<img src=\"%s\" title=\"%s\" border=\"0\" class=\"user-thumb-small\" />", $user->getGravatar(), $user->getUserName());
    }

    public static function getFullName(User $user) {
        if ($user->getFirstName() != '' && $user->getLastName() != '')
            return ucwords(sprintf(__("%s %s", "s1b"), $user->getFirstName(), $user->getLastName()));
        elseif ($user->getFirstName() != '')
            return ucwords($user->getFirstName());
        elseif ($user->getLastName() != '')
            return ucwords($user->getLastName());
        elseif ($user->getUserName() != '')
            return ucwords($user->getUserName());
    }

    public static function getFullGenderAndAge(User $user) {

        $age = 0;
        if ($user->getBirthDay())
            $age = $user->getBirthDay()->diff(new \DateTime())->y;

        if ($user->getGender() && $age > 0) {
            return sprintf(__("%s %s ans", "s1b"), ($user->getGender() == "male") ? __("Homme", "s1b") : __("Femme", "s1b"), $age);
        } else if ($user->getGender()) {
            return ($user->getGender() == "male") ? __("Homme", "s1b") : __("Femme", "s1b");
        } else if ($age > 0) {
            $age = $user->getBirthDay()->diff(new \DateTime())->y;
            return sprintf(__("%s ans", "s1b"), $age);
        }

        return "";
    }

    public static function getFullCityAndCountry(User $user) {
        $countryLabel = "";
        if ($user->getCountry()) {
            $country = \Sb\Db\Dao\CountryDao::getInstance()->getCountryByCode($user->getCountry());
            $countryLabel = (ArrayHelper::getSafeFromArray($_SESSION, "WPLANG", "fr_FR") <> "en_US" ? $country->getLabel_french() : $country->getLabel_english());
            $countryLabel = ucfirst($countryLabel);
        }

        $res = "";
        if ($countryLabel != '' && $user->getCity() != '') {
            $res = sprintf(__("%s, %s", "s1b"), ucfirst($user->getCity()), $countryLabel);
        } elseif ($countryLabel != '') {
            $res = $countryLabel;
        } elseif ($user->getCity() != '') {
            $res = ucfirst($user->getCity());
        }
        return $res;
    }

    public static function getGender(User $user) {
        if ($user->getGender() != "") {
            if ($user->getGender() == "male") {
                return __("masculin", "s1b");
            } else {
                return __("féminin", "s1b");
            }
            return "";
        }
        return "";
    }

}