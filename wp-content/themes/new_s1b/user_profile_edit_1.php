<?php

use Sb\Helpers\ArrayHelper;


$user = $context->getConnectedUser();
$userSettings = $user->getSetting();

$FirstName_modif = trim(ArrayHelper::getSafeFromArray($_POST, "FirstName_modif", null));
$LastName_modif = trim(ArrayHelper::getSafeFromArray($_POST, "LastName_modif", null));
$UserName_modif = trim(ArrayHelper::getSafeFromArray($_POST, "UserName_modif", null));
$Gender_modif = trim(ArrayHelper::getSafeFromArray($_POST, "Gender_modif", null));
$BirthDay_modif = trim(ArrayHelper::getSafeFromArray($_POST, "BirthDay_pre_modif", null));
$Address_modif = trim(ArrayHelper::getSafeFromArray($_POST, "Address_modif", null));
$City_modif = trim(ArrayHelper::getSafeFromArray($_POST, "City_modif", null));
$ZipCode_modif = trim(ArrayHelper::getSafeFromArray($_POST, "ZipCode_modif", null));
$Country_modif = trim(ArrayHelper::getSafeFromArray($_POST, "Country_modif", null));
$Language_modif = trim(ArrayHelper::getSafeFromArray($_POST, "Language_modif", null));

$lang = Sb\Helpers\ArrayHelper::getSafeFromArray($_SESSION, "WPLANG", "fr_FR");

// on vérifie que tous les champs soient complétés
if (!empty($_POST) && strlen($LastName_modif) > 3 && strlen($FirstName_modif) > 1 && strlen($UserName_modif) > 1) {

    $userNameExist = false;
    if ($UserName_modif != $user->getUserName()) {
        $existingUserWithUserName = \Sb\Db\Dao\UserDao::getInstance()->getByUserName($UserName_modif);
        if ($existingUserWithUserName) {
            \Sb\Flash\Flash::addItem(__("Un membre utilise déjà l'identifiant que vous avez entré, merci d'en choisir un autre",
                            "s1b"));
            $userNameExist = true;
        }
    }
    if (!$userNameExist) {
        $user->setFirstName($FirstName_modif);
        $user->setLastName($LastName_modif);
        $user->setUserName($UserName_modif);
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