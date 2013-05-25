<?php

namespace Sb\Service;

use Sb\Model\FullBook;
use Sb\Db\Model\Book;
use Sb\Db\Model\UserBook;
use Sb\Db\Dao\BookDao;
use Sb\Db\Service\BookSvc;
use Sb\Db\Service\PressReviewSvc;
use Sb\Db\Service\UserBookSvc;
use Sb\Db\Service\ChronicleSvc;
use Sb\Db\Service\TagSvc;
use Sb\Entity\PressReviewTypes;

/**
 * Description of FullBookSvc
 * @author Didier
 */
class FullBookSvc extends Service {

    const FULL_BOOK = "FULL_BOOK";

    private static $instance;

    protected function __construct() {

        parent::__construct("FullBook");
    }

    /**
     *
     * @return FullBookSvc
     */
    public static function getInstance() {

        if (!self::$instance)
            self::$instance = new FullBookSvc();
        return self::$instance;
    }

    public function get($bookId) {

        try {
            
            $key = self::FULL_BOOK . "_id_" . $bookId;
            
            $result = $this->getData($key);
            
            if ($result === false) {
                $result = new FullBook();
                
                $book = BookDao::getInstance()->get($bookId);
                
                $result->setBook($book);
                
                $booksAlsoLiked = BookSvc::getInstance()->getBooksAlsoLiked($bookId, false);
                $result->setBooksAlsoLiked($booksAlsoLiked);
                
                $booksWithSameAuthor = BookSvc::getInstance()->getBooksWithSameContributors($bookId, false);
                $result->setBooksWithSameAuthor($booksWithSameAuthor);
                
                $booksWithSameTags = BookSvc::getInstance()->getBooksWithSameTags($bookId, false);
                $result->setBooksWithSameTags($booksWithSameTags);
                
                $lastlyReadUserbooks = UserBookSvc::getInstance()->getLastlyReadUserbookByBookId($bookId, 5, false);
                $result->setLastlyReadUserbooks($lastlyReadUserbooks);
                
                $reviewedUserBooks = $this->getReviewedUserBooks($book->getNotDeletedUserBooks());
                $result->setReviewedUserBooks($reviewedUserBooks);
                
                $pressReviews = $this->getBookPressReviews($book);
                $result->setPressReviews($pressReviews);
                
                $relatedChronicles = $this->getChroniclesRelativeToBook($book);
                $result->setRelatedChronicles($relatedChronicles);
                
                $videoPressReviews = PressReviewSvc::getInstance()->getList($book->getId(), PressReviewTypes::VIDEO, 1, false);
                if ($videoPressReviews) {
                    $video = $videoPressReviews[0];
                    $result->setVideoPressReview($video);
                }
                
                $this->setData($key, $result);
            }
            
            return $result;
        } catch (\Exception $e) {
            $this->logException(get_class(), __FUNCTION__, $e);
        }
    }

    private function getBookPressReviews(Book $book) {

        $bookPressReviews = PressReviewSvc::getInstance()->getList($book->getId(), PressReviewTypes::ARTICLE, 3, false);
        
        // If not enough press reviews associated to book, getting general press reviews
        if (!$bookPressReviews || count($bookPressReviews) < 3) {
            
            // Get general press reviews
            $generalPressReviews = PressReviewSvc::getInstance()->getList(null, PressReviewTypes::ARTICLE, 3, false);
            
            if (!$bookPressReviews) {
                
                $bookPressReviews = $generalPressReviews;
            } else {
                if ($generalPressReviews) {
                    foreach ($generalPressReviews as $generalPressReview) {
                        /* @var $generalPressReview PressReview */
                        $add = true;
                        foreach ($bookPressReviews as $bookPressReview) {
                            /* @var $bookPressReview PressReview */
                            if ($generalPressReview->getId() == $bookPressReview->getId()) {
                                $add = false;
                                break;
                            }
                        }
                        
                        if ($add)
                            $bookPressReviews[] = $generalPressReview;
                    }
                }
            }
        }
        
        if ($bookPressReviews)
            $bookPressReviews = array_slice($bookPressReviews, 0, 3);
        
        return $bookPressReviews;
    }

    private function getChroniclesRelativeToBook(Book $book) {

        $chronicles = null;
        
        // Get book userbook's tag
        $bookTags = TagSvc::getInstance()->getTagsForBooks(array(
                $book
        ), false);        
        $bookTagIds = null;
        foreach ($bookTags as $bookTag) {
            /* @var $bookTag Tag */
            $bookTagIds[] = $bookTag->getId();
        }
        
        // Get 3 chronicles with same tags
        if ($bookTags && count($bookTags) > 0)
            $chronicles = ChronicleSvc::getInstance()->getChroniclesWithTags($bookTagIds, 3, false); //
                                                                                                  
        // Get last chronicles of any types and add them to previously set list of chronicles
        if (!$chronicles || count($chronicles) < 3) {
            $lastChronicles = ChronicleSvc::getInstance()->getLastChronicles(3);
            foreach ($lastChronicles as $lastChronicle) {
                
                $add = true;
                if ($chronicles) {
                    foreach ($chronicles as $chronicle) {
                        if ($chronicle->getId() == $lastChronicle->getId()) {
                            $add = false;
                            break;
                        }
                    }
                }
                
                if ($add)
                    $chronicles[] = $lastChronicle;
            }
        }
        
        if ($chronicles)
            $chronicles = array_slice($chronicles, 0, 3);
        
        return $chronicles;
    }

    private function getReviewedUserBooks($userBooks) {

        $results = array();
        
        foreach ($userBooks as $userBook) {
            
            /* @var $userBook UserBook */
            if ($userBook->getReview())
                $results[] = $userBook;
        }
        
        return $results;
    }

}