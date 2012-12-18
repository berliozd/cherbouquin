<?php

namespace Sb\Facebook\Mapping;

/**
 * Description of FacebookUserMapper
 *
 * @author Didier
 */
class FacebookUserMapper {

    public static function map(&$facebookUser, array $properties, $prefix = "") {
        $facebookUser = new \Sb\Facebook\Model\FacebookUser;
        if (array_key_exists("Id", $properties))
            $facebookUser->setId($properties["Id"]);
        if (array_key_exists("uid", $properties))
            $facebookUser->setUid($properties["uid"]);
        if (array_key_exists("email", $properties))
            $facebookUser->setEmail($properties["email"]);
        if (array_key_exists("first_name", $properties))
            $facebookUser->setFirst_name($properties["first_name"]);
        if (array_key_exists("last_name", $properties))
            $facebookUser->setLast_name($properties["last_name"]);
        if (array_key_exists("name", $properties))
            $facebookUser->setName($properties["name"]);
        if (array_key_exists("sex", $properties))
            $facebookUser->setSex($properties["sex"]);
        if (array_key_exists("hometown_location", $properties))
            $facebookUser->setHometown_location($properties["hometown_location"]);
        if (array_key_exists("birthday", $properties))
            $facebookUser->setBirthday(\Sb\Helpers\DateHelper::createDateTime(date('Y-m-d H:i:s', strtotime($properties["birthday"]))));
        if (array_key_exists("locale", $properties))
            $facebookUser->setLocale($properties["locale"]);
        if (array_key_exists("pic_small", $properties))
            $facebookUser->setPic_small($properties["pic_small"]);
        if (array_key_exists("pic", $properties))
            $facebookUser->setPic($properties["pic"]);
    }

    public static function reverseMap($facebookUser, array &$properties) {


//
//            private $id;
//    private $uid;
//    private $email;
//    private $first_name;
//    private $last_name;
//    private $name;
//    private $sex;
//    private $hometown_location;
//    private $birthday;
//    private $locale;
//    private $pic_small;
//    private $pic;
//        $properties['IdSender'] = $message->getIdSender();
//
//        if ($message->getDate())
//            $properties['Date'] = \Sb\Helpers\DateHelper::getDateForDB($message->getDate());
    }

}

?>
