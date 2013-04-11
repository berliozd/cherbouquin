<?php

namespace Sb\Google\Model;

/**
 * Description of GoogleBook
 *
 * @author Didier
 */
class GoogleBook {

    private $isbn10;
    private $isbn13;
    private $asin;
    private $results;
    private $apiKey;
    private $volumeInfo = null;

    public function getVolumeInfo() {
        return $this->volumeInfo;
    }

    function __construct($isbn10, $isbn13, $asin, $apiKey) {
        $this->isbn10 = $isbn10;
        $this->isbn13 = $isbn13;
        $this->asin = $asin;
        $this->apiKey = $apiKey;

        require_once 'GoogleBooks/apiClient.php';
        require_once 'GoogleBooks/contrib/apiBooksService.php';

        // [DB - 20120803] We set the cache class to use (APC)
        // By default, it's file cache but that couldn't work on the VPS server
        global $apiConfig;
        $apiConfig['cacheClass'] = 'apiApcCache';

        $this->init();
    }

    private function init() {

        $client = new \apiClient();
        $client->setApplicationName("Share1Book Test Page");
        $client->setDeveloperKey($this->apiKey);
        $service = new \apiBooksService($client);
        $volumes = $service->volumes;

        $optParams['maxResults'] = 1;
        $optParams['country'] = 'FR';
        $optParams['fields'] = 'kind,totalItems,items(volumeInfo(title,authors,imageLinks,description,publisher,publishedDate,industryIdentifiers))';
        $q = ($this->isbn10 ? $this->isbn10 : ($this->isbn13 ? $this->isbn13 : ($this->asin ? $this->asin : "")));
        \Sb\Trace\Trace::addItem("requetage de google avec $q");
        $this->results = $volumes->listVolumes($q, $optParams);
        
        if (($this->results) && (count($this->results) > 0)) {
            if (array_key_exists('items', $this->results)) {
                if (count($this->results['items']) > 0) {
                    $bookInfo = $this->results['items'][0];
                    $this->volumeInfo = $bookInfo['volumeInfo'];
                }
            }
        }
    }

}