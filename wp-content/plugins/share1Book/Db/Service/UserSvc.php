<?php

namespace Sb\Db\Service;

/**
 * Description of UserSvc
 *
 * @author Didier
 */
class UserSvc extends \Sb\Db\Service\Service {

    private static $instance;

    /**
     *
     * @param String $baseDir
     * @return \Sb\Db\Service\UserSvc
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Service\UserSvc();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct(\Sb\Db\Dao\UserDao::getInstance(), "User");
    }

    /**
     *
     * @param type $lastname
     * @param type $firstname
     * @param type $username
     * @param type $email
     * @return \Sb\Db\Model\User
     */
    public function addLightUser($lastname, $firstname, $username, $email, $password) {
        $user = null;


        $userTmp = new \Sb\Db\Model\User;
        // Création du user dans la table s1b_users
        $userTmp->setToken(sha1(uniqid(rand())));
        $userTmp->setEmail($email);
        $userTmp->setFirstName($firstname);
        $userTmp->setLastName($lastname);
        $userTmp->setPassword(sha1($password));
        $userTmp->setUserName($username);
        $userTmp->setToken(sha1(uniqid(rand())));
        $userTmp->setDeleted(false);
        $userTmp->setActivated(false);
        $userTmp->setConnexionType(\Sb\Entity\ConnexionType::SHARE1BOOK);
        $userTmp->setGender("");
        $userTmp->setFacebookLanguage("");
        $userTmp->setTokenFacebook("");
        $userTmp->setPicture("");
        $userTmp->setPictureBig("");

        $setting = new \Sb\Db\Model\UserSetting();
        \Sb\Helpers\UserSettingHelper::loadDefaultSettings($setting);
        $userTmp->setSetting($setting);

        $user = \Sb\Db\Dao\UserDao::getInstance()->add($userTmp);

        return $user;
    }

//    function addCompleteOne(\Sb\Db\Model\User $user) {
//
//        $user->setToken(sha1(uniqid(rand())));
//        $user->setDeleted(false);
//
//        \Sb\Db\Dao\UserDao::getInstance()->add($user);
//
//        return $user;
//    }

}