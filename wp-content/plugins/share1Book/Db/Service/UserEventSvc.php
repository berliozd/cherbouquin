<?php

namespace Sb\Db\Service;

use \Sb\Db\Model\UserBook;
use \Sb\Form\UserBook as UserBookForm;
use \Sb\Trace\Trace;

/**
 *
 * @author Didier
 */
class UserEventSvc extends \Sb\Db\Service\Service {

    private static $instance;

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
            Trace::addItem("Une erreur s'est produite lors de l'enregistrment des uservents");
        }
    }

}