<?php

namespace Sb\Db\Service;

use Sb\Db\Dao\PressReviewDao;
use Sb\Db\Model\Model;
use Sb\Db\Model\Media;
use Sb\Db\Dao\MediaDao;

/**
 * Description of PressReviewSvc
 * @author Didier
 */
class PressReviewSvc extends Service {

    const LST = "LST";

    private static $instance;

    /**
     * Get singleton
     * @return \Sb\Db\Service\PressReviewSvc
     */
    public static function getInstance() {

        if (!self::$instance)
            self::$instance = new PressReviewSvc();
        return self::$instance;
    }

    protected function __construct() {

        parent::__construct(PressReviewDao::getInstance(), "PressReview");
    }

    public function getList($criteria = null, $maxResults = null, $useCache = true) {

        try {
            $result = null;
            
            // Build cache key and try to get result in cache
            if ($useCache) {
                $key = $this->getListCacheKey($criteria);
                $result = $this->getData($key);
            }
            
            // if result not retrieved, get it
            if (!isset($result) || $result === false) {
                
                $result = $this->getListResult($criteria);
                
                // set the cache if wanted
                if ($useCache)
                    $this->setData($key, $result);
            }
            
            // Get only the wanted number of items
            if (isset($maxResults))
                $result = array_slice($result, 0, $maxResults);
            
            return $result;
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
    }

    private function getListCacheKey($criteria) {

        $key = self::LST;
        $key .= "_m_100";
        
        if (isset($criteria)) {
            foreach ($criteria as $arrayKey => $arrayValue) {
                if (isset($arrayValue)) {
                    if ($arrayValue instanceof Model)
                        $key .= "_" . $arrayKey . "_" . $arrayValue->getId();
                    else {
                        // in that case $arrayValue is an array and contains operator (=, LIKE) as first element and value to compare as second element
                        $key .= "_" . $arrayKey . "_" . $arrayValue[1];
                    }
                }
            }
        }
        
        return $key;
    }

    private function getListResult($criteria) {
        
        /* @var $dao PressReviewDao */
        $dao = $this->getDao();
        $result = $dao->getList($criteria, array(
                "date" => "DESC"
        ), 100);
        
        foreach ($result as $pressReview) {
            
            if ($pressReview->getMedia()) {
                
                /* @var $media Media */
                $media = MediaDao::getInstance()->get($pressReview->getMedia()
                    ->getId());
                
                /*
                 * IMPORTANT !!!
                 */
                // Do not remove line below : accessing a property is done to properly initialize the proxy object
                $mediaWebsite = $media->getWebsite();
                $pressReview->setMedia($media);
            }
        }
        
        return $result;
    }

}
