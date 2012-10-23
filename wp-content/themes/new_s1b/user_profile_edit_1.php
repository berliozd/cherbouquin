<?php

$user = $context->getConnectedUser();
$userSettings = $user->getSetting();

$FirstName_modif = trim($_POST['FirstName_modif']);
$LastName_modif = trim($_POST['LastName_modif']);
$UserName_modif = trim($_POST['UserName_modif']);
//$Email_modif = trim($_POST['Email_modif']);
$Gender_modif = trim($_POST['Gender_modif']);
$BirthDay_modif = trim($_POST['BirthDay_pre_modif']);
$Address_modif = trim($_POST['Address_modif']);
$City_modif = trim($_POST['City_modif']);
$ZipCode_modif = trim($_POST['ZipCode_modif']);
$Country_modif = trim($_POST['Country_modif']);
$Language_modif = trim($_POST['Language_modif']);

// on vérifie que tous les champs soient complétés
if (!empty($_POST) && strlen($LastName_modif) > 3 && strlen($FirstName_modif) > 1 && strlen($UserName_modif) > 1) {
//    //On verifie s'il n'y a pas deja un utilisateur inscrit avec l'email choisit
//    if (trim($Email_modif) != $user->getEmail()) {
//        $existingUserWithEmail = \Sb\Db\Dao\UserDao::getInstance()->getByEmail($Email_modif);
//        if ($existingUserWithEmail) {
//            \Sb\Flash\Flash::addItem(__("Un membre utilise déjà l'email que vous avez entré, merci d'en choisir un autre", "s1b"));
//            $emailExist = true;
//        }
//    }

    if ($UserName_modif != $user->getUserName()) {
        $existingUserWithUserName = \Sb\Db\Dao\UserDao::getInstance()->getByUserName($UserName_modif);
        if ($existingUserWithUserName) {
            \Sb\Flash\Flash::addItem(__("Un membre utilise déjà l'identifiant que vous avez entré, merci d'en choisir un autre",
                            "s1b"));
            $userNameExist = true;
        }
    }
    if (!($userNameExist)) {
        $user->setFirstName($FirstName_modif);
        $user->setLastName($LastName_modif);
        $user->setUserName($UserName_modif);
        //$user->setEmail($Email_modif);
        $user->setGender($Gender_modif);
        $user->setBirthDay(\Sb\Helpers\DateHelper::createDateBis($BirthDay_modif));
        $user->setAddress($Address_modif);
        $user->setCity($City_modif);
        $user->setZipCode($ZipCode_modif);
        $user->setCountry($Country_modif);
        $user->setLanguage($Language_modif);
        \Sb\Db\Dao\UserDao::getInstance()->update($user);
        \Sb\Flash\Flash::addItem(__("Vos modifications ont bien été enregistrées", "s1b"));
    }
} else {

    if (!empty($_POST) && strlen($LastName_modif) < 3) {
        \Sb\Flash\Flash::addItem(__("votre nom doit comprendre au moins 3 caractères", "s1b"));
    }

    if (!empty($_POST) && strlen($FirstName_modif) < 1) {
        \Sb\Flash\Flash::addItem(__("merci d'indiquer votre prénom", "s1b"));
    }

    if (!empty($_POST) && strlen($UserName_modif) < 1) {
        \Sb\Flash\Flash::addItem(__("merci d'indiquer un identifiant", "s1b"));
    }
}

//\Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::USER_PROFILE_EDIT);