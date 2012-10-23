<?php

/**
 * Template Name: user_facebook_connect
 */
$noAuthentification = true;
require_once 'includes/init.php';

$accountDeleted = __("Votre compte a été supprimé.", "s1b");
$home = \Sb\Helpers\HTTPHelper::Link("");
$loginFaceBook = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::LOGIN_FACEBOOK);

// Testing if user is facebook connected
$facebookSvc = new \Sb\Facebook\Service\FacebookSvc($config->getFacebookApiId(), $config->getFacebookSecret(), $loginFaceBook, $home);
$facebookUser = $facebookSvc->getUser();

if ($facebookUser) {

    // If yes, testing if a user exist in db (and not deleted)
    // Search a matching activated user in DB
    $faceBookEmail = $facebookUser->getEmail();
    $facebookId = $facebookUser->getUid();
    $userInDB = \Sb\Db\Dao\UserDao::getInstance()->getFacebookUser($faceBookEmail);

    if (!$userInDB) { // If no existing user => create an account and redirect to user homepage
        // create user in db
        $userFromFB = new \Sb\Db\Model\User;
        \Sb\Db\Mapping\UserMapper::mapFromFacebookUser($userFromFB, $facebookUser);
        $userFromFB->setToken(sha1(uniqid(rand())));
        $userFromFB->setDeleted(false);
        $setting = new \Sb\Db\Model\UserSetting();
        \Sb\Helpers\UserSettingHelper::loadDefaultSettings($setting);
        $userFromFB->setSetting($setting);
        $userInDB = \Sb\Db\Dao\UserDao::getInstance()->add($userFromFB);

        // send confirmation email
        $subject = sprintf(__("Votre compte %s a été créé avec Facebook", "s1b"), \Sb\Entity\Constants::SITENAME);
        $mailSvc->send($userInDB->getEmail(), $subject, \Sb\Helpers\MailHelper::faceBookAccountCreationEmailBody($userInDB->getFirstName()));

        // Test if the email matches invitations and set them to accepted and validated
        \Sb\Db\Service\InvitationSvc::getInstance()->setInvitationsAccepted($userInDB->getEmail());

        // Send warning email to webmaster
        $mailSvc->send(\Sb\Entity\Constants::WEBMASTER_EMAIL . ", berliozd@gmail.com, rebiffe_olivier@yahoo.fr", __("nouveau user via facebook", "s1b"), $userInDB->getEmail());


        // send message in user internal mailbox
        \Sb\Db\Service\MessageSvc::getInstance()->createWelcomeMessage($userInDB->getId());

        // redirect to user homepage
        \Sb\Authentification\Service\AuthentificationSvc::getInstance()->loginSucces($userInDB);
    } elseif ($userInDB->getDeleted()) { // In user deleted, display a message and redirect to referer
        \Sb\Flash\Flash::addItem($accountDeleted);
        $facebookSvc->cleanUser();
        $facebookUser = null;
        $faceBookEmail = null;
        $facebookId = null;
        \Sb\Helpers\HTTPHelper::redirectToReferer();
    } else {// If yes => connect and redirect to user homepage
        if (!$userInDB->getConnexionType() != \Sb\Entity\ConnexionType::FACEBOOK)
            $userInDB->setConnexionType(\Sb\Entity\ConnexionType::FACEBOOK);

        if (!$userInDB->getFacebookId())
            $userInDB->setFacebookId($facebookUser->getUid());

        if (!$userInDB->getPicture())
            $userInDB->setPicture($facebookUser->getPic_small());

        if (!$userInDB->getPictureBig())
            $userInDB->setPictureBig($facebookUser->getPic());

        if (!$userInDB->getFacebookLanguage())
            $userInDB->setFacebookLanguage($facebookUser->getLocale());

        if (!$userInDB->getGender())
            $userInDB->setGender($facebookUser->getSex());

        if (!$userInDB->getCity())
            $userInDB->setCity($facebookUser->getHometown_location());

        if (!$userInDB->getBirthDay())
            $userInDB->setBirthDay($facebookUser->getBirthday());

        $userInDB->setLastLogin(new \DateTime);

        \Sb\Db\Dao\UserDao::getInstance()->update($userInDB);

        \Sb\Authentification\Service\AuthentificationSvc::getInstance()->loginSucces($userInDB);
    }
} else { // If no, redirect to facebook login page
    \Sb\Helpers\HTTPHelper::redirectToUrl($facebookSvc->getFacebookLogInUrl());
}