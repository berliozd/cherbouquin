<?php

namespace Sb\Db\Service;

use Sb\Db\Model\Book;
use Sb\Db\Dao\TagDao;
use Sb\Trace\Trace;

/**
 *
 * @author Didier
 */
class TagSvc extends \Sb\Db\Service\Service {

    const ALL_TAGS = "ALL_TAGS";
    const TAGS_BOOK = "TAGS_BOOK";

    private static $instance;

    /**
     *
     * @return \Sb\Db\Service\TagSvc
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Service\TagSvc();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct(\Sb\Db\Dao\TagDao::getInstance(), "Tag");
    }

    public function getAllTags($ordeColumn) {
        $dataKey = self::ALL_TAGS;
        $data = $this->getData($dataKey);
        if ($data === false) {
            $data = $this->getDao()->getAll(array(), array($ordeColumn => "ASC"));
            $this->setData($dataKey, $data);
        }
        return $data;
    }

    /**
     * Get a list of tags for the list of books
     * @param array of Book $books : list of books
     * @return array of Tag list of tags
     */
    public function getTagsForBooks($books, $useCache = true) {
        
        Trace::addItem("useCache : " . $useCache);

        try {
            
            $data = null;
            
            $bookIds = array_map(array(&$this, "getId"), $books);

            if ($useCache) {
                $dataKey = self::TAGS_BOOK . "_bids_" . implode("_", $bookIds);                
                $data = $this->getData($dataKey);                
            }
            
            // If no data are cached
            if (!isset($data) || $data === false) {
                $data = TagDao::getInstance()->getTagsForBooks($bookIds);
                if ($useCache) {
                    Trace::addItem("setting cache");
                    $this->setData($dataKey, $data);
                }
                    
            }

            return $data;
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
        return null;
    }

    /**
     * Get the book id, called in array_map
     * @param \Sb\Db\Model\Book $book
     * @return type
     */
    private function getId(Book $book) {
        return $book->getId();
    }

}
