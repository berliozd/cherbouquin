<?php

use \Sb\Authentification\Service\AuthentificationSvc;

// Démarrage du plugin Share1Book
global $s1b;

$s1b->prepare();

$config = $s1b->getConfig();
$context = $s1b->getContext();
$mailSvc = $s1b->getMailSvc();


// Démarrage de la session si besoin
$session_id = session_id();
if (empty($session_id))
    session_start();

// Activation des langues
$languageDir = BASE_PATH . "languages";
load_theme_textdomain('s1b', $languageDir);

global $noAuthentification;
// La page necessite une authentification
if (!$noAuthentification) {
    AuthentificationSvc::getInstance()->checkUserIsConnected();
}