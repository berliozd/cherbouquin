<?php

require_once 'includes/init.php';
get_header();
$user = $context->getConnectedUser();
$userSettings = $user->getSetting();

/**
 * Template Name: user_profile
 */
?>
<?php
$userNavigation = new \Sb\View\Components\UserNavigation;
echo $userNavigation->get();
?>
<?php showFlashes(); ?>
<div id="content-wrap" class="user-profile-bkg">
    <div id="content-center">
        <?php
        $profileView =new \Sb\View\UserProfile($user, $userSettings, true, true, false);
        echo $profileView->get();
        ?>
    </div>
    <div id="content-right">
        <?php
        $userToolBox = new \Sb\View\Components\UserToolBox;
        echo $userToolBox->get();
        ?>
    </div>
<?php get_footer(); ?>