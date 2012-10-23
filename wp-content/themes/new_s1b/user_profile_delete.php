<?php
require_once 'includes/init.php';
get_header();
require_once 'user_profile_delete_1.php';

/**
 * Template Name: user_profile_delete
 */
?>
<?php
$userNavigation = new \Sb\View\Components\UserNavigation;
echo $userNavigation->get();
?>
<?php showFlashes(); ?>
<div id="content-wrap" class="user-profile-bkg">
    <div id="content-center">
        <div class="profile-delete ">            
            <?php
            $profileView =new \Sb\View\UserProfile($user, $userSettings, false, false, false);
            echo $profileView->get();
            ?>        
            <form action="" method="post">
                <input type="hidden" name="delete" value="1" />
                <div class="pd-title">
                    <?php _e("Supprimer votre compte","s1b"); ?><br/>
                </div>
                <div class="pd-description">
                    <?php _e("La suppression de votre compte n'entraînera pas la suppression de vos commentaires. Etes-vous sûr de vouloir le supprimer ?", "s1b"); ?>
                </div>
                <div class="buttons-bar">   
                    <div class="buttons-bar">
                        <button class="float-right button bt-black-m margin-right margin-left"><?php _e("Supprimer","s1b");?></button>
                        <?php if (!$_POST) {?>
                        <a class="button bt-blue-xs float-right" href="javascript:history.back()" class="link"><?php _e("Annuler", "s1b") ?></a>
                        <?php } ?>
                    </div>
               </div>
            </form>
        </div>
    </div>
    <div id="content-right">
        <?php
        $userToolBox = new \Sb\View\Components\UserToolBox;
        echo $userToolBox->get();
        ?>
    </div>
<?php get_footer(); ?>