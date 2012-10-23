<?php

use Sb\Config\Model;

namespace Sb\Templates;

/**
 * Description of Template
 *
 * @author Didier
 */
class Template {

    protected $file;
    protected $values = array();
    private $cache;
    private $cacheKey;
    private $cacheEnabled = true;
    private $variables;

    /**
     *
     * @return Config
     */
    private function getConfig() {
        global $s1b;
        return $s1b->getConfig();
    }

    /**
     *
     * @return \Sb\Context\Model\Context
     */
    private function getContext() {
        global $s1b;
        return $s1b->getContext();
    }

    public function __construct($file) {
        $file .= ".phtml";
        $this->cacheKey = str_replace(".", "_", str_replace("/", "_", $file));
        $this->file = $this->getContext()->getBaseDirectory() . "Templates/" . $file;
        $this->cache = \Sb\Cache\ZendFileCache::getInstance();
        $this->cacheEnabled = $this->getConfig()->getCacheTemplatingEnabled();
    }

    public function set($key, $value) {
        $this->values[$key] = $value;
    }

    /**
     * Set an array of variables.
     * Ex : with array("addMode" => true), $addMode will be available in Template
     * @param type $variables
     */
    public function setVariables($variables) {
        $this->variables = $variables;
    }

    public function output() {

        if ($this->cacheEnabled) {
            // tentative de récupération du template en cache
            $output = $this->cache->load($this->cacheKey);

            // le template n'est pas disponible en cache
            if (!$output) {
                //\Sb\Trace\Trace::addItem($this->file . " NOTIN en cache");
                if (!file_exists($this->file)) {
                    return "Error loading template file ($this->file).<br />";
                }

                $output = $this->_getOuput();
                $this->cache->save($output, $this->cacheKey);
            } else {
                //\Sb\Trace\Trace::addItem($this->file . " IN cache");
            }
        } else {
            if (!file_exists($this->file)) {
                return "Error loading template file ($this->file).<br />";
            }
            $output = $this->_getOuput();
        }


        foreach ($this->values as $key => $value) {
            $tagToReplace = "[@$key]";
            $output = str_replace($tagToReplace, $value, $output);
        }

        return $output;
    }

    static public function merge($templates, $separator = "") {
        $output = "";

        foreach ($templates as $template) {
            $content = (get_class($template) !== "Sb\Templates\Template") ? "Error, incorrect type - expected Template." : $template->output();
            $output .= $content . $separator;
        }

        return $output;
    }

    private function _getOuput() {
        ob_start();

        // Extract variables to make them accessible in the template
        if (is_array($this->variables))
            extract($this->variables);

        include $this->file;

        $output = ob_get_contents();

        ob_end_clean();

        return $output;
    }

}