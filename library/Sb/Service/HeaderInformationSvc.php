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
            $result = new HeaderInformation();
            
            // Set title tag
            $publisherName = "";
            if ($book->getPublisher())
                $publisherName = $book->getPublisher()
                    ->getName(); //
                                     
            // Get if book is an ebook
            $bookIsEbook = !$book->getISBN10() && (substr($book->getASIN(), 0, 1) == "B");
            
            // For tag title, maximum length recommended is 60
            $titlePrefix = "";
            if ($bookIsEbook)
                $titlePrefix = "ebook "; //
            $title = StringHelper::tronque(sprintf(__("%s de %s par %s", "s1b"), $book->getTitle(), $book->getOrderableContributors(), $publisherName), 60 - strlen("...") - strlen($titlePrefix));
            $title = $titlePrefix . $title;
            $result->setTitle($title);
            
            // Set description
            // For meta description, maximum length recommended is 160
            $descriptionSuffix = "";
            if ($bookIsEbook)
                $descriptionSuffix = " | numérique"; //
            $description = StringHelper::tronque($book->getDescription(), 160 - strlen("...") - strlen($descriptionSuffix));
            $description .= $descriptionSuffix;
            
            // Remove double quotes
            $result->setDescription(str_replace("\"", "", $description));
            
            // Set keywords
            // Get 2 first tags for keywords
            $bookTags = TagSvc::getInstance()->getTagsForBooks(array(
                    $book
            ));
            $tags = "";
            if ($bookTags && count($bookTags) > 0) {
                $firstTags = array_slice($bookTags, 0, 5);
                $firstTagNames = array_map(array(
                        &$this,
                        "getTagName"
                ), $firstTags);
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

    public function getForLastAddedPage($pageNumber, $tagLabel = null) {

        try {
            $result = new HeaderInformation();
            
            $title = $this->addPageAndTag(sprintf(__("%s : derniers livres ajoutés par les membres", "s1b"), Constants::SITENAME), $pageNumber, $tagLabel);
            $description = __("Derniers livres ajoutés par les lecteurs et membres de la communauté|voir derniers livres ajoutés par tag ou catégorie", "s1b");
            $keyWords = __("derniers ajouts|derniers livres", "s1b");
            
            $result->setTitle($title);
            $result->setDescription($description);
            $result->setKeywords($keyWords);
            
            return $result;
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
    }

    public function getForBohPage($pageNumber, $tagLabel = null) {

        try {
            $result = new HeaderInformation();
            
            $title = $this->addPageAndTag(sprintf(__("%s : coups de cœur des lecteurs | livres", "s1b"), Constants::SITENAME), $pageNumber, $tagLabel);
            $description = __("Coups de cœur des lecteurs et membres de la communauté|voir coups de cœur par tag ou catégorie", "s1b");
            $keyWords = __("coups de cœur|coups de cœur lecteurs|coups de cœur membres", "s1b");
            
            $result->setTitle($title);
            $result->setDescription($description);
            $result->setKeywords($keyWords);
            
            return $result;
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
    }

    public function getForTopsPage($pageNumber, $tagLabel = null) {

        try {
            $result = new HeaderInformation();
            
            $title = $this->addPageAndTag(sprintf(__("%s : top des lecteurs | livres", "s1b"), Constants::SITENAME), $pageNumber, $tagLabel);
            $description = __("Top des lecteurs et membres de la communauté|voir tops par tag ou catégorie", "s1b");
            $keyWords = __("top|top lecteurs|top membres", "s1b");
            
            $result->setTitle($title);
            $result->setDescription($description);
            $result->setKeywords($keyWords);
            
            return $result;
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
    }

    public function getForStaticPage($routeName) {

        try {
            $result = new HeaderInformation();
            
            switch ($routeName) {
                case "newsletters" :
                    $result->setTitle(sprintf(__("%s : newsletter", "s1b"), Constants::SITENAME));
                    $result->setDescription("Les newsletters envoyées pour vous tenir informé des parutions, des coups de cœur et commentaires de la communauté");
                    $result->setKeywords("newsletter");
                    break;
                case "pressReviews" :
                    $result->setTitle(sprintf(__("%s : que s'est-il passé dans la presse ?", "s1b"), Constants::SITENAME));
                    $result->setDescription("Les revues de presse envoyées pour vous tenir informé des derniers articles, interviews ou polémiques parus");
                    $result->setKeywords("revue de presse");
                    break;
                case "stepByStep" :
                    $result->setTitle(sprintf(__("%s : aide - pas à pas - comment utiliser le site ?", "s1b"), Constants::SITENAME));
                    $result->setDescription("Si vous avez besoin d'un pas à pas pour utiliser pleinement toutes les fonctionnalités du site");
                    $result->setKeywords("aide|pas à pas");
                    break;
                case "about" :
                    $result->setTitle(sprintf(__("%s : à propos du projet", "s1b"), Constants::SITENAME));
                    $result->setDescription("Comment et pourquoi nous est venu cette idée de communauté autour du livre et de la lecture");
                    $result->setKeywords("à propos|projet|pourquoi|société");
                    break;
                case "press" :
                    $result->setTitle(sprintf(__("%s : la presse et les blogs littéraires parlent de nous", "s1b"), Constants::SITENAME));
                    $result->setDescription("Envoi de communiqués de presse aux médias pour qu'ils parlent de nous | les blogs et blogueurs littéraires qui nous soutiennent");
                    $result->setKeywords("presse|blog|blogueurs littéraires|blogueurs BD");
                    break;
                case "team" :
                    $result->setTitle(sprintf(__("%s : découvrir l'équipe - les collaborateurs", "s1b"), Constants::SITENAME));
                    $result->setDescription("Découvrez l'équipe qui travaille derrière un ordinateur, mais pas que, pour développer la communauté");
                    $result->setKeywords("équipe|collaborateur");
                    break;
                case "howItWorks" :
                    $result->setTitle(sprintf(__("%s : aide - comment ça marche ?", "s1b"), Constants::SITENAME));
                    $result->setDescription("Si vous souhaitez tout comprendre sur l'utilisation du site sans rentrer dans un pas à pas");
                    $result->setKeywords("aide générale");
                    break;
                case "helpUs" :
                    $result->setTitle(sprintf(__("%s : pourquoi et comment nous aider ?", "s1b"), Constants::SITENAME));
                    $result->setDescription("Si vous souhaitez nous donner un coup de pouce sans même vous en rendre compte");
                    $result->setKeywords("nous aider");
                    break;
                default :
                    break;
            }
            
            return $result;
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
    }

    private function getTagName(Tag $tag) {

        return $tag->getLabel();
    }

    private function addPageAndTag($title, $pageNumber, $tagLabel = null) {

        if ($pageNumber > 1)
            $title .= sprintf(__(" - page %s", "s1b"), $pageNumber);
        if ($tagLabel)
            $title .= sprintf(__(" - catégorie %s", "s1b"), $tagLabel);
        return $title;
    }

}