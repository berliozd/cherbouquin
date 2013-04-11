<?php

namespace Sb\Trace;

/**
 * Description of FireBugTrace
 *
 * @author Didier
 */
class FireBugTrace {

    private static $writer = null;
    private static $logger = null;
    private static $request = null;
    private static $response = null;
    private static $channel = null;

    public static function getWriter() {
        if (!self::$writer)
            self::$writer = new \Zend_Log_Writer_Firebug();
        return self::$writer;
    }

    public static function getLogger() {
        if (!self::$logger)
            self::$logger = new \Zend_Log(self::getWriter());
        return self::$logger;
    }

    public static function getRequest() {
        if (!self::$request)
            self::$request = new \Zend_Controller_Request_Http();
        return self::$request;
    }

    public static function getResponse() {
        if (!self::$response)
            self::$response = new \Zend_Controller_Response_Http();
        return self::$response;
    }

    public static function getChannel() {
        if (!self::$channel)
            self::$channel = \Zend_Wildfire_Channel_HttpHeaders::getInstance();
        return self::$channel;
    }

    public static function Trace($message, $level = null) {

        $writer = self::getWriter();
        $logger = self::getLogger();
        $request = self::getRequest();
        $response = self::getResponse();
        $channel = self::getChannel();

        $channel->setRequest($request);
        $channel->setResponse($response);

        // Start output buffering
        ob_start();
        
        if (!$level)
            $level = \Zend_log::INFO;
        
        // Now you can make calls to the logger        
        $logger->log($message, $level);

        // Flush log data to browser
        $channel->flush();
        $response->sendHeaders();
    }

}