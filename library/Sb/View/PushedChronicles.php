<?php
namespace Sb\View;

use Sb\Templates\Template;
/** 
 * @author Didier
 * 
 */
class PushedChronicles extends AbstractView {

    private $chronicles;
    private $title;
    private $typeCSS;

    /**
     * @return PushedChronicleViewModel[] $chronicles
     */
    public function getChronicles() {
        return $this->chronicles;
    }

    /**
     * @param PushedChronicleViewModel[] $chronicles
     */
    public function setChronicles($title, $chronicles) {
        $this->chronicles = $chronicles;
    }

    /**
     * @return String $title
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param String $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return String $typeCSS
     */
    public function getTypeCSS() {
        return $this->typeCSS;
    }

    /**
     * @param String $typeCSS
     */
    public function setTypeCSS($typeCSS) {
        $this->typeCSS = $typeCSS;
    }

    /**
     * 
     */
    function __construct($title, $chronicles, $typeCSS) {
        $this->chronicles = $chronicles;
        $this->title = $title;
        $this->typeCSS = $typeCSS;
    }

    public function get() {

        $tpl = new Template("pushedChronicles");
        $tpl->setVariables(array("title" => $this->getTitle(), "chronicles" => $this->getChronicles(), "typeCSS" => $this->getTypeCSS()));

        return $tpl->output();

    }
}
