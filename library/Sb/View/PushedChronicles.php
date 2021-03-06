<?php

namespace Sb\View;

use Sb\Templates\Template;

/**
 *
 * @author Didier
 */
class PushedChronicles extends AbstractView {

    private $chronicles;

    private $title;

    private $typeCSS;

    private $textLink;

    private $link;

    /**
     *
     * @param Ambigous <PushedChronicleViewModel, unknown> $chronicles
     */
    public function setChronicles($chronicles) {

        $this->chronicles = $chronicles;
    }

    /**
     *
     * @param String $title
     */
    public function setTitle($title) {

        $this->title = $title;
    }

    /**
     *
     * @param String $typeCSS
     */
    public function setTypeCSS($typeCSS) {

        $this->typeCSS = $typeCSS;
    }

    /**
     *
     * @param String $textLink
     */
    public function setTextLink($textLink) {

        $this->textLink = $textLink;
    }

    /**
     *
     * @param String $link
     */
    public function setLink($link) {

        $this->link = $link;
    }

    /**
     */
    function __construct($chronicles, $link, $title = null, $typeCSS = null, $textLink = null) {

        $this->chronicles = $chronicles;
        
        $this->title = $title;
        if (!$title)
            $this->title = __("Dernières <strong>chroniques</strong>", "s1b");
        
        $this->typeCSS = $typeCSS;
        if (!$typeCSS)
            $this->typeCSS = "last-chronicles";
        
        $this->link = $link;
        
        $this->textLink = $textLink;
        if (!$textLink)
            $this->textLink = __("Voir d'autres chroniques", "s1b");
    }

    public function get() {

        $tpl = new Template("pushedChronicles");
        $tpl->setVariables(array(
                "title" => $this->title,
                "chronicles" => $this->chronicles,
                "typeCSS" => $this->typeCSS,
                "link" => $this->link,
                "textLink" => $this->textLink
        ));
        
        return $tpl->output();
    }

}
