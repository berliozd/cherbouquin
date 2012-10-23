<?php

$user = $context->getConnectedUser();
$userSettings = $user->getSetting();


if (!empty($_POST)) {

    $settings_DisplayProfile = $_POST['settings_DisplayProfile'];
    $settings_DisplayEmail = $_POST['settings_DisplayEmail'];
    $settings_SendMessages = $_POST['settings_SendMessages'];
    $settings_DisplayBirthDay = $_POST['settings_DisplayBirthDay'];
    $settings_AllowFollowers = $_POST['settings_AllowFollowers'];
    $settings_EmailMe = $_POST['settings_EmailMe'];
    $settings_AcceptNewsletter = ($_POST['settings_AcceptNewsletter'] == 1 ? true : false);

    $userSettings->setDisplayProfile($settings_DisplayProfile);
    $userSettings->setDisplayEmail($settings_DisplayEmail);
    $userSettings->setSendMessages($settings_SendMessages);
    $userSettings->setDisplayBirthday($settings_DisplayBirthDay);
    $userSettings->setAllowFollowers($settings_AllowFollowers);
    $userSettings->setEmailMe($settings_EmailMe);
    $userSettings->setAccept_newsletter($settings_AcceptNewsletter);

    \Sb\Db\Dao\UserSettingDao::getInstance()->update($userSettings);

    \Sb\Flash\Flash::addItem(__("Vos modifications ont bien été enregistrées", "s1b"));
}