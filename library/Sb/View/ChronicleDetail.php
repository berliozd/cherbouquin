<?php

namespace Sb\View;

use Sb\Templates\Template;
use Sb\Model\ChronicleViewModel;
use Sb\View\SocialNetworksBar;

/**
 *
 * @author Didier
 */
class ChronicleDetail extends AbstractView {

    private $chronicleViewModel;

    function __construct(ChronicleViewModel $chronicleViewModel = null) {

        parent::__construct();
        $this->chronicleViewModel = $chronicleViewModel;
    
    }

    public function get() {

        $tpl = new Template("chronicleDetail");
        
        // Get Social networks bar
        $imageUrlToPin = null;
        $textToPin = $this->chronicleViewModel->getTitle();
        if ($this->chronicleViewModel->getChronicleHasBook())
            $imageUrlToPin = $this->chronicleViewModel->getBook()->getLargeImageUrl();
        $socialNetworksBar = new SocialNetworksBar($imageUrlToPin, $textToPin);
        
        $tpl->setVariables(array (
                "chronicle" => $this->chronicleViewModel,
                "socialNetworksBar" => $socialNetworksBar->get() 
        ));
        
        return $tpl->output();
    
    }

}
