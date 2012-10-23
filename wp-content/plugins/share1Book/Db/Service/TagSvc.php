<?php

namespace Sb\Db\Service;

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

}
