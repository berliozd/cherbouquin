<?php

if ($context->getConnectedUser())
    \Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::USER_HOME);

$invalidDataMsg = __("Les informations saisies ne nous permettent pas de vous authentifier.", "s1b");
$accountNotActivated = __("Votre compte n'est pas activé. Merci de vérifier votre boite email. Vous avez certainemnt reçu un message vous demandant de l'activer.",
        "s1b");
$accountDeleted = __("Votre compte a été supprimé.", "s1b");

if ($_POST) {

    $userInForm = new \Sb\Db\Model\User;
    \Sb\Db\Mapping\UserMapper::map($userInForm, $_POST);

    if ($userInForm->IsValidForS1bAuthentification()) {
        $activeUser = \Sb\Db\Dao\UserDao::getInstance()->getS1bUser($userInForm->getEmail(), $userInForm->getPassword());
        if ($activeUser) {
            if ($activeUser->getDeleted()) {
                \Sb\Flash\Flash::addItem($accountDeleted);
            } elseif (!$activeUser->getActivated()) {
                \Sb\Flash\Flash::addItem($accountNotActivated);
            } else {
                $activeUser->setLastLogin(new \DateTime);
                \Sb\Db\Dao\UserDao::getInstance()->update($activeUser);
                \Sb\Authentification\Service\AuthentificationSvc::getInstance()->loginSucces($activeUser);
            }
        } else {
            \Sb\Flash\Flash::addItem($invalidDataMsg);
        }
    } else {
        \Sb\Flash\Flash::addItem($invalidDataMsg);
        \Sb\Trace\Trace::addItem($invalidDataMsg);
    }
}