<?php

namespace Sb\Db\Service;

use Sb\Db\Dao\ChronicleDao;
use Sb\Db\Model\Book;
use Sb\Entity\GroupTypes;
use Sb\Helpers\StringHelper;

/**
 * Description of ChronicleSvc
 * @author Didier
 */
class ChronicleSvc extends Service {

    const LAST_ANY_GROUPS_CHRONICLES = "LAST_CHRONICLES_OF_ANY_GROUPS";

    const LAST_BLOGGERS_CHRONICLES = "LAST_BLOGGERS_CHRONICLES";

    const LAST_BOOK_STORES_CHRONICLES = "LAST_BOOK_STORES_CHRONICLES";

    const CHRONICLES_WITH_TAG = "CHRONICLES_WITH_TAG";

    const CHRONICLES_WITH_KEYWORDS = "CHRONICLES_WITH_KEYWORDS";

    const AUTHORS_CHRONICLES = "AUTHORS_CHRONICLES";

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
        
        // When getting any group types chronicles, we don't want bloggers and book stores chronicles
        $excludeGroupTypes = GroupTypes::BLOGGER . "," . GroupTypes::BOOK_STORE;
        return $this->getLastChronicles(4, null, $excludeGroupTypes);
    }

    public function getLastBookStoresChronicles() {

        return $this->getLastChronicles(3, GroupTypes::BOOK_STORE);
    }

    public function getLastBloggersChronicles() {

        return $this->getLastChronicles(3, GroupTypes::BLOGGER);
    }

    public function getLastChronicles($nbOfItems, $groupType = null, $excludeGroupTypes = null, $useCache = true, $searchTerm = null, $orderBy = null) {

        try {
            
            switch ($groupType) {
                case GroupTypes::BLOGGER :
                    $key = self::LAST_BLOGGERS_CHRONICLES;
                    break;
                case GroupTypes::BOOK_STORE :
                    $key = self::LAST_BOOK_STORES_CHRONICLES;
                    break;
                default :
                    $key = self::LAST_ANY_GROUPS_CHRONICLES;
                    break;
            }
            
            $key = $key . "_m_" . $nbOfItems;
            if ($excludeGroupTypes)
                $key .= "_eg_" . str_replace(",", "_", $excludeGroupTypes);
            if ($orderBy)
                $key .= "_ob_" . $orderBy[0] . "_" . $orderBy[1];
            
            $results = $this->getData($key);
            
            if ($results === false || !$useCache) {
                /* @var $dao ChronicleDao */
                $dao = $this->getDao();
                $results = $dao->getLastChronicles(100, $groupType, $excludeGroupTypes, $searchTerm, $orderBy);
                
//                 foreach ($results as $result) {
//                     if ($result->getBook())
//                         $result->setBook($this->getFullBookRelatedUserEvent($result->getBook()));
//                 }
                
                if ($useCache)
                    $this->setData($key, $results);
            }
            
            $results = array_slice($results, 0, $nbOfItems);
            return $results;
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
    }

    /**
     * Get a collection of chronicle with a tag specified
     * @param array of int $tagIds the tag ids to search
     * @param int $numberOfChronicles number of maximum chronicle to get
     * @return Collection of Chronicle
     */
    public function getChroniclesWithTags($tagIds, $numberOfChronicles) {

        try {
            
            $key = self::CHRONICLES_WITH_TAG . "_tid_" . implode("_", $tagIds) . "_m_" . $numberOfChronicles;
            
            $results = $this->getData($key);
            
            if ($results === false) {
                /* @var $dao ChronicleDao */
                $dao = $this->getDao();
                $results = $dao->getChroniclesWithTags($tagIds, $numberOfChronicles);
                
                $this->setData($key, $results);
            }
            return $results;
        } catch (\Exception $e) {
            $this->logException(get_class(), __FUNCTION__, $e);
        }
    }

    /**
     * Get a collection of chronicles with same keywords
     * @param Array of String $keywords the keywords to search chronicle having one of them
     * @param int $numberOfChronicles number of maximum chronicle to get
     * @return Collection of chronicle
     */
    public function getChroniclesWithKeywords($keywords, $numberOfChronicles) {

        try {
            
            // Get cache key : sanitize keywords string and replace "-" by "_"
            $key = self::CHRONICLES_WITH_KEYWORDS . "_k_" . str_replace("-", "_", StringHelper::sanitize(implode("_", $keywords))) . "_m_" . $numberOfChronicles;
            
            $results = $this->getData($key);
            
            if ($results === false) {
                /* @var $dao ChronicleDao */
                $dao = $this->getDao();
                $results = $dao->getChroniclesWithKeywords($keywords, $numberOfChronicles);
                
                $this->setData($key, $results);
            }
            return $results;
        } catch (\Exception $e) {
            $this->logException(get_class(), __FUNCTION__, $e);
        }
    }

    /**
     * Get a collection of chronicle for a certain author (user) ordered by number of views descending
     * @param int $authorId
     * @return Collection of chronicle
     */
    public function getAuthorChronicles($authorId) {

        try {
            
            $numberOfChronicles = 10;
            $key = self::AUTHORS_CHRONICLES . "_aid_" . $authorId . "_m_" . $numberOfChronicles;
            
            $results = $this->getData($key);
            
            if ($results === false) {
                /* @var $dao ChronicleDao */
                $dao = $this->getDao();
                $results = $dao->getChroniclesOfAuthor($authorId, $numberOfChronicles);
                
                $this->setData($key, $results);
            }
            return $results;
        } catch (\Exception $e) {
            $this->logException(get_class(), __FUNCTION__, $e);
        }
    }

    /**
     * Get a full book with all members initialised
     * This is necessary for storing the object in cache otherwise when getting the object from cache (and then detach from database)
     * these members won't be initialized
     * @param Book $book
//      */
//     private function getFullBookRelatedUserEvent(Book $book) {

//         $contributors = ContributorDao::getInstance()->getListForBook($book->getId());
//         $book->setContributors($contributors);
//         return $book;
//     }

}
