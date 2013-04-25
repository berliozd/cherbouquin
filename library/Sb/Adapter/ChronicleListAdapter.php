<?php
namespace Sb\Adapter;

/** 
 * @author Didier
 * 
 */
class ChronicleListAdapter {

    private $chronicles;

    /**
     * @return Collection of Chronicle $chronicles
     */
    public function getChronicles() {
        return $this->chronicles;
    }

    /**
     * @param Collection of Chronicle $chronicles
     */
    public function setChronicles($chronicles) {
        $this->chronicles = $chronicles;
    }

    /**
     * 
     */
    function __construct() {
    }

    public function getAsPushedChronicleViewModelList() {

        $pushedChronicles = array();

        foreach ($this->chronicles as $chronicle) {

            // Get Chronicle Adapter
            $adapter = new ChronicleAdapter($chronicle);

            // Add PushedChronicle to array
            $pushedChronicles[] = $adapter->getAsPushedChronicleViewModel();

        }

        return $pushedChronicles;
    }
}
