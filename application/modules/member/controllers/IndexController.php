<?php
use Sb\Db\Model\Book;
use Sb\Db\Model\User;
use Sb\Db\Dao\BookDao;
use Sb\Db\Dao\UserBookDao;
use Sb\Db\Dao\UserEventDao;
use Sb\Db\Service\BookSvc;
use Sb\Db\Service\UserEventSvc;
use Sb\Authentification\Service\AuthentificationSvc;
use Sb\Entity\EventTypes;
use Sb\View\Components\AutoPromoWishlistWidget;
use Sb\View\LastReviews;
use Sb\View\Components\UserReadingWidget;
use Sb\View\Components\UserWishedBooksWidget;
use Sb\View\Components\Ad;
use Sb\View\Components\TwitterWidget;
use Sb\View\Components\FacebookFrame;
use Sb\View\Components\CreateChroniclesLinks;
use Sb\Trace\Trace;
class Member_IndexController extends Zend_Controller_Action {

    private $blowOfHeartFriendsBooksId = null;

    private $context = null;

    public function init() {
        
        // Checks is user is connected
        AuthentificationSvc::getInstance()->checkUserIsConnected();
        
        global $globalContext;
        $this->context = $globalContext;
    }

    /**
     * Show member home page action
     * @global type $globalContextMe
     */
    public function indexAction() {
        try {
            global $globalContext;
            
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
            
            // Getting auto prom wishlist widget
            $this->view->autoPromoWishList = new AutoPromoWishlistWidget();
            
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
        } catch ( \Exception $e ) {
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

