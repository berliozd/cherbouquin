<?php
require_once 'includes/init.php';
get_header();
require_once 'user_friend_profile_1.php';

/**
 * Template Name: user_friend_profile
 */
?>
<?php
$userNavigation = new \Sb\View\Components\UserNavigation;
echo $userNavigation->get();
?>
<?php showFlashes(); ?>
<div id="content-wrap" class="other-user-profile-bkg">
    <div id="content-center">        
        <div class="friend-profile">
            <?php
            $profileView =new \Sb\View\UserProfile($friend, $friendSetting, false, false, true);
            echo $profileView->get();
            ?>
            <div class="pushed-books pushedBooks">
                <div class="pb-title"><?php _e("Dernières lectures","s1b"); ?></div>
                <?php if ($currentlyReadingOrLastlyReadBooks) {
                    $friendLastlyReadBooks = new \Sb\View\PushedUserBooks($currentlyReadingOrLastlyReadBooks, $config->getUserLibraryPageName(), 3, false);
                    echo $friendLastlyReadBooks->get();
                } else {?>
                    <div class="pb-nobooks"><?php _e("Non renseigné","s1b");?></div>
                <?php }?>
            </div>
            <div class="horizontal-sep-1"></div>
            <div class="pushed-books pushedBooks">
                <div class="pb-title"><?php _e("Derniers coup de coeur","s1b"); ?></div>
                <?php if ($bohBooks) {
                    $friendBohBooks = new \Sb\View\PushedUserBooks($bohBooks, $config->getUserLibraryPageName(), 3, false);
                    echo $friendBohBooks->get();
                } else {?>
                    <div class="pb-nobooks"><?php _e("Non renseigné","s1b");?></div>
                <?php }?>
            </div>
            <div class="horizontal-sep-1 wished"></div>
            <div class="pushed-books pushedBooks">
                <div class="pb-title"><?php echo sprintf(__("Les envies de %s","s1b"), $friend->getFirstName()); ?></div>
                <?php if ($wishedBooks) {
                    $friendWishedBooks = new \Sb\View\PushedUserBooks($wishedBooks, $config->getUserLibraryPageName(), 3, false);
                    echo $friendWishedBooks->get();
                } else {?>
                    <div class="pb-nobooks"><?php _e("Non renseigné","s1b");?></div>
                <?php }?>
            </div>
        </div>
    </div>
    <div id="content-right">
       <div class="right-frame">
        <?php
            $userToolBox = new \Sb\View\Components\UserToolBox;
            echo $userToolBox->get();
        ?>
        </div>
        <div class="right-frame">
        <?php
            $facebookFrame = new \Sb\View\Components\FacebookFrame();
            echo $facebookFrame->get();
        ?>
        </div>
        <div class="right-frame">
        <?php
            $ad = new \Sb\View\Components\Ad("user_friend_profile", "6033296049");
            echo $ad->get();
        ?>
        </div>
    </div>
<?php get_footer(); ?>