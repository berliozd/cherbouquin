<?php

require_once 'includes/init.php';
get_header();
$user = $context->getConnectedUser();
$userSettings = $user->getSetting();

/**
 * Template Name: user_profile
 */
?>
<div class="other-profile-bkg">
    <div id="content-center">
        <?php
        $profileView =new \Sb\View\UserProfile($user, $userSettings, true, true, false);
        echo $profileView->get();
        ?>
    </div>
    <div id="content-right">
        <div class="right-frame">
            <?php
            $ad = new \Sb\View\Components\Ad("","");
            echo $ad->get();
            ?>
        </div>        
    </div>
</div>
<?php get_footer(); ?>