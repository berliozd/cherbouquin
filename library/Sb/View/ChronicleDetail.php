<?php

namespace Sb\View;

use Sb\Templates\Template;
use Sb\Db\Model\Chronicle;
use Sb\Adapter\ChronicleAdapter;

/**
 *
 * @author Didier
 */
class ChronicleDetail extends \Sb\View\AbstractView {

    private $chronicle;

    function __construct(Chronicle $chronicle = null) {
        parent::__construct();
        $this->chronicle = $chronicle;
    }

    public function get() {

        $tpl = new Template("chronicleDetail");

        // Get chronicle adaptater
        $chronicleAdpater = new ChronicleAdapter($this->chronicle);

        // Get Social networks bar
        $imageUrlToPin = null;
        $textToPin = $this->chronicle->getTitle();
        if ($this->chronicle->getBook())
            $imageUrlToPin = $this->chronicle->getBook()->getLargeImageUrl();
        $socialNetworksBar = new SocialNetworksBar($imageUrlToPin, $textToPin);

        $tpl->setVariables(array(
            "chronicle" => $chronicleAdpater->getAsChronicleDetailViewModel($this->defImg), "socialNetworksBar" => $socialNetworksBar->get()
        ));

        return $tpl->output();
    }

}
