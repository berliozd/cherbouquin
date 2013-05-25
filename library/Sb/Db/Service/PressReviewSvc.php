<?php

namespace Sb\Db\Service;

use Sb\Db\Dao\PressReviewDao;

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

    public function getList($bookId = null, $typeId = null, $maxResults = null, $useCache = true) {

        try {
            
            $results = null;
            
            if ($useCache) {
                $key = self::LST;
                if (isset($bookId))
                    $key .= "_bid_" . $bookId;
                if (isset($typeId))
                    $key .= "_tid_" . $typeId;
                $key .= "_m_100";
                
                $results = $this->getData($key);
            }
            
            if (!isset($results) || $results === false) {
                /* @var $dao PressReviewDao */
                $dao = $this->getDao();
                $results = $dao->getLastPressReviews($bookId, $typeId, 100);
                
                if ($useCache)
                    $this->setData($key, $results);
            }
            
            if (isset($maxResults))
                $results = array_slice($results, 0, $maxResults);
            
            return $results;
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
    }

}
