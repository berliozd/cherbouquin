<?php

use Sb\Db\Model\UserBook;
use Sb\Db\Dao\UserBookDao;
use Sb\Db\Dao\UserDao;
use Sb\Db\Service\BookSvc;
use Sb\Db\Service\UserEventSvc;
use Sb\Db\Service\UserSvc;
use Sb\Entity\EventTypes;
use Sb\Flash\Flash;
use Sb\Helpers\HTTPHelper;
use Sb\Helpers\SecurityHelper;
use Sb\Authentification\Service\AuthentificationSvc;

class Default_UsersController extends Zend_Controller_Action {

    public function init() {
        AuthentificationSvc::getInstance()->checkUserIsConnected();
    }

    public function profileAction() {

        global $globalContext;

        $noUser = true;
        $friendId = $this->_getParam("uid");

        if ($friendId) {

            $friend = UserDao::getInstance()->get($friendId);

            $this->view->friend = $friend;

            if ($friend) {

                $noUser = false;

                if ($friend->getId() == $globalContext->getConnectedUser()->getId()) {

                    Flash::addItem(__("Il s'agit de votre profil!", "s1b"));
                    HTTPHelper::redirectToReferer();
                } else {

                    $requestingUser = $globalContext->getConnectedUser();
                    if (SecurityHelper::IsUserAccessible($friend, $requestingUser)) {
                        $this->view->friendSetting = $friend->getSetting();

                        $this->view->isFriend = UserSvc::getInstance()->areUsersFriends($globalContext->getConnectedUser(), $friend);
                        $this->view->isFriend = true;

                        // getting currently reading or lastly read books
                        $currentlyReading = UserBookDao::getInstance()->getReadingNow($friend->getId());
                        $lastlyReads = UserBookDao::getInstance()->getListLastlyRead($friend->getId());
                        if ($currentlyReading && $lastlyReads) {
                            $this->view->currentlyReadingOrLastlyReadBooks = array_merge(array($currentlyReading), $lastlyReads);
                        } elseif ($lastlyReads) {
                            $this->view->currentlyReadingOrLastlyReadBooks = $lastlyReads;
                        } elseif ($currentlyReading) {
                            $this->view->currentlyReadingOrLastlyReadBooks = array($currentlyReading);
                        }

                        // Getting friend currently reading user books
                        $this->view->allCurrentlyReadingUserBooks = UserBookDao::getInstance()->getCurrentlyReadingsNow($friend->getId());
                        if (count($this->view->allCurrentlyReadingUserBooks) > 1) {
                            $this->view->placeholder('footer')->append("<script src=\"" . $globalContext->getBaseUrl() . 'Resources/js/simple-carousel/simple.carousel.js' . "\"></script>\n");
                            $this->view->placeholder('footer')->append("<script>$(function() {initCarousel('carousel-currentreadings', 298, 190)});</script>\n");
                        }

                        // Getting friend last boh books
                        $bohUserBooks = UserBookDao::getInstance()->getListUserBOH($friend->getId());
                        $this->view->bohBooks = array_map(array($this, "getBook"), $bohUserBooks);

                        // Getting books friend could like
                        $this->view->booksHeCouldLikes = BookSvc::getInstance()->getBooksUserCouldLike($friend->getId());
                        if ($this->view->booksHeCouldLikes && (count($this->view->booksHeCouldLikes) > 0)) {
                            $this->view->placeholder('footer')->append("<script src=\"" . $globalContext->getBaseUrl() . 'Resources/js/waterwheel-carousel/jquery.waterwheelCarousel.min.js' . "\"></script>\n");
                            $this->view->placeholder('footer')->append("<script>$(function() {initCoverFlip('bookUserCouldLike', 90)});</script>\n");
                        }

                        // Getting friend's friends last reviews
                        $this->view->friendLastReviews = UserEventSvc::getInstance()->getUserLastEventsOfType($friend->getId(), EventTypes::USERBOOK_REVIEW_CHANGE);

                        // Getting friend last friends added events
                        $this->view->friendLastFriendsAddedEvents = UserEventSvc::getInstance()->getUserLastEventsOfType($friend->getId(), EventTypes::USER_ADD_FRIEND);
                        if (count($this->view->friendLastFriendsAddedEvents) > 1) {
                            $this->view->placeholder('footer')->append("<script src=\"" . $globalContext->getBaseUrl() . 'Resources/js/simple-carousel/simple.carousel.js' . "\"></script>\n");
                            $this->view->placeholder('footer')->append("<script>$(function() {initCarousel('carousel-friendlastfriends', 298, 85)});</script>\n");
                        }

                        // Getting friend last events
                        $this->view->friendLastEvents = UserEventSvc::getInstance()->getUserLastEventsOfType($friend->getId(), null, 15);
                        $this->view->placeholder('footer')->append("<script>\n
                            toInit.push(\"attachUserEventsExpandCollapse()\");\n
                            function attachUserEventsExpandCollapse() {_attachExpandCollapseBehavior(\"js_userLastEvents\", \"userEvent\", \"Voir moins d'activités\", \"Voir plus d'activités\");}\n
                        </script>\n");
                    } else {

                        Flash::addItem(__("Vous ne pouvez pas accéder à ce profil.", "s1b"));
                        HTTPHelper::redirectToReferer();
                    }
                }
            }
        }

        if ($noUser) {
            Flash::addItem(__("Cet utilisateur n'existe pas.", "s1b"));
            HTTPHelper::redirectToReferer();
        }
    }

    private function getBook(UserBook $userBook) {
        return $userBook->getBook();
    }

}

