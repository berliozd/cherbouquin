<?php

use Sb\Config\Model;

namespace Sb\Trace;

/**
 * Class managing flash messages.
 * <p>
 * (Flash messages are positive messages that are displayed exactly once,
 * on the next page; typically after form submitting.)
 */
class Trace {

    const TRACES_KEY = '_traces';

    private static $items = null;

    /**
     *
     * @return Config
     */
    private static function getConfig() {
        global $globalConfig;
        return $globalConfig;
    }

    /**
     *
     * @return \Sb\Context\Model\Context
     */
    private static function getContext() {
        global $globalContext;
        return $globalContext;
    }

    private function __construct() {

    }

    public static function hasItems() {
        self::initItems();
        return count(self::$items) > 0;
    }

    public static function addItem($message, $error = false) {
        global $s1b;

        if (self::getConfig()->getTracesEnabled() || self::getConfig()->getLogsEnabled()) {
            if (!strlen(trim($message))) {
                throw new \Exception('Cannot insert empty trace.');
            }

            // Ecriture du message dans le tableau des items en sessions si traces activées
            if (self::getConfig()->getTracesEnabled()) {
                self::initItems();
                self::$items[] = $message;
            }

            // Ecriture du log si activé
            if (self::getConfig()->getLogsEnabled()) {

                // vérification de l'existence du fichier et création si non existant
                $now = new \DateTime();
                $logFilePath = self::getContext()->getBaseDirectory() . sprintf("/var/log/%s-log.txt", $now->format("Ymd"));
                if (!file_exists($logFilePath)) {
                    $file = fopen($logFilePath, "w");
                    fclose($file);
                }

                // ecriture du log dans le fichier
                $writer = new \Zend_Log_Writer_Stream($logFilePath);
                $logger = new \Zend_Log($writer);
                if ($error) {
                    $logger->err($message);
                } else {
                    $logger->info($message);
                }
                $logger = null;
            }
        }
    }

    /**
     * Get flash messages and clear them.
     * @return array flash messages
     */
    public static function getItems() {
        if (self::getConfig()->getTracesEnabled()) {
            self::initItems();
            $copy = self::$items;
            self::$items = array();
            return $copy;
        }
    }

    private static function initItems() {
        if (self::getConfig()->getTracesEnabled()) {
            if (self::$items !== null) {
                return;
            }

            if (!array_key_exists(self::TRACES_KEY, $_SESSION)) {
                $_SESSION[self::TRACES_KEY] = array();
            }
            self::$items = &$_SESSION[self::TRACES_KEY];
        }
    }

}