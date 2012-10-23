<?php

namespace Sb\Mail\Service;

/**
 * Description of MailSvc
 *
 * @author Didier
 */
interface MailSvc {

    public function send($to, $subject, $body);
}