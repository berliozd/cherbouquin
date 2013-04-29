<?php
namespace Sb\Adapter;

use Sb\Db\Model\Chronicle;
use Sb\Model\PushedChronicleViewModel;
use Sb\Helpers\StringHelper;
use Sb\Helpers\HTTPHelper;
use Sb\Model\ChronicleDetailViewModel;
use Sb\Helpers\UserHelper;
use Sb\Helpers\BookHelper;
use Sb\Entity\ChronicleLinkType;
use Sb\Entity\Urls;
use Sb\Helpers\ChronicleHelper;
use Doctrine\Common\Util\Debug;

/** 
 * @author Didier
 * 
 */
class ChronicleAdapter {

    /* @var $Chronicle Chronicle */
    private $chronicle;

    /**
     * 
     */
    function __construct(Chronicle $chronicle) {
        $this->chronicle = $chronicle;
    }

    /**
     * Get a chronicle as a PushedChronicleViewModel object for home page
     * @return \Sb\Model\PushedChronicleViewModel a chronicle as a PushedChronicleViewModel object 
     */
    public function getAsPushedChronicleViewModel() {

        $pushedChronicle = new PushedChronicleViewModel();

        // Set title, description and link
        $pushedChronicle->setChronicleId($this->chronicle->getId());
        $pushedChronicle->setTitle(StringHelper::cleanHTML($this->chronicle->getTitle()));
        $pushedChronicle->setDescription(StringHelper::cleanHTML(StringHelper::tronque($this->chronicle->getText(), 100)));
        $pushedChronicle->setLink($this->chronicle->getLink());
        $pushedChronicle->setCreationDate($this->chronicle->getCreation_date());
        $pushedChronicle->setNbViews($this->chronicle->getNb_views());

        // Set internal detail page link
        if ($this->chronicle->getTitle())
            $pushedChronicle->setDetailLink("/chronique/" . HTTPHelper::encodeTextForURL(StringHelper::cleanHTML($this->chronicle->getTitle())) . "-" . $this->chronicle->getId());
        else
            $pushedChronicle->setDetailLink("/chronique/chronique-" . $this->chronicle->getId());

        // Set Image
        if ($this->chronicle->getBook())
            $pushedChronicle->setImage($this->chronicle->getBook()->getLargeImageUrl());
        else if ($this->chronicle->getImage())
            $pushedChronicle->setImage($this->chronicle->getImage());
        else if ($this->chronicle->getTag())
            $pushedChronicle->setImage(sprintf("/public/Resources/images/tags/large/tag_%s.jpg", $this->chronicle->getTag()->getId()));

        return $pushedChronicle;

    }

    /**
     * Get a chronicle as ChronicleDetailViewModel object for chronicle detail page
     * @param String $defImg default image for book used when no image available for a book 
     * @return \Sb\Model\ChronicleDetailViewModel a chronicle as a ChronicleDetailViewModel object
     */
    public function getAsChronicleDetailViewModel($defImg) {

        /* @var $chronicle Chronicle */
        $chronicle = new ChronicleDetailViewModel();

        $chronicle->setUserName($this->chronicle->getUser()->getUserName());
        $chronicle->setUserProfileLink(HTTPHelper::Link(Urls::USER_PROFILE, array(
            "uid" => $this->chronicle->getUser()->getId()
        )));
        $chronicle->setUserImage(UserHelper::getMediumImageTag($this->chronicle->getUser()));
        $chronicle->setTitle($this->chronicle->getTitle());
        $chronicle->setText($this->chronicle->getText());
        $chronicle->setSource($this->chronicle->getSource());
        $chronicle->setCreationDate($this->chronicle->getCreation_date()->format(__("d/m/Y", "s1b")));

        // Set book specific info
        $chronicle->setChronicleHasBook(false);
        $chronicle->setBookImage("");
        $chronicle->setBookTitle("");
        $chronicle->setBookAuthors("");
        $chronicle->setBookLink("");
        if ($this->chronicle->getBook()) {
            $chronicle->setBookImage(BookHelper::getMediumImageTag($this->chronicle->getBook(), $defImg));
            $chronicle->setBookTitle($this->chronicle->getBook()->getTitle());
            $chronicle->setBookAuthors($this->chronicle->getBook()->getOrderableContributors());
            $chronicle->setBookLink(HTTPHelper::Link($this->chronicle->getBook()->getLink()));
            $chronicle->setChronicleHasBook(true);
        }

        // Set link info
        $chronicle->setLink($this->chronicle->getLink());
        $chronicle->setLinkCss("pci-link-other");
        $chronicle->setLinkText(__("En savoir plus", "s1b"));
        switch ($this->chronicle->getLink_type()) {
        case ChronicleLinkType::IMAGE:
            $chronicle->setLinkCss("pci-link-image");
            $chronicle->setLinkText(__("Voir la photo", "s1b"));
            break;
        case ChronicleLinkType::PODCAST:
            $chronicle->setLinkCss("pci-link-podcast");
            $chronicle->setLinkText(__("Ecouter le podcast", "s1b"));
            break;
        case ChronicleLinkType::PRESS:
            $chronicle->setLinkCss("pci-link-press");
            $chronicle->setLinkText(__("Lire l'article", "s1b"));
            break;
        case ChronicleLinkType::URL:
            $chronicle->setLinkCss("pci-link-url");
            $chronicle->setLinkText(__("En savoir plus", "s1b"));
            break;
        case ChronicleLinkType::VIDEO:
            $chronicle->setLinkCss("pci-link-video");
            $linkText = __("Regarder la video", "s1b");
            break;
        }

        // Set type label
        $chronicle->setTypeLabel(ChronicleHelper::getTypeLabel($this->chronicle->getType_id()));

        return $chronicle;
    }

}
