<?php
use Sb\Trace\Trace;
use Sb\Helpers\ArrayHelper;

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
                $paginatedList = new \Sb\Lists\PaginatedList($friends, 9);
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

    private function filterBySearchTerm(\Sb\Db\Model\User $user) {

        global $searchTerm;
        if (preg_match("/$searchTerm/i", $user->getFirstName()) || preg_match("/$searchTerm/i", $user->getLastName()) || preg_match("/$searchTerm/i", $user->getUserName())) {return true;}
        return false;
    }

}
