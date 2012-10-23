<?php

$registeringErrors = array();

if ($_POST) {

    // test if user already in db

    if (validatePost()) {
        $userInDB = \Sb\Db\Dao\UserDao::getInstance()->getByEmail($_POST['email']);

        // if yes => show message and redirect to login page
        if ($userInDB) {
            if ($userInDB->getDeleted())
                \Sb\Flash\Flash::addItem(__("Un compte correspondant à cet email existe mais il a été supprimé. Merci de nous contacter via le formulaire de contact.", "s1b"));
            else
                \Sb\Flash\Flash::addItem(__("Vous avez déjà créé un compte avec cet email. Si vous l'avez créé avec Facebook, vous pouvez vous connecter avec Facebook et ajouter un mot de passe dans votre profil section mot de passe. Si ce n'est pas le cas et que vous ne vous souvenez pas du mot de passe, vous pouvez demandez à réinitialiser le mot de passe en cliquant sur le lien \"Mot de passe perdu\"", "s1b"));
            \Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::LOGIN);
        } else {
            // if ok
            // ==> create user
            // ==> send welcome eamil
            // ==> create welcome message in internal mailbox
            //
            $userFromPost = new \Sb\Db\Model\User;
            \Sb\Db\Mapping\UserMapper::map($userFromPost, $_POST);
            $userFromPost->setToken(sha1(uniqid(rand())));
            $userFromPost->setActivated(false);
            $userFromPost->setDeleted(false);
            $userFromPost->setFacebookId("");
            $userFromPost->setGender("");
            $userFromPost->setFacebookLanguage("");
            $userFromPost->setTokenFacebook("");
            $userFromPost->setPicture("");
            $userFromPost->setPictureBig("");

            $setting = new \Sb\Db\Model\UserSetting();
            \Sb\Helpers\UserSettingHelper::loadDefaultSettings($setting);
            $userFromPost->setSetting($setting);

            $userInDB = \Sb\Db\Dao\UserDao::getInstance()->add($userFromPost);

            // send confirmation email
            $subject = sprintf(__("Votre compte %s a été créé", "s1b"), \Sb\Entity\Constants::SITENAME);
            $mailSvc->send($userInDB->getEmail(), $subject, \Sb\Helpers\MailHelper::validationAccountEmailBody($userInDB->getFirstName(), $userInDB->getToken(), $userInDB->getEmail()));

            // Send warning email to webmaster
            $mailSvc->send(\Sb\Entity\Constants::WEBMASTER_EMAIL . ", berliozd@gmail.com, rebiffe_olivier@yahoo.fr", __("nouveau user", "s1b"), $userInDB->getEmail());

            // create message in user internal mailbox
            \Sb\Db\Service\MessageSvc::getInstance()->createWelcomeMessage($userInDB->getId());

            // redirect to user homepage
            $successMsg = __("Votre compte a été créé correctement. N'oubliez pas de l'activer grâce à l'email que vous avez reçu avant toute première connexion. <strong>Attention cet email pourrait tomber dans vos spams.</strong>", "s1b");
            \Sb\Flash\Flash::addItem($successMsg);

            // Testing if the user registering match invitations and set them to validted and accepted if they exist
            $invitationSvc = \Sb\Db\Service\InvitationSvc::getInstance()->setInvitationsAccepted($userInDB->getEmail());

            \Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::LOGIN);
        }
    }
}

function validatePost() {
    global $registeringErrors;
    $ret = true;
    if (strlen($_POST['last_name']) < 3) {
        \Sb\Flash\Flash::addItem(__("Votre nom doit comprendre au moins 3 caractères.", "s1b"));
        $ret = false;
    }
    if (strlen($_POST['first_name']) < 1) {
        \Sb\Flash\Flash::addItem(__("Merci d'indiquer votre prénom.", "s1b"));
        $ret = false;
    }
    if (strlen($_POST['user_name']) < 1) {
        \Sb\Flash\Flash::addItem(__("Merci d'indiquer un identifiant.", "s1b"));
        $ret = false;
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        \Sb\Flash\Flash::addItem(__("Votre email n'est pas valide, merci de réessayer.", "s1b"));
        $ret = false;
    }
    if (strlen($_POST['password']) < 8) {
        \Sb\Flash\Flash::addItem(__("Votre mot de passe doit faire au moins 8 caractères.", "s1b"));
        $ret = false;
    }
    if (!$_POST['cgu_validation']) {
        \Sb\Flash\Flash::addItem(__("Vous devez accepter les CGU.", "s1b"));
        $ret = false;
    }
    return $ret;
}