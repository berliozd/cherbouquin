<?php
use Sb\Trace\Trace;
use Sb\Helpers\ArrayHelper;
use Sb\Flash\Flash;
use Sb\Helpers\HTTPHelper;
use Sb\Lists\PaginatedList;
use Sb\Entity\Constants;
use Sb\Helpers\MailHelper;
use Sb\Service\MailSvc;
use Sb\Entity\Urls;
use Sb\Helpers\StringHelper;

use Sb\Db\Model\FriendShip;
use Sb\Db\Model\Message;
use Sb\Db\Model\User;
use Sb\Db\Model\Invitation;
use Sb\Db\Model\Guest;

use Sb\Db\Dao\MessageDao;
use Sb\Db\Dao\FriendShipDao;
use Sb\Db\Dao\UserDao;
use Sb\Db\Dao\InvitationDao;
use Sb\Db\Dao\GuestDao;


/**
 *
 * @author Didier
 */
class Member_FriendsController extends Zend_Controller_Action {

    public function listAction() {

        try {

            global $globalContext;

            $user = $globalContext->getConnectedUser();
            $friends = $user->getAcceptedFriends();

            // filter in case a search query is done
            global $searchTerm;
            $searchTerm = ArrayHelper::getSafeFromArray($_GET, "q", null);
            if ($searchTerm) {
                $friends = array_filter($friends, array(
                    &$this, "filterBySearchTerm"
                ));
            }

            if ($friends && count($friends) > 0) {
                // preparing pagination
                $paginatedList = new PaginatedList($friends, 9);
                $this->view->firstItemIdx = $paginatedList->getFirstPage();
                $this->view->lastItemIdx = $paginatedList->getLastPage();
                $this->view->nbItemsTot = $paginatedList->getTotalPages();
                $this->view->navigation = $paginatedList->getNavigationBar();
                $this->view->friends = $paginatedList->getItems();
            }

            $nbFriends = count($friends);
            $this->view->nbFriends = $nbFriends;
            if ($nbFriends == 0)
                $this->view->noFriendsMessage = __("Aucun amis", "s1b");
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function requestAction() {

        try {

            global $globalContext;
            $user = $globalContext->getConnectedUser();

            $requestFriendId = ArrayHelper::getSafeFromArray($_GET, 'fid', null);
            if ($requestFriendId) {

                // testing if a request to that user has been done or if the requested user is already a friend
                $userFriendShips = $user->getFriendships_as_source();
                if ($userFriendShips && count($userFriendShips)) {
                    foreach ($userFriendShips as $userFriendShip) {
                        if (($userFriendShip->getUser_target()->getId() == $requestFriendId) && ($userFriendShip->getAccepted())) {
                            Flash::addItem(__("Vous êtes déja ami avec cet utilisateur.", "s1b"));
                            HTTPHelper::redirectToReferer();
                        }
                        if (($userFriendShip->getUser_target()->getId() == $requestFriendId) && (!$userFriendShip->getValidated())) {
                            Flash::addItem(__("Une demande a déjà été transmise à cet utilisateur.", "s1b"));
                            HTTPHelper::redirectToReferer();
                        }
                    }
                }

                // testing the accepted or pending frienship that the requested user has initiated
                $requestedUser = UserDao::getInstance()->get($requestFriendId);
                $requestedUserFriendShips = $requestedUser->getFriendships_as_source();
                $connectedUserId = $user->getId();
                if ($requestedUserFriendShips && count($requestedUserFriendShips)) {
                    foreach ($requestedUserFriendShips as $requestedUserFriendShip) {
                        if (($requestedUserFriendShip->getUser_target()->getId() == $connectedUserId) && ($requestedUserFriendShip->getAccepted())) {
                            Flash::addItem(__("Vous êtes déja ami avec cet utilisateur.", "s1b"));
                            HTTPHelper::redirectToReferer();
                        }
                        if (($requestedUserFriendShip->getUser_target()->getId() == $connectedUserId) && (!$requestedUserFriendShip->getValidated())) {
                            Flash::addItem(__("Une demande vous a déjà été transmise de la part de cet utilisateur.", "s1b"));
                            HTTPHelper::redirectToReferer();
                        }
                    }
                }
            } else {
                Flash::addItem(__("Vous devez sélectioner un utilisateur", "s1b"));
                HTTPHelper::redirectToReferer();
            }

            // add friendship line
            $newFriendShip = new FriendShip();
            $newFriendShip->setCreationDate(new \DateTime());
            $newFriendShip->setUser_source($user);
            $newFriendShip->setUser_target($requestedUser);
            FriendShipDao::getInstance()->add($newFriendShip);

            // send email to the requested user
            MailSvc::getInstance()->send($requestedUser->getEmail(), sprintf(__("%s - Vous avez reçu une demande d'ami.", "s1b"), Constants::SITENAME), MailHelper::friendRequestEmailBody($user->getUserName()));

            // add message line for requestedUser
            $message = new Message();
            $message->setRecipient($requestedUser);
            $message->setSender($user);
            $message->setDate(new \DateTime());
            $message->setTitle(__("Demande d'ami", "s1b"));
            $message->setMessage(sprintf(__("Bonjour,<br/><br/>Vous avez reçu une demande d'ami de %s.", "s1b"), $user->getUserName()));
            $message->setIs_read(false);
            MessageDao::getInstance()->add($message);

            Flash::addItem(__("Votre demande a bien été envoyée.", "s1b"));
            HTTPHelper::redirectToReferer();
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    /**
     * Shows a form for selecting friends for emails
     */
    public function selectAction() {

        try {

            global $globalContext;

            $user = $globalContext->getConnectedUser();
            $friends = $user->getFriendsForEmailing();
            $this->sortByUserName($friends);
            $nbRecipients = count($friends);

            if ($nbRecipients <= 0) {
                Flash::addItem(__("Pas de destinataire possible. Vous devez ajouter des amis pour pouvoir envoyer des messages.", "s1b"));
                HTTPHelper::redirectToReferer();
            }

            $this->view->friends = $friends;
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    /**
     * Show friends invite form
     */
    public function showInviteFormAction() {

        try {
            global $globalContext;

            $user = $globalContext->getConnectedUser();
            $this->view->user = $user;

            $this->view->emails = ArrayHelper::getSafeFromArray($_GET, "emails", "");

        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    /**
     * Send invitation to users
     */
    public function inviteAction() {

        try {
            global $globalContext;

            $user = $globalContext->getConnectedUser();
            $continue = true;

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

                                    $friendRequestUrl = HTTPHelper::Link(Urls::USER_FRIENDS_REQUEST, array("fid" => $userInDb->getId()));
                                    Flash::addItem(sprintf(__("Un utilisateur existe déjà avec l'email : %s. <a class=\"link\" href=\"%s\">Envoyer lui une demande d'ami</a>", "s1b"), $emailToInvite, $friendRequestUrl));

                                    // We will stop invitation sending
                                    $continue = false;
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
                                    MailSvc::getNewInstance($user->getEmail(), $user->getEmail())->send($emailToInvite, sprintf(__("Invitation à rejoindre %s", "s1b"), Constants::SITENAME), $message);

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

            // If we arrive here : an error occured, then redirect to the invite form
            HTTPHelper::redirect(Urls::USER_FRIENDS_INVITE, array("emails" => $emails));

        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function friendsOfFriendsAction() {

        try {
            global $globalContext;

            $user = $globalContext->getConnectedUser();
            $friendsFriendShips = \Sb\Db\Dao\FriendShipDao::getInstance()->getFriendsFriendShips($user->getId());
            $friendsFriends = array_map(array(&$this, "getTargetUser"), $friendsFriendShips);
            $friendsFriends = array_filter($friendsFriends, array(&$this, "isNotMe"));
            $friendsFriends = array_filter($friendsFriends, array(&$this, "isNotDeleted"));

            $allUsers = UserDao::getInstance()->getAll();
            $allUsers = array_filter($allUsers, array(&$this, "isNotDeleted"));
            $this->view->nbUsers = count($allUsers);

            if ($friendsFriends && count($friendsFriends) > 0) {
                // preparing pagination
                $paginatedList = new \Sb\Lists\PaginatedList($friendsFriends, 9);
                $this->view->firstItemIdx = $paginatedList->getFirstPage();
                $this->view->lastItemIdx = $paginatedList->getLastPage();
                $this->view->nbItemsTot = $paginatedList->getTotalPages();
                $this->view->navigation = $paginatedList->getNavigationBar();
                $this->view->friendsFriends = $paginatedList->getItems();
            }

        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    private function sortByUserName(&$friends) {

        usort($friends, array(
            &$this, "compareByUserNameAsc"
        ));
    }

    private function filterBySearchTerm(User $user) {

        global $searchTerm;
        if (preg_match("/$searchTerm/i", $user->getFirstName()) || preg_match("/$searchTerm/i", $user->getLastName()) || preg_match("/$searchTerm/i", $user->getUserName())) {
            return true;
        }
        return false;
    }

    private function compareByUserNameAsc(User $friend1, User $friend2) {

        $val1 = strtoupper(call_user_func(array(
            &$friend1, "getUserName"
        )));
        $val2 = strtoupper(call_user_func(array(
            &$friend2, "getUserName"
        )));
        if ($val1 == $val2) {
            return 0;
        }
        return ($val1 < $val2) ? -1 : 1;
    }

    private function getTargetUser(FriendShip $friendShip) {
        return $friendShip->getUser_target();
    }

    private function isNotMe(User $friend) {
        global $globalContext;
        $user = $globalContext->getConnectedUser();
        return $friend->getId() != $user->getId();
    }

    private function isNotDeleted(User $friend) {
        return !$friend->getDeleted();
    }

}
