<?php

namespace Sb\Db\Service;

use Sb\Db\Dao\GroupChronicleDao;

/**
 * Description of GroupChronicleSvc
 *
 * @author Didier
 */
class GroupChronicleSvc extends Service {

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
        
        $key = __FUNCTION__;

        $result = $this->getData($key);

        if (!$result) {
            
            $result = $this->getDao()->getLast();
            $this->setData($key, $result);
        }
        return $result;
    }   

}