<?php
namespace Sb\Adaptater;

use Sb\Db\Model\GroupChronicle;
use Sb\Model\PushedChronicleViewModel;
use Sb\Helpers\StringHelper;

/** 
 * @author Didier
 * 
 */
class GroupChronicleListAdaptater {

    private $groupChronicles;

    /**
     * @return Collection of GroupChronicle $groupChronicles
     */
    public function getGroupChronicles() {
        return $this->groupChronicles;
    }

    /**
     * @param Collection of GroupChronicle $groupChronicles
     */
    public function setGroupChronicles($groupChronicles) {
        $this->groupChronicles = $groupChronicles;
    }

    /**
     * 
     */
    function __construct() {
    }

    public function getAsPushedChronicleViewModelList() {

        $pushedChronicles = array();

        foreach ($this->groupChronicles as $groupChronicle) {
            /* @var $groupChronicle GroupChronicle */
            $pushedChronicle = new PushedChronicleViewModel();

            // Set title, description and link
            $pushedChronicle->setGroupChronicleId($groupChronicle->getId());
            $pushedChronicle->setTitle(StringHelper::cleanHTML($groupChronicle->getTitle()));
            $pushedChronicle->setDescription(StringHelper::cleanHTML(StringHelper::tronque($groupChronicle->getText(), 100)));
            $pushedChronicle->setLink($groupChronicle->getLink());

            //Set Image
            if ($groupChronicle->getBook())
                $pushedChronicle->setImage($groupChronicle->getBook()->getLargeImageUrl());
            else if ($groupChronicle->getImage())
                $pushedChronicle->setImage($groupChronicle->getImage());
            else if ($groupChronicle->getTag())
                $pushedChronicle->setImage(sprintf("/images/tags/large/tag_%s.jpg", $groupChronicle->getTag()->getId()));

            $pushedChronicles[] = $pushedChronicle;
        }

        return $pushedChronicles;
    }
}
