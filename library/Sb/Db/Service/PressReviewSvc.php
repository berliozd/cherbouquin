<?php

namespace Sb\Db\Service;

use Sb\Db\Dao\PressReviewDao;

/**
 * Description of PressReviewSvc
 * @author Didier
 */
class PressReviewSvc extends Service {

    const LAST_PRESSREVIEWS = "LAST_PRESSREVIEWS";

    const PRESSREVIEW_ONBOOK = "PRESSREVIEW_ONBOOK";

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

    public function getList($maxResults, $typeId) {

        try {
            
            $key = self::LAST_PRESSREVIEWS;
            
            $key = $key . "_m_100_tid_" . $typeId;
            
            $results = $this->getData($key);
            
            if ($results === false) {
                /* @var $dao PressReviewDao */
                $dao = $this->getDao();
                $results = $dao->getLastPressReviews(100, $typeId);
                
                $this->setData($key, $results);
            }
            
            $results = array_slice($results, 0, $maxResults);
            return $results;
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
    }

    public function getListByBookId($bookId, $typeId = null, $maxResults = null) {

        try {
            
            $key = self::PRESSREVIEW_ONBOOK . "_bid_" . $bookId;
            if (isset($typeId))
                $key .= "_tid_" . $typeId;
            
            $key .= "_m_100";
            
            $results = $this->getData($key);
            
            if ($results === false) {
                /* @var $dao PressReviewDao */
                $dao = $this->getDao();
                $results = $dao->getLastPressReviewsForBookId($bookId, $typeId, 100);
                
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
