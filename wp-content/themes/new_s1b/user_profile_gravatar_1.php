<?php

$user = $context->getConnectedUser();
$userSettings = $user->getSetting();

if ($_POST) {
    if (!empty($_POST['gravatar'])) {
        $gravatar = trim($_POST['gravatar']);
        $user->setGravatar($gravatar);
        \Sb\Db\Dao\UserDao::getInstance()->update($user);
        \Sb\Flash\Flash::addItem(__("Votre photo a été mise à jour.", "s1b"));
    } else {
        \Sb\Flash\Flash::addItem(__("Vous devez sélectionner au moins un Gravatar", "s1b"));
    }
    \Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::USER_PROFILE);
}