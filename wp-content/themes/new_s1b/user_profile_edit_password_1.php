<?php

$user = $context->getConnectedUser();
$userSettings = $user->getSetting();

if ($_POST) {
    $Password_old = trim($_POST['Password_old']);
    $Password_modif = trim($_POST['Password_modif']);
    $Password_old_crypted = sha1(trim($_POST['Password_old']));
    $Password_modif_crypted = sha1(trim($_POST['Password_modif']));

// on teste si l'utilisateur à déjà un mot de passe
// --> si oui il est inscrit via le formulaire share1Book
// --> si non il est inscrit via Facebook Connect
//
    // no password for user
    if (!$user->getPassword()) {
        if (strlen($Password_modif) >= 8) {
            // update password
            $user->setPassword($Password_modif_crypted);
            \Sb\Db\Dao\UserDao::getInstance()->update($user);

            // set flash message
            \Sb\Flash\Flash::addItem(__("Vos modifications ont bien été enregistrées.", "s1b"));

        } else {
            // password not long enough
            \Sb\Flash\Flash::addItem(__("Le mot de passe doit contenir au moins 8 caractères", "s1b"));
        }
    } else {
        // on teste si l'ancien mot de passe est bon
        if ($user->getPassword() == $Password_old_crypted) {

            //On verifie que le mot de passe a 8 caracteres ou plus
            if (strlen($Password_modif) >= 8) {

                // update password
                $user->setPassword($Password_modif_crypted);
                \Sb\Db\Dao\UserDao::getInstance()->update($user);

                // set flash message                
                \Sb\Flash\Flash::addItem(__("Vos modifications ont bien été enregistrées.", "s1b"));
                
            } else {
                //Sinon, on dit que le mot de passe n'est pas assez long
                \Sb\Flash\Flash::addItem(__("Le mot de passe doit contenir au moins 8 caractères", "s1b"));
            }
        } else {
            \Sb\Flash\Flash::addItem(__("Votre ancien mot de passe n'est pas correct", "s1b"));
        }
    }
    \Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::USER_PROFILE);
} else {
    if (!$user->getPassword())
        \Sb\Flash\Flash::addItem(__("Vous n'avez pas de mot de passe car vous vous êtes inscrit via Facebook. 
        Si vous souhaitez utiliser votre email et un mot de passe pour vous connecter 
        merci d'indiquer un mot de passe dans la case Nouveau mot de passe", "s1b"));
}

function disConnect() {
    $tmpLang = $_SESSION['WPLANG'];
    $_SESSION = array();
    session_destroy();
    $_SESSION['WPLANG'] = $tmpLang;
}