<?php

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
$languageDir = get_theme_root() . "/" . get_current_theme() . "/languages";
load_theme_textdomain('s1b', $languageDir);

global $noAuthentification;
// La page necessite une authentification
if (!$noAuthentification) {
    // Test si l'utilisateur est connecté ou non
    $isConnected = $s1b->getIsConnected();
    if (!$isConnected) {
        \Sb\Flash\Flash::addItem(__("Vous devez être connecté pour accéder à cette page.", "s1b"));
        \Sb\Helpers\HTTPHelper::redirectToHome();
    }
}