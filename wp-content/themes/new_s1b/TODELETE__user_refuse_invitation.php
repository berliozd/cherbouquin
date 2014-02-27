<?php

$noAuthentification = true;
require_once 'includes/init.php';
get_header();

/**
 * Template Name: user_refuse_invitation
 */
if (!empty($_GET)) {

    $email = $_GET['Email'];
    $token = $_GET['Token'];
    $invitation = \Sb\Db\Dao\InvitationDao::getInstance()->getByEmailAndToken($email, $token);
    if ($invitation) {
        $invitation->setIs_accepted(false);
        $invitation->setIs_validated(true);
        $invitation->setLast_modification_date(new \DateTime);
        \Sb\Db\Dao\InvitationDao::getInstance()->update($invitation);
        \Sb\Flash\Flash::addItem(sprintf(__("L'invitation à rejoindre %s a été refusée.", "s1b"), \Sb\Entity\Constants::SITENAME));
    } else {
        //Invitation unknown
        \Sb\Flash\Flash::addItem(__("Une erreur est survenue lors du refus de l'invitation", "s1b"));
    }
}
\Sb\Helpers\HTTPHelper::redirectToHome();