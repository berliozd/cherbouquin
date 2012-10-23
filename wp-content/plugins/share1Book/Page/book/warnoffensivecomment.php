<?php

global $s1b;

$context = $s1b->getContext();
$config = $s1b->getConfig();

if (array_key_exists("bid", $_GET)) {

    $bookId = $_GET["bid"];
    $userId = $context->getConnectedUser()->getId();

    $mailSvc = \Sb\Mail\Service\MailSvcImpl::getNewInstance(null, $context->getConnectedUser()->getEmail());
    $body = "Un commentaire injurieux a été signalé pour le livre $bookId par l'utilisateur $userId";

    if ($mailSvc->send(\Sb\Entity\Constants::WEBMASTER_EMAIL, "signalisation de commentaire injurieux", $body))
        \Sb\Flash\Flash::addItem(__("Le commentaire injurieux a été signalé à l'administrateur du site.", "s1b"));
}
\Sb\Helpers\HTTPHelper::redirectToUrl($_SERVER["HTTP_REFERER"]);