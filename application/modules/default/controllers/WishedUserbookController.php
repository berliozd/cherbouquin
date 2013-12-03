<?php
use Sb\Flash\Flash;
use Sb\Db\Dao\UserBookDao;
use Sb\Db\Model\UserBookGift;
use Sb\Db\Dao\UserBookGiftDao;
use Sb\Helpers\HTTPHelper;
use Sb\ZendForm\WishListSearchForm;
use Sb\Db\Dao\UserDao;
use Sb\Trace\Trace;

class Default_WishedUserbookController extends Zend_Controller_Action {

    private $connectedUSerFound = false;
    
    public function init() {

    }

    public function indexAction() {

    }

    public function setAsOfferedAction() {

        $dest = (HTTPHelper::getReferer() ? HTTPHelper::getReferer() : HTTPHelper::Link());
        $id = $this->_getParam('ubid', -1);
        // Checking if passed id is > 0
        if ($id > 0) {
            $userBook = UserBookDao::getInstance()->get($id);
            // Checking if id passed matches a user book
            if ($userBook) {
                // Checking if user book not set as offered already
                if (!$userBook->getActiveGiftRelated()) {
                    $userBookGift = new UserBookGift();
                    $userBookGift->setUserbook($userBook);
                    global $globalContext;
                    $connectedUser = $globalContext->getConnectedUser();
                    $userBookGift->setOfferer($connectedUser);
                    $userBookGift->setIs_active(true);
                    if (UserBookGiftDao::getInstance()->add($userBookGift)) {
                        Flash::addItem(__("Le livre a correctement été marqué 'déjà acheté'.", "s1b"));
                        $this->_redirect($dest);
                        exit();
                    }
                }
            }
        }
        Flash::addItem(__("une erreur s'est produite et le livre n'a pas pu être marqué 'déjà acheté'.", "s1b"));
        $this->_redirect($dest);
        exit();
    }

    public function searchListAction() {

        global $globalContext;
        
        // Check the form validity
        $form = new WishListSearchForm();
        if (!$form->isValid($_GET)) {
            
            Flash::addItems($form->getFailureMessages());
            HTTPHelper::redirectToReferer();
        } else {
            $searchTerm = $this->_getParam('wishedListSearchTerm', "");
            Trace::addItem($searchTerm);
            $users = UserDao::getInstance()->getListByKeywordAndWishedUserBooks($searchTerm);
            
            // Remove connected user and admin user
            $cleanedUsers = $this->cleanUsersList($users);
            
            // Display specific message when connected user found in list
            if ($this->connectedUSerFound)
                Flash::addItem(__("Si vous cherchez votre liste, c'est raté ;-) La surprise n'en sera que plus grande.", "s1b"));
            
            if (count($cleanedUsers) == 0) {
                
                // Getting user without wish list
                $usersWithoutWishList = UserDao::getInstance()->getListByKeyword($searchTerm);
                $cleanedUsersWithoutWishList = $this->cleanUsersList($usersWithoutWishList);
                
                if (count($cleanedUsersWithoutWishList) != 0)
                    Flash::addItem(sprintf(__("Aucun utilisateur '%s' n'a créé de liste d'envies ou bien sa liste est privée.", "s1b"), $searchTerm));
                else
                    Flash::addItem(__("Aucun utilisateur ne correspond à votre recherche.", "s1b"));
                
                HTTPHelper::redirectToReferer();
            }
            $this->view->users = $cleanedUsers;
            $this->view->form = $form;
        }
    }

    /**
     * Remove current connected user (if one) and admin user (id=1)
     * @param array of User $users
     * @return array of User
     */
    private function cleanUsersList($users) {

        $cleanedUsers = array();
        global $globalContext;
        
        foreach ($users as $user) {
            
            if ($globalContext->getConnectedUser() && ($globalContext->getConnectedUser()
                ->getId() == $user->getId()))
                $this->connectedUSerFound = true;
                
                // Don't add connected user and Admin
            if (($globalContext->getConnectedUser() && ($globalContext->getConnectedUser()
                ->getId() != $user->getId())) || !$globalContext->getConnectedUser()) {
                if ($user->getId() != 1)
                    $cleanedUsers[] = $user;
            }
        }
        
        return $cleanedUsers;
    }

}

