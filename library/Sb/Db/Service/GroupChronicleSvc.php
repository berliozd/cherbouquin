<?php

namespace Sb\Db\Service;

use Sb\Db\Dao\GroupChronicleDao;
use Sb\Db\Model\Book;
use Sb\Db\Dao\ContributorDao;

/**
 * Description of GroupChronicleSvc
 *
 * @author Didier
 */
class GroupChronicleSvc extends Service {

    const LAST_ONE = "LAST_ONE";

    private static $instance;

    /**
     *
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

    /**
     * Get the last added Chronicle , stored in cache
     * @return type
     */
    public function getLast() {
        try {
            $key = self::LAST_ONE;

            $result = $this->getData($key);

            if ($result === false) {
                $result = $this->getDao()->getLast();
                if ($result->getBook())
                    $result->setBook($this->getFullBookRelatedUserEvent($result->getBook()));
                $this->setData($key, $result);
            }
            return $result;
        } catch (Exception $exc) {
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