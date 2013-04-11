<?php

namespace Sb\Helpers;

/**
 * Description of LocalizationHelper
 *
 * @author Didier
 */
class LocalizationHelper {

    private static $translate;

    /**
     * 
     * @return a Zend_Translate object used in new pages built on Zend
     */
    public static function getZendTranslate() {
        if (!self::$translate) {
            self::$translate = new \Zend_Translate(array('adapter' => 'gettext', 'content' => BASE_PATH . '/languages/fr_FR.mo', 'locale' => 'fr_FR'));
            switch (WPLANG) {
                case "fr_FR" :
                    self::$translate = new \Zend_Translate(array('adapter' => 'gettext', 'content' => BASE_PATH . '/languages/fr_FR.mo', 'locale' => 'fr_FR'));
                    break;
                case "en_US" :
                    self::$translate = new \Zend_Translate(array('adapter' => 'gettext', 'content' => BASE_PATH . '/languages/en_US.mo', 'locale' => 'en_US'));
                    break;
            }
        }
        return self::$translate;
    }

}