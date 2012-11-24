<?php

namespace Sb\Wrapper;

/**
 * Description of AjaxAction
 *
 * @author Didier
 */
class AjaxActions {

    // Private properties
    /**
     *
     * @var share1Book
     */
    private $s1b;

    /**
     *
     * @return Config
     */
    private function getConfig() {
        global $s1b;
        return $s1b->getConfig();
    }

    /**
     *
     * @return \Sb\Context\Model\Context
     */
    private function getContext() {
        global $s1b;
        return $s1b->getContext();
    }

    // Contructor
    public function __construct($s1b) {
        $this->s1b = $s1b;
    }

    // Public functions

    public function onAjaxActionSearchABookNagivation() {
        // la page demandée ne nessecite pas d'authentification
        $this->doAjax('searchBookNavigationNonce', 'getSearchABookPageHTML', false, null);
    }

    public function onAjaxActionBooksNavigation() {
        $this->doAjax('navigationNonce', 'getBooksPageOnNavigation', true, null);
    }

    public function onAjaxActionBooksSort() {
        $this->doAjax('sortingNonce', 'getBooksPageOnSorting', true, null);
    }

    public function onAjaxAddUserBook() {
        $this->doAjax('addUserBookNonce', 'addUserBook', true, null);
    }

    private function doAjax($nonceKey, $functionToCall, $needAuthentification, $prmArray) {
        
        try {

            $isFriendLibrary = $_POST['isfriendlibrary'];
            if ($isFriendLibrary == "1") {
                $this->s1b->setIsFriendLibrary(true);
            }

            // prépare le module (auto register des classes, démarrage de la session, initialisation des variables, récupération du user connecté
            $this->s1b->prepare();
            
            // vérification du nonce : correspond t'il bien à un nonce généré plus tôt?
            $this->s1b->checkNonce($nonceKey);
            
            if ($needAuthentification && !$this->s1b->getIsConnected()) {
                Throw new \Sb\Exception\UserException($this->s1b->getMsgNotConnectedUser());
            } else {
                // récupération de la réponse de la fonction a appelée
                if ($prmArray) {
                    $response = call_user_func_array(array(&$this, $functionToCall), $prmArray);
                } else {
                    $response = call_user_func(array(&$this, $functionToCall));
                }
            }
            header("Content-Type: text/html");
            echo $response;
        } catch (\Sb\Exception\UserException $excUser) {
            echo sprintf(__("Error : %s"), $excUser->getMessage());
        } catch (\Exception $exc) {
            echo $exc->getMessage();
        }
        // IMPORTANT: toujours terminer le script!
        exit;
    }

    private function addUserBook() {
        $config = $this->getConfig();
        $return = \Sb\Db\Service\UserBookSvc::getInstance()->addFromPost($this->getContext()->getConnectedUser(), $config);        
        return $return;
    }

    private function getSearchABookPageHTML() {
        $pageId = $_POST['param'];
        
        $_SESSION[\Sb\Entity\SessionKeys::SEARCH_A_BOOK_PAGE_ID] = $pageId;

        $bookSearch = new \Sb\Lists\BookSearch(false, null, $pageId, $this->getConfig()->getSearchNbResultsPerPage(), $this->getContext()->getBaseDirectory(),
                        $this->getConfig()->getSearchNbResultsToShow(), $this->getConfig()->getAmazonApiKey(), $this->getConfig()->getAmazonSecretKey(),
                        $this->getConfig()->getAmazonAssociateTag(), $this->getConfig()->getAmazonNumberOfPageRequested());

        $list = $bookSearch->getList();

        if ($list) {
            $view = new \Sb\View\BookSearch($list->getShownResults(), $list->getPagerLinks(),
                            $list->getFirstItemIdx(), $list->getLastItemIdx(), $list->getNbItemsTot());
            $response = $view->get();
        } else {
            Throw new \Exception(__("Une erreur s'est produite lors de la récupération de la recherche dans la cache."));
        }


        \Sb\Trace\Trace::addItem("utilisation du templating en ajax");
        return $response;
    }

    private function getBooksPageOnNavigation() {
        $fullKey = $this->s1b->formateListKey($_POST["key"]);
        $pageId = $_POST["param"];
        $this->s1b->setListOptionsForNavigation($fullKey, $pageId);
        return $this->getBooks($_POST["key"]);
    }

    private function getBooksPageOnSorting() {
        $fullKey = $this->s1b->formateListKey($_POST["key"]);
        $sortCriteria = $_POST["param"];
        $this->s1b->setListOptionsForSorting($fullKey, $sortCriteria);
        return $this->getBooks($_POST["key"]);
    }

    private function getBooks($key) {
        $books = \Sb\Db\Service\UserBookSvc::getInstance()->getUserBooks($key, $this->getContext()->getLibraryUserId(), false);
        if ($books) {
            return $this->getBooksHTML($key, $books);
        }
    }

    private function getBooksHTML($key, $books) {
        // Prepare list view
        $view = $this->s1b->createBookTableView($key, $books);
        $response = $view->get();
        return $response;
    }

}