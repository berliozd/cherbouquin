<?php

namespace Sb\Adapter;

use Sb\Db\Model\Chronicle;
use Sb\Model\ChronicleViewModelLight;
use Sb\Model\ChronicleViewModel;
use Sb\Helpers\StringHelper;
use Sb\Helpers\HTTPHelper;
use Sb\Helpers\UserHelper;
use Sb\Helpers\ChronicleHelper;
use Sb\Entity\ChronicleLinkType;
use Sb\Entity\Urls;
use Sb\Db\Service\ChronicleSvc;

/**
 *
 * @author Didier
 */
class ChronicleAdapter {
    
    /* @var $Chronicle Chronicle */
    private $chronicle;

    /**
     */
    function __construct(Chronicle $chronicle) {

        $this->chronicle = $chronicle;
    }

    /**
     * Get a chronicle as a ChronicleViewModelLight object for home page
     * @return \Sb\Model\ChronicleViewModelLight a chronicle as a ChronicleViewModelLight object
     */
    public function getAsChronicleViewModelLight() {

        $lightChronicle = new ChronicleViewModelLight();
        
        $this->setChronicleViewModelLight($lightChronicle);
        
        return $lightChronicle;
    }

    /**
     * Get a chronicle as ChronicleViewModel object for chronicle detail page
     * @param int $nbSimilarChronicles the number of similar chronicles to return , if not passed (null), then no similar chronicles will be returned
     * @return \Sb\Model\ChronicleViewModel a chronicle as a ChronicleViewModel object
     */
    public function getAsChronicleViewModel($nbSimilarChronicles = null, $nbSameAuthorChronicles = null) {
        
        /* @var $chronicle ChronicleViewModel */
        $chronicleViewModel = new ChronicleViewModel();
        
        // Set common members
        $this->setChronicleViewModelLight($chronicleViewModel);
        
        $chronicleViewModel->setText($this->chronicle->getText());
        
        $chronicleViewModel->setUserName($this->chronicle->getUser()
            ->getUserName());
        $chronicleViewModel->setUserProfileLink(HTTPHelper::Link(Urls::USER_PROFILE, array(
                "uid" => $this->chronicle->getUser()
                    ->getId()
        )));
        $chronicleViewModel->setUserImage(UserHelper::getMediumImageTag($this->chronicle->getUser()));
        
        $chronicleViewModel->setSource($this->chronicle->getSource());
        
        // Set book
        $chronicleViewModel->setChronicleHasBook(false);
        if ($this->chronicle->getBook()) {
            $chronicleViewModel->setChronicleHasBook(true);
            $chronicleViewModel->setBook($this->chronicle->getBook());
        }
        
        // Set link info
        $chronicleViewModel->setLinkCss("pci-link-other");
        $chronicleViewModel->setLinkText(__("En savoir plus", "s1b"));
        switch ($this->chronicle->getLink_type()) {
            case ChronicleLinkType::IMAGE :
                $chronicleViewModel->setLinkCss("pci-link-image");
                $chronicleViewModel->setLinkText(__("Voir la photo", "s1b"));
                break;
            case ChronicleLinkType::PODCAST :
                $chronicleViewModel->setLinkCss("pci-link-podcast");
                $chronicleViewModel->setLinkText(__("Ecouter le podcast", "s1b"));
                break;
            case ChronicleLinkType::PRESS :
                $chronicleViewModel->setLinkCss("pci-link-press");
                $chronicleViewModel->setLinkText(__("Lire l'article", "s1b"));
                break;
            case ChronicleLinkType::URL :
                $chronicleViewModel->setLinkCss("pci-link-url");
                $chronicleViewModel->setLinkText(__("En savoir plus", "s1b"));
                break;
            case ChronicleLinkType::VIDEO :
                $chronicleViewModel->setLinkCss("pci-link-video");
                $linkText = __("Regarder la video", "s1b");
                break;
        }
        
        // Set type label
        $chronicleViewModel->setTypeLabel(ChronicleHelper::getTypeLabel($this->chronicle->getType_id()));
        
        // Set similar chronicles or same author chronicles
        if ($nbSimilarChronicles || $nbSameAuthorChronicles) {
            
            $chroniclesAdapter = new ChronicleListAdapter();
            
            // Set the similar chronicles
            if ($nbSimilarChronicles) {
                $chroniclesAdapter->setChronicles($this->getSimilarChronicles($nbSimilarChronicles));
                $chronicleViewModel->setSimilarChronicles($chroniclesAdapter->getAsChronicleViewModelList());
            }
            
            // Set the same author chronicles
            if ($nbSameAuthorChronicles) {
                $chroniclesAdapter->setChronicles($this->getSameAuthorChronicles($nbSameAuthorChronicles));
                $chronicleViewModel->setSameAuthorChronicles($chroniclesAdapter->getAsChronicleViewModelList());
            }
        }
        
        return $chronicleViewModel;
    }

    /**
     * Set ChronicleViewModelLight object member with the chronicle
     * @param ChronicleViewModelLight $lightChronicle
     */
    private function setChronicleViewModelLight(ChronicleViewModelLight $lightChronicle) {
        
        // Set title, description and link
        $lightChronicle->setChronicleId($this->chronicle->getId());
        $lightChronicle->setTitle(StringHelper::cleanHTML($this->chronicle->getTitle()));
        $lightChronicle->setLink($this->chronicle->getLink());
        $lightChronicle->setCreationDate($this->chronicle->getCreation_date());
        
        $lightChronicle->setShortenText(StringHelper::cleanHTML(StringHelper::tronque($this->chronicle->getText(), 100)));
        
        $lightChronicle->setNbViews($this->chronicle->getNb_views());
        
        // Set internal detail page link
        $lightChronicle->setDetailLink($this->chronicle->getDetailLink());
        
        // Set Image
        if ($this->chronicle->getBook())
            $lightChronicle->setImage($this->chronicle->getBook()
                ->getLargeImageUrl());
        else if ($this->chronicle->getImage())
            $lightChronicle->setImage($this->chronicle->getImage());
        else if ($this->chronicle->getTag())
            $lightChronicle->setImage(sprintf(BASE_URL . "Resources/images/tags/large/tag_%s.jpg", $this->chronicle->getTag()
                ->getId()));
    }

    /**
     * Get similar chronicles for current chronicle : with same tag or same keywords
     * @param Chronicle $this->chronicle the current chronicle
     * @param number $nbOfSimilarChronicles the number of chronicles to return
     * @return Collection of chronicle
     */
    private function getSimilarChronicles($nbOfSimilarChronicles = 3) {
        
        // Get the chronicles with same tag
        $similarChronicles = array();
        if ($this->chronicle->getTag()) {
            // nb of chronicles requested is + 1 as the current chronicle will be returned in the results
            $chroniclesWithTag = ChronicleSvc::getInstance()->getChroniclesWithTags(array(
                    $this->chronicle->getTag()
                        ->getId()
            ), $nbOfSimilarChronicles + 1);
            $chroniclesWithTag = ChronicleHelper::getDifferentChronicles($this->chronicle, $chroniclesWithTag, $nbOfSimilarChronicles);
            $similarChronicles = $chroniclesWithTag;
        }
        
        // If there's not enough chronicles (or 0) with same tag and if current chronicle has some keywords :
        // we search for chronicles with same keywords
        if ((!$similarChronicles || count($similarChronicles) < $nbOfSimilarChronicles) && $this->chronicle->getKeywords()) {
            
            // nb of chronicles requested is + 1 as the current chronicle will be returned in the results
            $chroniclesWithKeywords = ChronicleSvc::getInstance()->getChroniclesWithKeywords(explode(",", $this->chronicle->getKeywords()), $nbOfSimilarChronicles + 1);
            
            if ($chroniclesWithKeywords) {
                // If no chronicles with same tag, we just add the one we just get with same keywords
                if (!$similarChronicles) {
                    
                    $similarChronicles = $chroniclesWithKeywords;
                    $similarChronicles = ChronicleHelper::getDifferentChronicles($this->chronicle, $similarChronicles, $nbOfSimilarChronicles);
                } else {
                    
                    $filteredChroniclesWithKeywords = array();
                    // Loop all chronicles found with keywords and remove the one already found with same tag
                    foreach ($chroniclesWithKeywords as $chronicleWithKeyword) {
                        
                        $add = true;
                        foreach ($similarChronicles as $similarChronicle) {
                            if ($similarChronicle->getId() == $chronicleWithKeyword->getId()) {
                                $add = false;
                                break;
                            }
                        }
                        if ($add)
                            $filteredChroniclesWithKeywords[] = $chronicleWithKeyword;
                    }
                    $filteredChroniclesWithKeywords = ChronicleHelper::getDifferentChronicles($this->chronicle, $filteredChroniclesWithKeywords, $nbOfSimilarChronicles);
                    
                    // Merge the chronicles found with tag and the one found with keywords
                    $similarChronicles = array_merge($similarChronicles, $filteredChroniclesWithKeywords);
                    $similarChronicles = ChronicleHelper::getDifferentChronicles($this->chronicle, $similarChronicles, $nbOfSimilarChronicles);
                }
            }
        }
        return $similarChronicles;
    }

    private function getSameAuthorChronicles($nbChroniclesToReturn) {
        // Get same author chronicles and add it to model view
        $authorChronicles = ChronicleSvc::getInstance()->getAuthorChronicles($this->chronicle->getUser()
            ->getId());
        if ($authorChronicles)
            $authorChronicles = ChronicleHelper::getDifferentChronicles($this->chronicle, $authorChronicles, $nbChroniclesToReturn);
        
        return $authorChronicles;
    }

}
