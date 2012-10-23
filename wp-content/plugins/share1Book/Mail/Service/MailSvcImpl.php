<?php

namespace Sb\Mail\Service;

/**
 * Description of \Sb\Mail\Service\MailSvcImpl
 *
 * @author Didier
 */
class MailSvcImpl implements \Sb\Mail\Service\MailSvc {

    private $replyto;
    private $from;
    private static $instance;

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
     * @return \Sb\Mail\Service\MailSvcImpl
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Mail\Service\MailSvcImpl();
        return self::$instance;
    }

    public static function getNewInstance($replyTo = null, $from = null) {
        return new \Sb\Mail\Service\MailSvcImpl($replyTo, $from);
    }

    private function __construct($replyTo = null, $from = null) {
        $config = $this->getConfig();

        if ($replyTo)
            $this->replyto = $replyTo;
        else
            $this->replyto = \Sb\Entity\Constants::MAIL_REPLY_TO;

        if ($from)
            $this->from = $from;
        else
            $this->from = \Sb\Entity\Constants::MAIL_FROM;
    }

    /**
     *
     * @param type $to
     * @param type $subject
     * @param type $body
     * @return boolean true is mail was acceptable for delivery
     */
    public function send($to, $subject, $body) {
        $entete = $this->getHeader();
        \Sb\Trace\Trace::addItem("sending mail to  : " . $to);
        return mail($to, utf8_decode($subject), $body, $entete);
    }

    private function getHeader() {
        $entete = "MIME-Version: 1.0\n";
        $entete .= "Content-type: text/html; Charset=UTF-8\n";
        $entete .= 'From: ' . $this->from . "\n" .
                'Reply-To: ' . $this->replyto . "\n" .
                'X-Mailer: PHP/' . phpversion();
        \Sb\Trace\Trace::addItem("sending mail from  : " . $this->from);
        \Sb\Trace\Trace::addItem("sending mail reply to  : " . $this->replyto);
        return $entete;
    }

}