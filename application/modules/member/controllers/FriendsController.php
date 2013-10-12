<?php
use Sb\Trace\Trace;
use Sb\Helpers\ArrayHelper;
use Sb\Flash\Flash;
use Sb\Helpers\HTTPHelper;
use Sb\Db\Dao\MessageDao;
use Sb\Db\Dao\FriendShipDao;
use Sb\Db\Dao\UserDao;
use Sb\Db\Model\FriendShip;
use Sb\Lists\PaginatedList;
use Sb\Entity\Constants;
use Sb\Helpers\MailHelper;
use Sb\Db\Model\Message;
use Sb\Db\Model\User;
use Sb\Service\MailSvc;

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
            $newFriendShip = new FriendShip;
            $newFriendShip->setCreationDate(new \DateTime);
            $newFriendShip->setUser_source($user);
            $newFriendShip->setUser_target($requestedUser);
            FriendShipDao::getInstance()->add($newFriendShip);
            
            // send email to the requested user
            MailSvc::getInstance()->send($requestedUser->getEmail(), sprintf(__("%s - Vous avez reçu une demande d'ami.", "s1b"), Constants::SITENAME), MailHelper::friendRequestEmailBody($user->getUserName()));
            
            // add message line for requestedUser
            $message = new Message;
            $message->setRecipient($requestedUser);
            $message->setSender($user);
            $message->setDate(new \DateTime);
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
    
    private function filterBySearchTerm(User $user) {

        global $searchTerm;
        if (preg_match("/$searchTerm/i", $user->getFirstName()) || preg_match("/$searchTerm/i", $user->getLastName()) || preg_match("/$searchTerm/i", $user->getUserName())) {return true;}
        return false;
    }

}
