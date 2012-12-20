<?php

namespace Sb\Db\Service;

use Sb\Db\Model\Book;
use Sb\Db\Dao\TagDao;

/**
 *
 * @author Didier
 */
class TagSvc extends \Sb\Db\Service\Service {

    const TAGS_DATA_KEY = "tags";

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
        $dataKey = self::TAGS_DATA_KEY;
        $data = $this->getData($dataKey);
        if (!$data) {
            $data = $this->getDao()->getAll(array(), array($ordeColumn => "ASC"));
            $this->setData($dataKey, $data);
        }
        return $data;
    }

    /**
     * Get a list of tags for the list of books
     * @param type $books : list of books
     * @return type list of tags
     */
    public function getTagsForBooks($books) {

        try {
            $bookIds = array_map(array(&$this, "getId"), $books);

            $dataKey = __FUNCTION__ . "_" . implode("_", $bookIds);

            $data = $this->getData($dataKey);
            if (!$data) {

                $data = TagDao::getInstance()->getTagsForBooks($bookIds);
                $this->setData($dataKey, $data);
            }

            return $data;
        } catch (\Exception $exc) {
            $this->logException("TagSvc", __FUNCTION__, $exc);
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
