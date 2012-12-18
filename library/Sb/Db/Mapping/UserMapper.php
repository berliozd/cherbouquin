<?php

namespace Sb\Db\Mapping;

/**
 * Description of UserMapper
 *
 * @author Didier
 */
class UserMapper implements \Sb\Db\Mapping\Mapper {

    public static function map(\Sb\Db\Model\Model &$user, array $properties, $prefix = "") {

//        var_dump($properties);
        if (array_key_exists('id', $properties)) {
            $user->setId($properties['id']);
        }
//        if (array_key_exists('wp_id', $properties)) {
//            $user->setWpId($properties['wp_id']);
//        }
        if (array_key_exists('facebook_id', $properties)) {
            $user->setFacebookId($properties['facebook_id']);
        }
        if (array_key_exists('connexion_type', $properties)) {
            $user->setConnexionType($properties['connexion_type']);
        }
        if (array_key_exists('first_name', $properties)) {
            $user->setFirstName(stripslashes($properties['first_name']));
        }
        if (array_key_exists('last_name', $properties)) {
            $user->setLastName(stripslashes($properties['last_name']));
        }
        if (array_key_exists('user_name', $properties)) {
            $user->setUserName(stripslashes($properties['user_name']));
        }
        if (array_key_exists('email', $properties)) {
            $user->setEmail($properties['email']);
        }
        if (array_key_exists('password', $properties)) {
            if (!empty($properties['password']))
                $user->setPassword(sha1($properties['password']));
        }
        if (array_key_exists('gender', $properties)) {
            $user->setGender($properties['gender']);
        }
        if (array_key_exists('address', $properties)) {
            $user->setAddress($properties['address']);
        }
        if (array_key_exists('city', $properties)) {
            $user->setCity($properties['city']);
        }
        if (array_key_exists('zipCode', $properties)) {
            $user->setZipCode($properties['zipCode']);
        }
        if (array_key_exists('country', $properties)) {
            $user->setCountry($properties['country']);
        }
        if (array_key_exists('birthday', $properties)) {
            $user->setBirthDay(\Sb\Helpers\DateHelper::createDate($properties['birthDay']));
        }
        if (array_key_exists('facebook_language', $properties)) {
            $user->setFacebookLanguage($properties['facebook_language']);
        }
        if (array_key_exists('language', $properties)) {
            $user->setLanguage($properties['language']);
        }
        if (array_key_exists('token', $properties)) {
            $user->setToken($properties['token']);
        }
        if (array_key_exists('token_facebook', $properties)) {
            $user->setTokenFacebook($properties['token_facebook']);
        }
        if (array_key_exists('TokenFacebook', $properties)) {
            $user->setTokenFacebook($properties['TokenFacebook']);
        }
        if (array_key_exists('activated', $properties)) {
            $user->setActivated($properties['activated']);
        }
        if (array_key_exists('deleted', $properties)) {
            $user->setDeleted($properties['deleted']);
        }
        if (array_key_exists('gravatar', $properties)) {
            $user->setGravatar($properties['gravatar']);
        }
        if (array_key_exists('picture', $properties)) {
            $user->setPicture($properties['picture']);
        }
        if (array_key_exists('picture_big', $properties)) {
            $user->setPictureBig($properties['picture_big']);
        }
        if (array_key_exists('created', $properties)) {
            $user->setCreated(\Sb\Helpers\DateHelper::createDate($properties['created']));
        }
        if (array_key_exists('last_login', $properties)) {
            $user->setLastLogin(\Sb\Helpers\DateHelper::createDate($properties['last_login']));
        }
    }

    public static function mapFromFacebookUser(\Sb\Db\Model\User &$user, \Sb\Facebook\Model\FacebookUser $faceBookUser) {
        $user->setFacebookId($faceBookUser->getUid());
        $user->setConnexionType(\Sb\Entity\ConnexionType::FACEBOOK);
        $user->setFirstName($faceBookUser->getFirst_name());
        $user->setLastName($faceBookUser->getLast_name());
        $user->setUserName($faceBookUser->getName());
        $user->setEmail($faceBookUser->getEmail());
        $user->setGender($faceBookUser->getSex());
        $user->setCity($faceBookUser->getHometown_location());
        $user->setBirthDay($faceBookUser->getBirthday());
        $user->setFacebookLanguage($faceBookUser->getLocale());
        $user->setTokenFacebook(sha1(uniqid(rand())));
        $user->setActivated(true);
        $user->setPicture($faceBookUser->getPic_small());
        $user->setPictureBig($faceBookUser->getPic());
    }

    public static function reverseMap(\Sb\Db\Model\Model $user, array &$properties) {
//        $properties['Id'] = $user->getId();
////        $properties['WpId'] = $user->getWpId();
//        $properties['FacebookId'] = $user->getFacebookId();
//        $properties['ConnexionType'] = $user->getConnexionType();
//        $properties['FirstName'] = $user->getFirstName();
//        $properties['LastName'] = $user->getLastName();
//        $properties['UserName'] = $user->getUserName();
//        $properties['Email'] = $user->getEmail();
//        $properties['Password'] = $user->getPassword();
//        $properties['Gender'] = $user->getGender();
//        $properties['Address'] = $user->getAddress();
//        $properties['City'] = $user->getCity();
//        $properties['ZipCode'] = $user->getZipCode();
//        $properties['Country'] = $user->getCountry();
//        if ($user->getBirthDay())
//            $properties['Birthday'] = \Sb\Helpers\DateHelper::getDateForDB($user->getBirthDay());
//        $properties['FacebookLanguage'] = $user->getFacebookLanguage();
//        $properties['Language'] = $user->getLanguage();
//        $properties['Token'] = $user->getToken();
//        $properties['TokenFacebook'] = $user->getTokenFacebook();
//        $properties['Activated'] = ($user->getActivated() ? 1 : 0);
//        $properties['Deleted'] = ($user->getDeleted() ? 1 : 0);
//        $properties['Gravatar'] = $user->getGravatar();
//        $properties['Picture'] = $user->getPicture();
//        $properties['PictureBig'] = $user->getPictureBig();
//        if ($user->getCreated())
//            $properties['Created'] = \Sb\Helpers\DateHelper::getDateForDB($user->getCreated());
//        if ($user->getLastLogin())
//            $properties['LastLogin'] = \Sb\Helpers\DateHelper::getDateForDB($user->getLastLogin());
    }

}