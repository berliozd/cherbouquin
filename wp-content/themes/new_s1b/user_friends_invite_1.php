<?php

use \Sb\Flash\Flash;
use \Sb\Db\Dao\UserDao;
use \Sb\Db\Dao\GuestDao;
use \Sb\Helpers\StringHelper;
use \Sb\Helpers\ArrayHelper;
use \Sb\Db\Dao\InvitationDao;
use \Sb\Helpers\HTTPHelper;
use \Sb\Entity\Urls;
use \Sb\Mail\Service\MailSvcImpl;
use \Sb\Db\Model\Invitation;
use \Sb\Db\Model\Guest;
use \Sb\Entity\Constants;

$user = $context->getConnectedUser();
$continue = true;

if ($_POST) {

    $emails = ArrayHelper::getSafeFromArray($_POST, 'Emails', null);
    $message = ArrayHelper::getSafeFromArray($_POST, 'Message', null);
    if (!$emails || !$message) {
        Flash::addItem(__("Vous devez renseigner tous les champs obligatoires.", "s1b"));
        $continue = false;
    }

    if ($continue) {

        $emailsListFromPost = explode(",", $emails);
        // Getting emails list
        if ($emailsListFromPost) {

            // Looping through all emails for validating all of them
            // At the end of the loop:
            // we will have an array of emails to be processed
            // and flag to process or not the sendings
            $emailsList = array();

            foreach ($emailsListFromPost as $emailToInvite) {
                $addEmail = true;
                $emailToInvite = strtolower(trim($emailToInvite));
                if ($emailToInvite != "") {
                    // Testing if the email is valid
                    if (!StringHelper::isValidEmail($emailToInvite)) {
                        Flash::addItem(sprintf(__("%s n'est pas un email valide.", "s1b"), $emailToInvite));
                        // We will stop invitation sending
                        $continue = false;
                        // Current email not added to the array of emails to be processed
                        $addEmail = false;
                    } else {

                        // Testing if the email does not match an existing user                        
                        $userInDb = UserDao::getInstance()->getByEmail($emailToInvite);

                        if ($userInDb && !$userInDb->getDeleted()) {
                            if ($continue) {
                                $friendRequestUrl = HTTPHelper::Link(Urls::USER_FRIENDS_REQUEST, array("fid" => $userInDb->getId()));
                                Flash::addItem(sprintf(__("Un utilisateur existe déjà avec l'email : %s. <a class=\"link\" href=\"%s\">Envoyer lui une demande d'ami</a>", "s1b"), $emailToInvite, $friendRequestUrl));
                            }
                            // Current email not added to the array of emails to be processed
                            $addEmail = false;
                        } else {

                            // Testing if invitations have been sent to that guest (email) by the current user                        
                            $invitations = InvitationDao::getInstance()->getListForSenderAndGuestEmail($user, $emailToInvite);
                            if ($invitations && count($invitations) > 0) {
                                Flash::addItem(sprintf(__("Vous avez déjà envoyé une invitation à cet email : %s.", "s1b"), $emailToInvite));
                                // We will stop invitation sending
                                $continue = false;
                                // Current email not added to the array of emails to be processed
                                $addEmail = false;
                            }
                        }
                    }
                }
                if ($addEmail)
                    $emailsList[] = $emailToInvite;
            }

            if ($continue) {
                // Looping through all emails for sending invitation
                $initialMessage = $message;
                foreach ($emailsList as $emailToInvite) {
                    $emailToInvite = strtolower(trim($emailToInvite));
                    if ($emailToInvite != "") {

                        $sendInvitation = true;
                        // Testing again if invitations have been sent to that guest (email) by the current user                        
                        $invitations = InvitationDao::getInstance()->getListForSenderAndGuestEmail($user, $emailToInvite);
                        if ($invitations && count($invitations) > 0)
                            $sendInvitation = false;

                        if ($sendInvitation) {

                            // Getting existing guests matching email, and take first 1
                            $guest = null;
                            $guests = GuestDao::getInstance()->getListByEmail($emailToInvite);
                            if ($guests && count($guests) > 0) {
                                $guest = $guests[0];
                            }

                            $token = sha1(uniqid(rand()));

                            // Send invite email
                            $message = str_replace("\n", "<br/>", $initialMessage);
                            $message .= "<br/><br/>";
                            $message .= sprintf(__("<a href=\"%s\">S'inscrire</a> ou <a href=\"%s\">Refuser</a>", "s1b"), HTTPHelper::Link(Urls::SUBSCRIBE), HTTPHelper::Link(Urls::REFUSE_INVITATION, array("Token" => $token, "Email" => $emailToInvite)));
                            $message .= "<br/><br/>";
                            $message .= "<strong>" . sprintf(__("L'équipe %s", "s1b"), Constants::SITENAME) . "<strong/>";
                            MailSvcImpl::getNewInstance($user->getEmail(), $user->getEmail())->send($emailToInvite, sprintf(__("Invitation à rejoindre %s", "s1b"), Constants::SITENAME), $message);

                            // Create invitation
                            $invitation = new Invitation();
                            $invitation->setSender($user);
                            $invitation->setToken($token);
                            $invitation->setLast_modification_date(new \DateTime);

                            if ($guest) {
                                // Updating guest
                                $guest->addInvitations($invitation);
                                GuestDao::getInstance()->update($guest);
                            } else {
                                // Create guest
                                $guest = new Guest();
                                $guest->setEmail($emailToInvite);
                                $guest->setCreation_date(new \DateTime);
                                $guest->addInvitations($invitation);
                                GuestDao::getInstance()->add($guest);
                            }

                            Flash::addItem(sprintf(__("Une invitation a été envoyée à %s.", "s1b"), $emailToInvite));
                        }
                    }
                }
                HTTPHelper::redirectToHome();
            }
        }
    }
}