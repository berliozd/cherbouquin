<?php

$user = $context->getConnectedUser();
$userSettings = $user->getSetting();
if ($_POST) {

    
    $user->setDeleted(true);
    Sb\Db\Dao\UserDao::getInstance()->update($user);
//
//    $_SESSION = array();
//    if (ini_get("session.use_cookies")) {
//        $params = session_get_cookie_params();
//        setcookie(session_name(), '', time() - 3600, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
//        );
//    }
    session_destroy();
    \Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::LOGIN);
}