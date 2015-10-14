<?php

namespace Sb\Config\Model;

/**
 * Description of Config
 *
 * @author Didier
 */
Class Config {


    private $tracesEnabled;
    private $logsEnabled;
    private $cacheTemplatingEnabled;
    private $amazonApiKey;
    private $amazonSecretKey;
    private $amazonAssociateTag;
    private $googleApiKey;
    private $searchNbResultsToShow;
    private $searchNbResultsPerPage;
    private $listNbBooksPerPage;
    private $amazonNumberOfPageRequested;
    private $maxImportNb;
    private $facebookApiId;
    private $facebookSecret;
    private $maximumNbUserBooksForPublic;
    private $isProduction;
    private $databaseParams;
    private $apcCacheNamespace;


    /**
     * Config constructor.
     */
    public function __construct() {
        $config = new \Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);

        $this->databaseParams = $config->database->params;
        $this->tracesEnabled = $config->debug->traces->enabled;
        $this->logsEnabled = $config->debug->logs->enabled;
        $this->cacheTemplatingEnabled = $config->performance->cache_templating->enabled;
        $this->amazonApiKey = $config->amazon->api_key;
        $this->amazonSecretKey = $config->amazon->secret_key;
        $this->amazonAssociateTag = $config->amazon->associate_tag;
        $this->googleApiKey = $config->google->api_key;
        $this->searchNbResultsToShow = $config->search->minimum_nb_of_books_displayed;
        $this->searchNbResultsPerPage = $config->search->nb_results_per_page;
        $this->listNbBooksPerPage = $config->list->nb_books_per_page;
        $this->amazonNumberOfPageRequested = $config->amazon->nb_of_page_requested;
        $this->maxImportNb = $config->import->max;
        $this->facebookApiId = $config->facebook->api_id;
        $this->facebookSecret = $config->facebook->secret;
        $this->maximumNbUserBooksForPublic = $config->general->max_nb_books_for_users;
        $this->isProduction = (APPLICATION_ENV == "production" ? true : false);
        $this->apcCacheNamespace = $config->apc->cache->namespace;
    }

    public function getTracesEnabled() {
        return $this->tracesEnabled;
    }

    public function getLogsEnabled() {
        return $this->logsEnabled;
    }

    public function getCacheTemplatingEnabled() {
        return $this->cacheTemplatingEnabled;
    }

    public function getAmazonApiKey() {
        return $this->amazonApiKey;
    }

    public function getAmazonSecretKey() {
        return $this->amazonSecretKey;
    }

    public function getAmazonAssociateTag() {
        return $this->amazonAssociateTag;
    }

    public function getGoogleApiKey() {
        return $this->googleApiKey;
    }

    public function getSearchNbResultsToShow() {
        return $this->searchNbResultsToShow;
    }

    public function getSearchNbResultsPerPage() {
        return $this->searchNbResultsPerPage;
    }

    public function getListNbBooksPerPage() {
        return $this->listNbBooksPerPage;
    }

    public function getAmazonNumberOfPageRequested() {
        return $this->amazonNumberOfPageRequested;
    }

    public function getMaxImportNb() {
        return $this->maxImportNb;
    }

    public function getFacebookApiId() {
        return $this->facebookApiId;
    }

    public function getFacebookSecret() {
        return $this->facebookSecret;
    }

    public function getMaximumNbUserBooksForPublic() {
        return $this->maximumNbUserBooksForPublic;
    }

    public function getIsProduction() {
        return $this->isProduction;
    }

    /**
     * @return mixed
     */
    public function getDatabaseParams() {
        return $this->databaseParams;
    }

    /**
     * @return mixed
     */
    public function getApcCacheNamespace() {
        return $this->apcCacheNamespace;
    }


}