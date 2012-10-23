<?php
require_once 'includes/init.php';
get_header();
require_once 'user_profile_edit_password_1.php';
/**
 * Template Name: user_profile_edit_password
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
        $profileView =new \Sb\View\UserProfile($user, $userSettings, false, false, false);
        echo $profileView->get();
        ?>        
        <div class="horizontal-sep-1"></div>
        <div class="profile-edit-form">            
            <form action="" method="post">
                <table>
                    <tr>
                        <td class="pef-label">
                            <?php _e("Ancien mot de passe", "s1b"); ?>
                        </td>
                        <td>
                            <input type="Password" class="input-item textinput"  name="Password_old" id="Password_old" value="" />                            
                        </td>
                    </tr>
                    <tr>
                        <td class="pef-label">
                            <?php _e("Nouveau mot de passe", "s1b"); ?>
                        </td>
                        <td>
                            <input type="Password" class="input-item textinput" name="Password_modif" id="Password_modif" value="" />
                        </td>
                    </tr>
                </table>
                <div class="buttons-bar">  
                    <div class="inner-padding">
                        <button class="float-right button bt-black-m margin-right margin-left"><?php _e("Valider","s1b");?></button>
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