<?php

namespace Sb\Db\Service;

use Sb\Db\Dao\GroupChronicleDao;
use Sb\Db\Model\Book;
use Sb\Db\Dao\ContributorDao;
use Sb\Entity\GroupTypes;

/**
 * Description of GroupChronicleSvc
 *
 * @author Didier
 */
class GroupChronicleSvc extends Service {

    const LAST_ANY_GROUPS_CHRONICLES = "LAST_CHRONICLES_OF_ANY_GROUPS";
    const LAST_BLOGGERS_CHRONICLES = "LAST_BLOGGERS_CHRONICLES";
    const LAST_BOOK_STORES_CHRONICLES = "LAST_BOOK_STORES_CHRONICLES";

    private static $instance;

    /**
     * Get singleton
     * @return \Sb\Db\Service\GroupChronicleSvc
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new GroupChronicleSvc();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct(GroupChronicleDao::getInstance(), "GroupChronicle");
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

    function getLastChronicles($nbOfItems, $type) {

        try {

        	$excludeGroupTypes = null;
        	
            switch ($type) {
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
                /* @var $dao GroupChronicleDao */
                $dao = $this->getDao();
                $results = $dao->getLastChronicles(100, $type, $excludeGroupTypes);

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
