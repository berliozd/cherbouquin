<?php

namespace Sb\Db\Service;

use Sb\Db\Dao\PressReviewDao;

/**
 * Description of PressReviewSvc
 * @author Didier
 */
class PressReviewSvc extends Service {

    const LAST_PRESSREVIEWS = "LAST_PRESSREVIEWS";

    const LAST_VIDEO_PRESSREVIEW_ONBOOK = "LAST_VIDEO_PRESSREVIEW_ONBOOK";

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

    public function getList($nbOfItems, $typeId) {

        try {
            
            $key = self::LAST_PRESSREVIEWS;
            
            $key = $key . "_m_" . $nbOfItems . "_tid_" . $typeId;
            
            $results = $this->getData($key);
            
            if ($results === false) {
                /* @var $dao PressReviewDao */
                $dao = $this->getDao();
                $results = $dao->getLastPressReviews($nbOfItems, $typeId);
                
                $this->setData($key, $results);
            }
            
            $results = array_slice($results, 0, $nbOfItems);
            return $results;
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
    }

    public function getVideoByBookId($bookId) {

        try {
            
            $key = self::LAST_VIDEO_PRESSREVIEW_ONBOOK;
            
            $key = $key . "_bid_" . $bookId;
            
            $result = $this->getData($key);
            
            if ($result === false) {
                /* @var $dao PressReviewDao */
                $dao = $this->getDao();
                $result = $dao->getLastVideoPressReviewForBookId($bookId);
                
                $this->setData($key, $result);
            }
            
            return $result;
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
    }

}
