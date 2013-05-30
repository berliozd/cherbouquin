<?php

namespace Sb\Service;

/**
 * Description of MailSvc
 * @author Didier
 */
class MailSvc extends Service {

    private $replyto;

    private $from;

    private static $instance;

    /**
     *
     * @return \Sb\Service\MailSvc
     */
    public static function getInstance() {

        if (!self::$instance)
            self::$instance = new MailSvc();
        return self::$instance;
    }

    public static function getNewInstance($replyTo = null, $from = null) {

        return new MailSvc($replyTo, $from);
    }

    protected function __construct($replyTo = null, $from = null) {

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
        return mail($to, utf8_decode($subject), $body, $entete);
    }

    private function getHeader() {

        $entete = "MIME-Version: 1.0\n";
        $entete .= "Content-type: text/html; Charset=UTF-8\n";
        $entete .= 'From: ' . $this->from . "\n" . 'Reply-To: ' . $this->replyto . "\n" . 'X-Mailer: PHP/' . phpversion();
        return $entete;
    }

}