<?php
use Sb\Db\Model\Book,
    Sb\Db\Model\User,
    Sb\Db\Dao\BookDao,
    Sb\Db\Dao\UserBookDao,
    Sb\Db\Dao\UserEventDao,
    Sb\Db\Service\BookSvc,
    Sb\Db\Service\UserEventSvc,
    Sb\Authentification\Service\AuthentificationSvc,
    Sb\Entity\EventTypes,
    Sb\View\LastReviews,
    Sb\View\Components\UserReadingWidget,
    Sb\View\Components\UserWishedBooksWidget,
    Sb\View\Components\Ad,
    Sb\View\Components\TwitterWidget,
    Sb\View\Components\FacebookFrame,
    Sb\View\Components\CreateChroniclesLinks,
    Sb\Trace\Trace,
    Sb\View\Components\WishListSearchWidget,
    Sb\Helpers\HTTPHelper,
    Sb\Entity\Urls,
    Sb\Facebook\Service\FacebookSvc,
    Sb\Flash\Flash;

class Member_IndexController extends Zend_Controller_Action {

    private $blowOfHeartFriendsBooksId = null;

    private $context = null;

    public function init() {

        // Checks is user is connected
        AuthentificationSvc::getInstance()->checkUserIsConnected();

        $globalContext = new \Sb\Context\Model\Context();
        $this->context = $globalContext;
    }

    /**
     * Show member home page action
     * @global type $globalContextMe
     */
    public function indexAction() {

        try {
            $globalContext = new \Sb\Context\Model\Context();

            /* @var $connectedUser User */
            $connectedUser = $globalContext->getConnectedUser();

            // Getting friends boh
            $blowOfHeartFriendsBooks = BookDao::getInstance()->getListBOHFriends($connectedUser->getId());
            $this->view->isShowingFriendsBOH = false;
            if (!$blowOfHeartFriendsBooks || count($blowOfHeartFriendsBooks) < 5) {
                // Setting class property with array of friend boh books ids to use it in "notInArray" function below
                $this->blowOfHeartFriendsBooksId = array_map(array(
                    &$this, "getId"
                ), $blowOfHeartFriendsBooks);
                // Getting all users boh
                $blowOfHeartBooks = BookSvc::getInstance()->getBOHForUserHomePage();
                $blowOfHeartBooks = array_filter($blowOfHeartBooks, array(
                    &$this, "notInArray"
                ));
                // Merging 2 arrays
                if ($blowOfHeartFriendsBooks && $blowOfHeartBooks)
                    $blowOfHeartBooks = array_merge($blowOfHeartFriendsBooks, $blowOfHeartBooks);
                $blowOfHeartBooks = array_slice($blowOfHeartBooks, 0, 5);
            } else {
                $this->view->isShowingFriendsBOH = true;
                $blowOfHeartBooks = $blowOfHeartFriendsBooks;
            }
            $this->view->blowOfHeartBooks = $blowOfHeartBooks;

            // Getting friends user events
            $this->view->userEvents = UserEventDao::getInstance()->getListUserFriendsUserEvents($connectedUser->getId());

            // Getting top books
            $this->view->topsBooks = BookSvc::getInstance()->getTopsUserHomePage();

            // Getting last review by friends
            $lastReviews = UserEventSvc::getInstance()->getFriendsLastEventsOfType($connectedUser->getId(), EventTypes::USERBOOK_REVIEW_CHANGE);
            $this->view->lastReviews = $lastReviews;
            $this->view->lastReviewsView = new LastReviews($lastReviews, __("<strong>Dernières critiques postées par vos amis</strong>", "s1b"));

            // Getting User Reading Widget
            $allCurrentlyReadingUserBooks = UserBookDao::getInstance()->getCurrentlyReadingsNow($connectedUser->getId());
            $userReading = new UserReadingWidget($connectedUser, $allCurrentlyReadingUserBooks, true);
            // If more than one book as 'being read', we need to set the javascript carousel
            if (count($allCurrentlyReadingUserBooks) > 1) {
                $this->view->placeholder('footer')->append("<script src=\"" . $globalContext->getBaseUrl() . 'Resources/js/simple-carousel/simple.carousel.js' . "\"></script>\n");
                $this->view->placeholder('footer')->append("<script>$(function() {initCarousel('carousel-currentreadings', 298, 210)});</script>\n");
            }
            $this->view->userReading = $userReading;

            // Getting user wished books widget
            $userWishedBooks = new UserWishedBooksWidget($connectedUser, true);
            $this->view->userWishedBooks = $userWishedBooks;

            // Getting wish list search widget
            $this->view->wishListSearchWidget = new WishListSearchWidget();

            // Getting the ad (second paramters is not used anymore)
            $this->view->ad = new Ad("user_homepage", "6697829998");

            // Getting twitter widget
            $this->view->twitter = new TwitterWidget();

            // Getting facebook frame
            $this->view->facebookFrame = new FacebookFrame();

            // Get create chronicle links widget
            if ($connectedUser->getIs_partner() && $connectedUser->getGroupusers()) {
                $createChroniclesLink = new CreateChroniclesLinks($connectedUser->getGroupusers());
                $this->view->createChroniclesLinkView = $createChroniclesLink->get();
            }
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function logOffAction() {

        try {

            $globalConfig = new Sb\Config\Model\Config();

            if (isset($_COOKIES) && array_key_exists("PHPSESSID", $_COOKIES)) {
                unset($_COOKIES["PHPSESSID"]);
            }

            // destruction du cookie de connexion PHPSESSID 3600 correspond à 60 min
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 3600, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
            }

            $tmpLang = null;
            if (isset($_SESSION) && array_key_exists('WPLANG', $_SESSION))
                $tmpLang = $_SESSION['WPLANG'];
            session_destroy();

            $_SESSION['WPLANG'] = $tmpLang;

            $facebookSvc = new FacebookSvc($globalConfig->getFacebookApiId(), $globalConfig->getFacebookSecret(), HTTPHelper::Link(Urls::USER_HOME), HTTPHelper::Link(Urls::LOGIN), HTTPHelper::Link(Urls::LOGIN));
            $faceBookUser = $facebookSvc->getUser();
            $facebookSvc->cleanUser();
            if ($faceBookUser) {
                HTTPHelper::redirect($facebookSvc->getFacebookLogOutUrl());
            }

            Flash::addItem(__("Déconnexion réussie", "s1b"));

            // Redirecting to login page
            HTTPHelper::redirect("");


        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    private function notInArray(Book $book) {

        return !in_array($book->getId(), $this->blowOfHeartFriendsBooksId);
    }

    private function getId(Book $book) {

        return $book->getId();
    }

}

