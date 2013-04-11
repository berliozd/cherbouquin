<?php

namespace Sb\Db\Service;

use Sb\Db\Model\UserBook;
use \Sb\Db\Model\UserEvent;
use Sb\Form\UserBook as UserBookForm;
use Sb\Trace\Trace;
use Sb\Db\Dao\UserEventDao;
use Sb\Db\Dao\UserBookDao;
use Sb\Db\Dao\UserDao;
use Sb\Entity\EventTypes;

/**
 *
 * @author Didier
 */
class UserEventSvc extends \Sb\Db\Service\Service {

    private static $instance;

    const USER_LAST_EVENT_OF_TYPE = "USER_LAST_EVENT_OF_TYPE";
    const LAST_EVENT_OF_TYPE = "LAST_EVENT_OF_TYPE";
    const FRIENDS_LAST_EVENT_OF_TYPE = "FRIENDS_LAST_EVENT_OF_TYPE";

    /**
     *
     * @return \Sb\Db\Service\UserEventSvc
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Service\UserEventSvc();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct(\Sb\Db\Dao\UserEventDao::getInstance(), "UserEvent");
    }

    public function prepareUserBookEvents(UserBook $oldUserBook, UserBookForm $newUserBook) {

        $userEvents = array();

        try {
            if ($oldUserBook->getRating() != $newUserBook->getRating()) {
                $userEvent = new \Sb\Db\Model\UserEvent;
                $userEvent->setItem_id($oldUserBook->getId());
                $userEvent->setUser($oldUserBook->getUser());
                $userEvent->setNew_value($newUserBook->getRating());
                $userEvent->setOld_value($oldUserBook->getRating());
                $userEvent->setType_id(\Sb\Entity\EventTypes::USERBOOK_RATING_CHANGE);
                $userEvents[] = $userEvent;
            }

            if ($oldUserBook->getIsBlowOfHeart() != $newUserBook->getIsBlowOfHeart()) {
                $userEvent = new \Sb\Db\Model\UserEvent;
                $userEvent->setItem_id($oldUserBook->getId());
                $userEvent->setUser($oldUserBook->getUser());
                $userEvent->setNew_value($newUserBook->getIsBlowOfHeart());
                $userEvent->setOld_value($oldUserBook->getIsBlowOfHeart());
                $userEvent->setType_id(\Sb\Entity\EventTypes::USERBOOK_BLOWOFHEART_CHANGE);
                $userEvents[] = $userEvent;
            }

            if ($oldUserBook->getHyperlink() != $newUserBook->getHyperLink()) {
                $userEvent = new \Sb\Db\Model\UserEvent;
                $userEvent->setItem_id($oldUserBook->getId());
                $userEvent->setUser($oldUserBook->getUser());
                // Removing http:// or https:// from url
                $newHyperlink = str_replace("http://", "", $newUserBook->getHyperLink());
                $newHyperlink = str_replace("https://", "", $newHyperlink);
                $userEvent->setNew_value($newHyperlink);
                $userEvent->setOld_value($oldUserBook->getHyperlink());
                $userEvent->setType_id(\Sb\Entity\EventTypes::USERBOOK_HYPERLINK_CHANGE);
                $userEvents[] = $userEvent;
            }

            $oldReadingStateId = ($oldUserBook->getReadingState() ? $oldUserBook->getReadingState()->getId() : -1);
            if ($oldReadingStateId != $newUserBook->getReadingStateId()) {
                $userEvent = new \Sb\Db\Model\UserEvent;
                $userEvent->setItem_id($oldUserBook->getId());
                $userEvent->setUser($oldUserBook->getUser());
                $userEvent->setNew_value($newUserBook->getReadingStateId());
                $userEvent->setOld_value(($oldUserBook->getReadingState() ? $oldUserBook->getReadingState()->getId() : null));
                $userEvent->setType_id(\Sb\Entity\EventTypes::USERBOOK_READINGSTATE_CHANGE);
                $userEvents[] = $userEvent;
            }

            if ($oldUserBook->getReview() != $newUserBook->getReview()) {
                $userEvent = new \Sb\Db\Model\UserEvent;
                $userEvent->setItem_id($oldUserBook->getId());
                $userEvent->setUser($oldUserBook->getUser());
                $userEvent->setNew_value($newUserBook->getReview());
                $userEvent->setOld_value($oldUserBook->getReview());
                $userEvent->setType_id(\Sb\Entity\EventTypes::USERBOOK_REVIEW_CHANGE);
                $userEvents[] = $userEvent;
            }

            if ($oldUserBook->getIsWished() != $newUserBook->getIsWished()) {
                $userEvent = new \Sb\Db\Model\UserEvent;
                $userEvent->setItem_id($oldUserBook->getId());
                $userEvent->setUser($oldUserBook->getUser());
                $userEvent->setNew_value($newUserBook->getIsWished());
                $userEvent->setOld_value($oldUserBook->getIsWished());
                $userEvent->setType_id(\Sb\Entity\EventTypes::USERBOOK_WISHEDSTATE_CHANGE);
                $userEvents[] = $userEvent;
            }
        } catch (\Exception $exc) {
            Trace::addItem("Une erreur s'est produite lors de la préparation des événements liés au userbook");
        }
        return $userEvents;
    }

    public function persistAll($userEvents) {
        try {
            if ($userEvents && count($userEvents) > 0) {
                foreach ($userEvents as $userEvent) {
                    $this->getDao()->add($userEvent);
                }
            }
        } catch (\Exception $exc) {
            Trace::addItem("Une erreur s'est produite lors de l'enregistrement des uservents");
        }
    }

    public function getLastEventsOfType($typeId = null, $maxResult = 10) {
        try {
            $dataKey = self::LAST_EVENT_OF_TYPE . "_tid_" . $typeId . "_m_" . $maxResult;
            $result = $this->getData($dataKey);
            if ($result === false) {
                $result = UserEventDao::getInstance()->getListLastEventsOfType($typeId, $maxResult);
                // Looping all events and set the book item for each
                foreach ($result as $event) {
                    // Make the event richer with book and contributors
                    $event = $this->getFullBookRelatedUserEvent($event);
                }
                $this->setData($dataKey, $result);
            }
            return $result;
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
    }

    public function getFriendsLastEventsOfType($userId, $typeId) {
        try {
            $dataKey = self::FRIENDS_LAST_EVENT_OF_TYPE . "_uid_" . $userId . "_tid_" . $typeId;
            $result = $this->getData($dataKey);
            if ($result === false) {
                $result = UserEventDao::getInstance()->getListUserFriendsUserEventsOfType($userId, $typeId);
                // Looping all events and set the book item for each
                foreach ($result as $event) {
                    // Make the event richer with book and contributors
                    $event = $this->getFullBookRelatedUserEvent($event);
                }
                $this->setData($dataKey, $result);
            }
            return $result;
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
    }

    /**
     * Get last events of a certain type for a user
     * @param type $userId
     * @param type $typeId
     * @return type
     */
    public function getUserLastEventsOfType($userId, $typeId = null, $maxResult = 10) {
        try {
            $dataKey = self::USER_LAST_EVENT_OF_TYPE . "_uid_" . $userId . "_tid_" . $typeId . "_m_" . $maxResult;
            $result = $this->getData($dataKey);
            if ($result === false) {
                $result = UserEventDao::getInstance()->getListUserUserEventsOfType($userId, $typeId, $maxResult);
                // Looping all events and set nested members depending on event type
                foreach ($result as $event) {
                    switch ($event->getType_id()) {
                        case EventTypes::USERBOOK_REVIEW_CHANGE:
                            $event = $this->getFullBookRelatedUserEvent($event);
                            break;
                        case EventTypes::USER_ADD_FRIEND:
                            $friend = UserDao::getInstance()->get($event->getNew_value());
                            /*
                             * IMPORTANT !!!
                             */
                            // Do not remove line below : accessing a property (here username) is done to properly initialize the proxy object
                            $friend->setUserName($friend->getUserName());
                            // Do not remove line below : set user userbooks list
                            $userbooks = new \Doctrine\Common\Collections\ArrayCollection(UserBookDao::getInstance()->getListAllBooks($friend->getId(), true));
                            $friend->setUserBooks($userbooks);

                            /**
                             * End IMPORTANT
                             */
                            if ($friend)
                                $event->setFriend($friend);
                            break;

                        default:
                            break;
                    }
                }
                $this->setData($dataKey, $result);
            }
            return $result;
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
    }

    /**
     * Get a full UserEvent object related to a book with all members initialised
     * This is necessary for storing the object in cache otherwise when getting the object from cahc (and detach from database) 
     * these members won't be initialized
     * @param \Sb\Db\Model\UserEvent $event
     */
    private function getFullBookRelatedUserEvent(UserEvent $event) {
        // If item_id is not null, we get the userbook item from db
        if ($event->getItem_id()) {
            $userbook = UserBookDao::getInstance()->get($event->getItem_id());
            if ($userbook) {
                $book = $userbook->getBook();
                $contributors = \Sb\Db\Dao\ContributorDao::getInstance()->getListForBook($book->getId());
                $book->setContributors($contributors);
                $event->setBook($book);
            }
            return $event;
        }else
            return $event;
    }

}