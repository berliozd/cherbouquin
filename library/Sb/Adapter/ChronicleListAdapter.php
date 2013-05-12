<?php

namespace Sb\Adapter;

/**
 *
 * @author Didier
 */
class ChronicleListAdapter {

    private $chronicles;

    /**
     *
     * @return Collection of Chronicle $chronicles
     */
    public function getChronicles() {

        return $this->chronicles;
    }

    /**
     *
     * @param Collection of Chronicle $chronicles
     */
    public function setChronicles($chronicles) {

        $this->chronicles = $chronicles;
    }

    /**
     * Get an array of Chronicle as an array of ChronicleViewModelLight
     * @return multitype:\Sb\Model\ChronicleViewModelLight
     */
    public function getAsChronicleViewModelLightList() {

        $chronicleViewModelLightList = array();
        
        foreach ($this->chronicles as $chronicle) {
            
            // Get Chronicle Adapter
            $adapter = new ChronicleAdapter($chronicle);
            
            // Add ChronicleViewModelLight to array
            $chronicleViewModelLightList[] = $adapter->getAsChronicleViewModelLight();
        }
        
        return $chronicleViewModelLightList;
    }

    /**
     * Get an array of Chronicle as an array of ChronicleViewModel
     * @param string $nbOfSimilarChronicles number of similar chronicles to return
     * @param string $nbOfSameAuthorChronicles number of same author chronicles to return
     * @return array of ChronicleViewModel
     */
    public function getAsChronicleViewModelList($nbOfSimilarChronicles = null, $nbOfSameAuthorChronicles = null) {

        $detailChronicles = array();
        
        foreach ($this->chronicles as $chronicle) {
            
            // Get Chronicle Adapter
            $adapter = new ChronicleAdapter($chronicle);
            
            // Add ChronicleViewModel to array
            $detailChronicles[] = $adapter->getAsChronicleViewModel($nbOfSimilarChronicles, $nbOfSameAuthorChronicles);
        }
        
        return $detailChronicles;
    }

}
