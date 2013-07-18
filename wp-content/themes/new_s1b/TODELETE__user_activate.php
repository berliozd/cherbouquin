<?php

$noAuthentification = true;
require_once 'includes/init.php';
get_header();

/**
 * Template Name: user_activation
 */
if (!empty($_GET)) {

    $email = $_GET['Email'];
    \Sb\Trace\Trace::addItem("email à activer : [" . $email . "]");
    $user = \Sb\Db\Dao\UserDao::getInstance()->getByEmail($email);
    if ($user) {

        if ($user->getActivated()) {
            \Sb\Flash\Flash::addItem(__("utilisateur déjà actif", "s1b"));
        } else {
            $token = htmlspecialchars($_GET['Token']);
            if ($user->getToken() == $token) {
                $user->setActivated(true);
                \Sb\Db\Dao\UserDao::getInstance()->update($user);
                \Sb\Flash\Flash::addItem(__("votre compte est désormais activé", "s1b"));
            } else {
                \Sb\Flash\Flash::addItem(__("Token invalide!", "s1b"));
            }
        }
    } else {
        //Utilisateur inconnu
        $prob_token = __("Une erreur est survenue lors de l'activation, merci de contacter l'administrateur via le formulaire de ", "s1b")
                . '<a href=' . \Sb\Entity\Urls::CONTACT . '>' . __("contact", "s1b") . '</a>';
        \Sb\Flash\Flash::addItem($prob_token);
    }
}
\Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::LOGIN);