<?php

/**
 * Template Name: user_logout
 */
if ($_COOKIES && array_key_exists("PHPSESSID", $_COOKIES)) {
    unset($_COOKIES["PHPSESSID"]);
}

// destruction du cookie de connexion PHPSESSID 3600 correspond à 60 min
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
    );
}

$tmpLang = $_SESSION['WPLANG'];
session_destroy();

$noAuthentification = true;
require_once ("includes/init.php");

$_SESSION['WPLANG'] = $tmpLang;

$facebookSvc = new \Sb\Facebook\Service\FacebookSvc($config->getFacebookApiId(), $config->getFacebookSecret(), \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_HOME), \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::LOGIN), \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::LOGIN));
$faceBookUser = $facebookSvc->getUser();
$facebookSvc->cleanUser();
if ($faceBookUser) {
    wp_redirect($facebookSvc->getFacebookLogOutUrl());
}

\Sb\Flash\Flash::addItem(__("Déconnexion réussie", "s1b"));


// Redirecting to login page
\Sb\Helpers\HTTPHelper::redirect("");