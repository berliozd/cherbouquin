<?php

namespace Sb\Db\Service;

use Sb\Db\Dao\ChronicleDao;
use Sb\Db\Model\Book;
use Sb\Db\Dao\ContributorDao;
use Sb\Entity\GroupTypes;

/**
 * Description of ChronicleSvc
 *
 * @author Didier
 */
class ChronicleSvc extends Service {

    const LAST_ANY_GROUPS_CHRONICLES = "LAST_CHRONICLES_OF_ANY_GROUPS";
    const LAST_BLOGGERS_CHRONICLES = "LAST_BLOGGERS_CHRONICLES";
    const LAST_BOOK_STORES_CHRONICLES = "LAST_BOOK_STORES_CHRONICLES";
    const CHRONICLES_SAME_TYPE = "CHRONICLES_SAME_TYPE";

    private static $instance;

    /**
     * Get singleton
     * @return \Sb\Db\Service\ChronicleSvc
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new ChronicleSvc();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct(ChronicleDao::getInstance(), "Chronicle");
    }

    public function getLastChroniclesOfAnyType() {
        return $this->getLastChronicles(4, null);
    }

    public function getLastBookStoresOfAnyType() {
        return $this->getLastChronicles(3, GroupTypes::BOOK_STORE);
    }

    public function getLastBloggersChronicles() {
        return $this->getLastChronicles(3, GroupTypes::BLOGGER);

    }

    private function getLastChronicles($nbOfItems, $groupType) {

        try {

            $excludeGroupTypes = null;

            switch ($groupType) {
            case GroupTypes::BLOGGER:
                $key = self::LAST_BLOGGERS_CHRONICLES;
                break;
            case GroupTypes::BOOK_STORE:
                $key = self::LAST_BOOK_STORES_CHRONICLES;
                break;
            default:
                $key = self::LAST_ANY_GROUPS_CHRONICLES;
                // When getting any groups chronicle, we don't want bloggers and book stores chronicles
                $excludeGroupTypes = GroupTypes::BLOGGER . "," . GroupTypes::BOOK_STORE;
                break;
            }

            $results = $this->getData($key);

            if ($results === false) {
                /* @var $dao ChronicleDao */
                $dao = $this->getDao();
                $results = $dao->getLastChronicles(100, $groupType, $excludeGroupTypes);

                foreach ($results as $result) {
                    if ($result->getBook())
                        $result->setBook($this->getFullBookRelatedUserEvent($result->getBook()));
                }

                $this->setData($key, $results);
            }
            return array_slice($results, $nbOfItems);
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
    }

    /**
     * Get a collection of Chronicle of a certain type from cache or from db if not in cache
     * @param int $type
     * @return Collection chronicle:
     */
    public function getChroniclesOfType($type) {

        try {

            $numberOfChronicles = 4;

            $key = self::CHRONICLES_SAME_TYPE . "_t_" . $type . "_m_" . $numberOfChronicles;

            $results = $this->getData($key);

            if ($results === false) {
                /* @var $dao ChronicleDao */
                $dao = $this->getDao();
                $results = $dao->getChroniclesOfType($type, $numberOfChronicles);

                $this->setData($key, $results);
            }
            return $results;
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
    }

    /**
     * Get a full book with all members initialised
     * This is necessary for storing the object in cache otherwise when getting the object from cache (and then detach from database)
     * these members won't be initialized
     * @param Book $book
     */
    private function getFullBookRelatedUserEvent(Book $book) {
        $contributors = ContributorDao::getInstance()->getListForBook($book->getId());
        $book->setContributors($contributors);
        return $book;
    }

}
