<?php

namespace Sb\Db\Service;

use Sb\Db\Dao\ChronicleDao,
    Sb\Entity\GroupTypes,
    Sb\Helpers\StringHelper,
    Sb\Db\Dao\GroupTypeDao,
    Sb\Db\Dao\TagDao,
    Sb\Db\Dao\UserDao,
    Sb\Db\Dao\BookDao,
    Sb\Db\Model\Chronicle,
    Sb\Entity\ChronicleType,
    Sb\Entity\ChronicleLinkType;

/**
 * Description of ChronicleSvc
 * @author Didier
 */
class ChronicleSvc extends AbstractService {

    const ANY_GROUPS_CHRONICLES = "CHRONICLES_OF_ANY_GROUPS";
    const BLOGGERS_CHRONICLES = "BLOGGERS_CHRONICLES";
    const BOOK_STORES_CHRONICLES = "BOOK_STORES_CHRONICLES";
    const CHRONICLES_WITH_TAG = "CHRONICLES_WITH_TAG";
    const CHRONICLES_WITH_KEYWORDS = "CHRONICLES_WITH_KEYWORDS";
    const AUTHORS_CHRONICLES = "AUTHORS_CHRONICLES";
    const CHRONICLE_FROM_USER_BOOK = "CHRONICLE_FROM_USER_BOOK";

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

    public function getLastAnyType() {

        return $this->getLastChronicles(4, null, null);
    }

    public function getLastChroniclesNotBloggersOrBookStores() {

        // When getting any group types chronicles, we don't want bloggers and book stores chronicles
        $excludeGroupTypes = GroupTypes::BLOGGER . "," . GroupTypes::BOOK_STORE;
        return $this->getLastChronicles(4, null, $excludeGroupTypes);
    }

    public function getLastBookStoresChronicles() {

        return $this->getLastChronicles(4, GroupTypes::BOOK_STORE);
    }

    public function getLastBloggersChronicles() {

        return $this->getLastChronicles(4, GroupTypes::BLOGGER);
    }

    public function getLastChronicles($nbOfItems, $groupTypeId = null, $excludeGroupTypesIds = null, $useCache = true, $searchTerm = null, $orderBy = null, $tagId = null) {

        try {

            switch ($groupTypeId) {
                case GroupTypes::BLOGGER :
                    $key = self::BLOGGERS_CHRONICLES;
                    break;
                case GroupTypes::BOOK_STORE :
                    $key = self::BOOK_STORES_CHRONICLES;
                    break;
                default :
                    $key = self::ANY_GROUPS_CHRONICLES;
                    break;
            }

            $key = $key . "_m_" . $nbOfItems;
            if ($excludeGroupTypesIds)
                $key .= "_eg_" . str_replace(",", "_", $excludeGroupTypesIds);
            if ($orderBy)
                $key .= "_ob_" . $orderBy[0] . "_" . $orderBy[1];

            $results = $this->getData($key);

            if ($results === false || !$useCache) {
                /* @var $dao ChronicleDao */
                $dao = $this->getDao();

                $criterias = $this->getCriterias($searchTerm, $groupTypeId, $excludeGroupTypesIds, $tagId);

                $newOrderBy = array();
                if ($orderBy)
                    $newOrderBy[$orderBy[0]] = $orderBy[1];
                else
                    $newOrderBy["creation_date"] = "DESC";
                $results = $dao->getList($criterias, $newOrderBy, 100);

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
     * Prepare and return criteria array
     * @param string $searchTerm
     * @param int $groupTypeId
     * @param string $excludedGroupTypeIds list of group types to exclude separated by a comma
     * @return multitype:multitype:boolean NULL multitype:boolean string unknown multitype:boolean string Ambigous <\Sb\Db\Dao\Ambigous, multitype:, \Doctrine\ORM\mixed,
     * \Doctrine\ORM\Internal\Hydration\mixed, \Doctrine\DBAL\Driver\Statement, string> multitype:boolean string Ambigous <\Sb\Db\Model\Model, object, NULL, unknown, string, string>
     */
    private function getCriterias($searchTerm = null, $groupTypeId = null, $excludedGroupTypeIds = null, $tagId = null) {

        $criteria = array();

        // Add keywords criteria
        if ($searchTerm)
            $criteria["keywords"] = array(
                    false,
                    "LIKE",
                    $searchTerm
            );

            // Add single group type criteria
        if ($groupTypeId) {
            $groupType = GroupTypeDao::getInstance()->get($groupTypeId);
            $criteria["group.type"] = array(
                    true,
                    "=",
                    $groupType
            );
        }

        // Add excluded group types criteria
        if ($excludedGroupTypeIds) {
            $groupTypeCriteria = array(
                    "id" => array(
                            false,
                            "IN",
                            $excludedGroupTypeIds
                    )
            );
            $excludedGroupTypes = GroupTypeDao::getInstance()->getList($groupTypeCriteria, null, null);
            $criteria["group.type"] = array(
                    true,
                    "NOT IN",
                    $excludedGroupTypes
            );
        }

        // Add tag criteria
        $tag = null;
        if ($tagId) {
            $tag = TagDao::getInstance()->get($tagId);
            if ($tag) {
                $criteria["tag"] = array(
                        true,
                        "=",
                        $tag
                );
            }
        } else {
            // Add tag criteria only to be returned in results
            $criteria["tag"] = array(
                    true,
                    null,
                    null
            );
        }

        // Add user criteria only to be returned in results
        $criteria["user"] = array(
                true,
                null,
                null
        );

        // Add book criteria only to be returned in results
        $criteria["book"] = array(
                true,
                null,
                null
        );

        // Add is_validated criteria
        $criteria["is_validated"] = array(
                false,
                "=",
                1
        );

        return $criteria;
    }

    /**
     * Get a collection of chronicle with a tag specified
     * @param array of int $tagIds the tag ids to search
     * @param int $numberOfChronicles number of maximum chronicle to get
     * @return Collection of Chronicle
     */
    public function getChroniclesWithTags($tagIds, $numberOfChronicles, $useCache = true) {

        try {

            $results = null;

            if ($useCache) {
                $key = self::CHRONICLES_WITH_TAG . "_tid_" . implode("_", $tagIds) . "_m_" . $numberOfChronicles;
                $results = $this->getData($key);
            }

            if (!isset($results) || $results === false) {
                /* @var $dao ChronicleDao */
                $dao = $this->getDao();

                $criteria = array();

                // Add tag criteria
                $tags = TagDao::getInstance()->getList(array(
                        "id" => array(
                                false,
                                "IN",
                                implode(",", $tagIds)
                        )
                ), null, null);
                $criteria["tag"] = array(
                        true,
                        "IN",
                        $tags
                );
                // Add is_validated criteria
                $criteria["is_validated"] = array(
                        false,
                        "=",
                        1
                );

                $orderBy = array(
                        "creation_date" => "DESC"
                );

                $results = $dao->getList($criteria, $orderBy, $numberOfChronicles);

                if ($useCache)
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
    public function getChroniclesWithKeywords($keywords, $numberOfChronicles, $useCache = true) {

        try {

            $results = null;

            if ($useCache) {
                // Get cache key : sanitize keywords string and replace "-" by "_"
                $key = self::CHRONICLES_WITH_KEYWORDS . "_k_" . str_replace("-", "_", StringHelper::sanitize(implode("_", $keywords))) . "_m_" . $numberOfChronicles;
                $results = $this->getData($key);
            }

            if (!isset($results) || $results === false) {
                /* @var $dao ChronicleDao */
                $dao = $this->getDao();

                $criteria = array();

                // Add is_validated criteria
                $criteria["is_validated"] = array(
                        false,
                        "=",
                        1
                );

                // Add keywords criteria
                $criteria["keywords"] = array(
                        false,
                        "LIKE",
                        $keywords
                );

                $orderBy = array(
                        "creation_date" => "DESC"
                );

                $results = $dao->getList($criteria, $orderBy, $numberOfChronicles);

                if ($useCache)
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
    public function getAuthorChronicles($authorId, $useCache = true) {

        try {

            $numberOfChronicles = 10;

            $results = null;

            if ($useCache) {
                $key = self::AUTHORS_CHRONICLES . "_aid_" . $authorId . "_m_" . $numberOfChronicles;
                $results = $this->getData($key);
            }

            if (!isset($results) || $results === false) {
                /* @var $dao ChronicleDao */
                $dao = $this->getDao();

                $criteria = array();

                // Add is_validated criteria
                $criteria["is_validated"] = array(
                        false,
                        "=",
                        1
                );

                $author = UserDao::getInstance()->get($authorId);
                $criteria["user"] = array(
                        true,
                        "=",
                        $author
                );

                $orderBy = array(
                        "nb_views" => "DESC"
                );

                $results = $dao->getList($criteria, $orderBy, $numberOfChronicles);

                if ($useCache)
                    $this->setData($key, $results);
            }
            return $results;
        } catch (\Exception $e) {
            $this->logException(get_class(), __FUNCTION__, $e);
        }
    }

    /**
     * Add or update a chronicle based on a userbook review
     *
     * @param \Sb\Db\Model\UserBook $userBook
     * @param unknown $groupId
     */
    public function addOrUpdateFromUserBook(\Sb\Db\Model\UserBook $userBook)
    {
        $bloggerGroupId = 2;
        /* @var $user \Sb\Db\Model\User */
        $user = $userBook->getUser();
        /* @var $user \Sb\Db\Model\Book */
        $book = $userBook->getBook();

        /* @var $existingChronicle \Sb\Db\Model\Chronicle */
        $chronicle = $this->getChronicle($user->getId(), $book->getId());
        if (is_null($chronicle)) {
            $chronicle = new Chronicle();
        }

        $chronicle->setUser($user);
        $chronicle->setBook($userBook->getBook());
        $chronicle->setCreation_date(new \DateTime());
        $chronicle->setGroup(\Sb\Db\Dao\GroupDao::getInstance()->get($bloggerGroupId));
        $chronicle->setIs_validated(true);
        $chronicle->setLink($userBook->getHyperlink()? 'http://' . $userBook->getHyperlink(): '');
        $chronicle->setLink_type(ChronicleLinkType::URL);
        $chronicle->setText($userBook->getReview());
        $chronicle->setTitle($book->getTitle());
        $chronicle->setType_id(ChronicleType::BOOK_CHRONICLE);
        $chronicle->setKeywords($book->getTitle() . ', '. $book->getOrderableContributors() . ', '. $book->getPublisher()->getName());
        $tags = $userBook->getTags();
        if (count($tags) > 0) {
            $chronicle->setTag($userBook->getTags()->first());
        }

        $this->getDao()->add($chronicle);
    }

    /**
     * Return a chronicle from a user id and a book id
     *
     * @param unknown $userId
     * @param unknown $bookId
     * @param string $useCache
     * @return \Sb\Db\Dao\Ambigous|NULL
     */
    public function getChronicle($userId, $bookId, $useCache = true) {

        try {

            $results = null;

            if ($useCache) {
                $key = self::CHRONICLE_FROM_USER_BOOK . "_uid_" . $userId . "_bid_" . $bookId;
                $result = $this->getData($key);
            }

            if (!isset($results) || $results === false) {
                $criteria["user"] = array(true, "=", UserDao::getInstance()->get($userId));
                $criteria["book"] = array(true, "=", BookDao::getInstance()->get($bookId));
                $result = $this->getDao()->getList($criteria, array('id' => 'DESC'), 1);
            }

            if ($useCache)
                $this->setData($key, $results);

            if (is_array($result) && count($result) > 0) {
                return $result[0];
            }
            return null;

        } catch (\Exception $e) {
            $this->logException(get_class(), __FUNCTION__, $e);
        }

        return $results;
    }
}
