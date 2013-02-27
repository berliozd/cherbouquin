<?php

namespace Sb\Service;

use Sb\Model\HeaderInformation;
use Sb\Helpers\StringHelper;
use Sb\Entity\Constants;
use Sb\Db\Model\Tag;
use Sb\Db\Model\Book;
use Sb\Db\Service\TagSvc;

/**
 * Retrieve header information (tile, meta, etc..) for different context (book pages, etc...)
 *
 * @author Didier
 */
class HeaderInformationSvc extends Service {

    private static $instance;

    /**
     * 
     * @return HeaderInformationSvc
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new HeaderInformationSvc();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct("HeaderInformation");
    }

    /**
     * Get a HeaderInformation from a book object : used on book pages
     * @param \Sb\Service\Sb\Db\Model\Book $book a book object 
     * @return \Sb\Model\HeaderInformation HeaderInformation object for book pages
     */
    public function get(Book $book) {

        try {
            $result = new HeaderInformation;

            // Set title tag
            $publisherName = "";
            if ($book->getPublisher())
                $publisherName = $book->getPublisher()->getName();

            // For tag title, maximum length recommended is 60
            $title = StringHelper::tronque(sprintf(__("%s : %s de %s par %s", "s1b"), Constants::SITENAME, $book->getTitle(), $book->getOrderableContributors(), $publisherName), 67);            
            $result->setTitle($title);

            // Set description
            // For meta description, maximum length recommended is 160
            $description = StringHelper::tronque($book->getDescription(), 157);            
            // Remove double quotes
            $result->setDescription(str_replace("\"", "" , $description));

            // Set keywords
            // Get 2 first tags for keywords
            $bookTags = TagSvc::getInstance()->getTagsForBooks(array($book));
            $tags = "";
            if ($bookTags && count($bookTags) > 0) {
                $firstTags = array_slice($bookTags, 0, 5);
                $firstTagNames = array_map(array(&$this, "getTagName"), $firstTags);
                $tags = implode(" | ", $firstTagNames);
            }
            $keywords = sprintf(__("%s | %s | %s", "s1b"), $book->getTitle(), $book->getOrderableContributors(), $publisherName);
            if ($tags != "")
                $keywords = sprintf(__("%s | %s | %s | %s", "s1b"), $book->getTitle(), $book->getOrderableContributors(), $publisherName, $tags);                
            
            // Remove double quotes
            $keywords = str_replace("\"", "", $keywords);                
            $result->setKeywords($keywords);

            return $result;
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
    }

    private function getTagName(Tag $tag) {
        return $tag->getLabel();
    }

}