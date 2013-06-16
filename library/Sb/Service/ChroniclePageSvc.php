<?php

namespace Sb\Service;

use Sb\Db\Model\UserBook;
use Sb\Model\ChroniclePage;
use Sb\Db\Dao\ChronicleDao;
use Sb\Adapter\ChronicleAdapter;
use Sb\Db\Model\Chronicle;
use Sb\Entity\PressReviewTypes;
use Sb\Db\Service\PressReviewSvc;

/**
 * Description of ChroniclePageSvc
 * @author Didier
 */
class ChroniclePageSvc extends Service {

    const CHRONICE_PAGE = "CHRONICE_PAGE";

    private static $instance;

    protected function __construct() {

        parent::__construct("ChroniclePage");
    }

    /**
     *
     * @return ChroniclePageSvc
     */
    public static function getInstance() {

        if (!self::$instance)
            self::$instance = new ChroniclePageSvc();
        return self::$instance;
    }

    public function get($chronicleId) {

        try {
            
            $key = self::CHRONICE_PAGE . "_id_" . $chronicleId;
            
            $result = $this->getData($key);
            
            if ($result === false) {
                
                $result = new ChroniclePage();
                
                $chronicle = ChronicleDao::getInstance()->get($chronicleId);
                
                if ($chronicle) {
                    
                    // Set chronicle
                    $result->setChronicle($chronicle);
                    
                    // Set chronicleViewModel
                    $chronicleAdapter = new ChronicleAdapter($chronicle);
                    $chronicleViewModel = $chronicleAdapter->getAsChronicleViewModel(3, 5, 5, false);
                    $result->setChronicleViewModel($chronicleViewModel);
                    
                    // Set press reviews
                    $result->setPressReviews($chronicleViewModel->getPressReviews());
                    
                    // Set same author chronicles
                    $result->setSameAuthorChronicles($chronicleViewModel->getSameAuthorChronicles());
                    
                    // Set similar chronicles
                    $result->setSimilarChronicles($chronicleViewModel->getSimilarChronicles());
                    
                    // Set user book reviews
                    $result->setUserBooksReviews($this->getUserBooksReviews($chronicle));
                    
                    // Set viedo press review
                    $result->setVideoPressReview($this->getVideoPressReview($chronicle));
                    
                    $this->setData($key, $result);
                } else
                    return null;
            }
            
            return $result;
        } catch (\Exception $e) {
            $this->logException(get_class(), __FUNCTION__, $e);
        }
    }

    /**
     * Get user book reviews if chronicle is associated to a book
     * @param Chronicle $chronicle the current chronicle to get the book and the reviews from
     * @return array of UserBook or NULL
     */
    private function getUserBooksReviews(Chronicle $chronicle) {

        if ($chronicle->getBook()) {
            
            // book reviews
            $userBooks = $chronicle->getBook()
                ->getNotDeletedUserBooks();
            
            $result = array();
            
            foreach ($userBooks as $userBook) {
                /* @var $userBook UserBook */
                if ($userBook->getReview())
                    $result[] = $userBook;
            }
        }
        
        return null;
    }

    private function getVideoPressReview(Chronicle $chronicle) {

        if ($chronicle->getBook()) {
            
            $criteria = array(
                    "type" => array(
                            false,
                            "=",
                            PressReviewTypes::VIDEO
                    ),
                    "book" => array(
                            true,
                            "=",
                            $chronicle->getBook()
                    )
            );
            
            $video = PressReviewSvc::getInstance()->getList($criteria, 1, false);
            if ($video && count($video) == 1)
                return $video[0];
        }
        
        return null;
    }

}