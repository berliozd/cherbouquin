<?php
require_once 'includes/init.php';
get_header();
require_once 'user_profile_settings_1.php';

use \Sb\Entity\UserDataVisibility;
use \Sb\View\UserProfile;
use \Sb\Entity\Constants;

/**
 * Template Name: user_profile_settings
 */
?>
<div class="user-profile-bkg">
    <div id="content-center"  >
        <?php
        $profileView =new UserProfile($user, $userSettings, true, true, false);
        echo $profileView->get();
        ?>
        <div class="profile-edit-settings">
            <div class="pes-description">
                <?php
                echo __("Vous pouvez gérer ici le paramétrage de votre compte. Vos Gravatar, prénom, nom, identifiant, pays, langue, sexe et date d'inscription seront toujours visibles sur le site.", "s1b")
                . " " . __("En revanche vous pouvez masquer votre email ainsi que votre anniversaire et pamétrer la confidentialité du reste de votre profil.", "s1b");
                ?>
            </div>
            <form action="" method="post">                
                <div class="pes-line">
                    <div class="pes-question">
                        <?php _e("Qui peut voir votre profil ?", "s1b");?>
                    </div>
                    <div class="pes-question-description">
                        <?php _e("Si vous limitez l'accès à vos amis seuls vos paramètres publics mentionnés ci-dessus seront visibles pour les autres utilisateurs.", "s1b"); ?>
                    </div>
                    <div class="pes-question-field">
                        <input type="radio" name="settings_DisplayProfile" id="radio" value="<?php echo UserDataVisibility::MEMBERS_ONLY; ?>" <?php if ($userSettings->getDisplayProfile() == UserDataVisibility::MEMBERS_ONLY) echo "checked" ?> />
                        <label><?php echo __("membres", "s1b") . " " . Constants::SITENAME; ?></label>
                        <br/>
                        <input type="radio" name="settings_DisplayProfile" id="radio" value="<?php echo UserDataVisibility::FRIENDS; ?>" <?php if ($userSettings->getDisplayProfile() == UserDataVisibility::FRIENDS) echo "checked" ?> />
                        <label><?php _e("seulement mes amis", "s1b"); ?></label>
                    </div>
                </div>
                <div class="pes-line">
                    <div class="pes-question">
                        <?php _e("Votre email est visible pour ?", "s1b"); ?>
                    </div>
                    <div class="pes-question-description">
                        <?php _e("Si vous limitez l'accès à vos amis et que vous avez choisi de ne pas limiter votre profil, votre adresse email ne sera visible que par vos amis.", "s1b"); ?>
                    </div>
                    <div class="pes-question-field">
                        <input type="radio" name="settings_DisplayEmail" id="radio" value="<?php echo UserDataVisibility::MEMBERS_ONLY; ?>" <?php if ($userSettings->getDisplayEmail() == UserDataVisibility::MEMBERS_ONLY) echo "checked" ?> />
                        <label><?php echo __("membres", "s1b") . " " . Constants::SITENAME; ?></label>
                        <br/>
                        <input type="radio" name="settings_DisplayEmail" id="radio" value="<?php echo UserDataVisibility::FRIENDS; ?>" <?php if ($userSettings->getDisplayEmail() == UserDataVisibility::FRIENDS) echo "checked" ?> />
                        <label><?php _e("seulement mes amis", "s1b"); ?></label>
                        <br/>
                        <input type="radio" name="settings_DisplayEmail" id="radio" value="<?php echo UserDataVisibility::NO_ONE; ?>" <?php if ($userSettings->getDisplayEmail() == UserDataVisibility::NO_ONE) echo "checked" ?> />
                        <label><?php _e("personne", "s1b"); ?></label>
                    </div>
                </div>                
                <div class="pes-line">
                    <div class="pes-question">
                        <?php _e("Votre date d'anniversaire est visible pour ?", "s1b"); ?>
                    </div>
                    <div class="pes-question-description">
                        <?php _e("N'oubliez pas que si vos amis y ont accès on leur rappelera de penser à vous !!!", "s1b"); ?>
                    </div>
                    <div class="pes-question-field">
                        <input type="radio" name="settings_DisplayBirthDay" id="radio" value="<?php echo UserDataVisibility::MEMBERS_ONLY?>" <?php if ($userSettings->getDisplayBirthDay() == UserDataVisibility::MEMBERS_ONLY) echo "checked" ?> />
                        <label><?php echo __("membres", "s1b") . " " . Constants::SITENAME; ?></label>
                        <br/>
                        <input type="radio" name="settings_DisplayBirthDay" id="radio" value="<?php echo UserDataVisibility::FRIENDS; ?>" <?php if ($userSettings->getDisplayBirthDay() == UserDataVisibility::FRIENDS) echo "checked" ?> />
                        <label><?php _e("seulement mes amis", "s1b"); ?></label>
                        <br/>
                        <input type="radio" name="settings_DisplayBirthDay" id="radio" value=<?php echo UserDataVisibility::NO_ONE; ?> <?php if ($userSettings->getDisplayBirthDay() == UserDataVisibility::NO_ONE) echo "checked" ?> />
                        <label><?php _e("personne", "s1b"); ?></label>
                    </div>
                </div>
                <div class="pes-line">
                    <div class="pes-question">
                        <?php _e("Qui peut vous envoyer des messages ?", "s1b"); ?>
                    </div>
                    <div class="pes-question-field">
                        <input type="radio" name="settings_SendMessages" id="radio" value="<?php echo UserDataVisibility::MEMBERS_ONLY;?>" <?php if ($userSettings->getSendMessages() == UserDataVisibility::MEMBERS_ONLY) echo "checked" ?>/>
                        <label><?php echo __("membres", "s1b") . " " . Constants::SITENAME; ?></label>
                        <br/>
                        <input type="radio" name="settings_SendMessages" id="radio" value=<?php echo UserDataVisibility::FRIENDS; ?> <?php if ($userSettings->getSendMessages() == UserDataVisibility::FRIENDS) echo "checked" ?>/>
                        <label><?php _e("seulement mes amis", "s1b"); ?></label>
                    </div>
                </div>
                <div class="pes-line">
                    <div class="pes-question">
                        <?php _e("Autorisez-vous un membre qui n'est pas votre ami à suivre votre activité ?", "s1b"); ?>
                        <br/>
                    </div>
                    <div class="pes-question-description">
                        <?php _e("Le follower peut suivre vos lectures, il n'est pour autant pas votre ami et ne pourra de fait pas avoir accès aux informations que vous avez limitées.", "s1b"); ?>
                    </div>
                    <div class="pes-question-field">
                        <input type="radio" name="settings_AllowFollowers" id="radio" value="Yes" <?php if ($userSettings->getAllowFollowers() == "Yes") echo "checked" ?> />
                        <label><?php _e("oui", "s1b"); ?></label>
                        <br/>
                        <input type="radio" name="settings_AllowFollowers" id="radio" value="No" <?php if ($userSettings->getAllowFollowers() == "No") echo "checked" ?> />
                        <label><?php _e("non", "s1b"); ?></label>
                    </div>
                </div>
                <div class="pes-line">
                    <div class="pes-question">
                        <?php _e("Souhaitez-vous recevoir un email lorsqu'un message vous est envoyé ?", "s1b"); ?>
                    </div>
                    <div class="pes-question-field">
                        <input type="radio" name="settings_EmailMe" id="radio" value="Yes" <?php if ($userSettings->getEmailMe() == "Yes") echo "checked" ?> />
                        <label><?php _e("oui", "s1b"); ?></label>
                        <br/>
                        <input type="radio" name="settings_EmailMe" id="radio" value="No" <?php if ($userSettings->getEmailMe() == "No") echo "checked" ?> />
                        <label><?php _e("non", "s1b"); ?></label>
                    </div>
                </div>
                <div class="pes-line">
                    <div class="pes-question">
                        <?php echo sprintf(__("Souhaitez-vous recevoir la newsletter de %s ?", "s1b"), Constants::SITENAME); ?>
                    </div>
                    <div class="pes-question-field">
                        <input type="radio" name="settings_AcceptNewsletter" id="radio" value="1" <?php if ($userSettings->getAccept_newsletter()) echo "checked" ?> />
                        <label><?php _e("oui", "s1b"); ?></label>
                        <br/>
                        <input type="radio" name="settings_AcceptNewsletter" id="radio" value="0" <?php if (!$userSettings->getAccept_newsletter()) echo "checked" ?> />
                        <label><?php _e("non", "s1b"); ?></label>
                    </div>
                </div>
                <br/>
                <div class="pes-line">
                    <div class="pes-question">
                        <?php echo __("Votre liste d'envies est visible pour ?", "s1b"); ?>
                    </div>
                    <div class="pes-question-field">
                        <input type="radio" name="settings_DisplayWishList" id="settings_DisplayWishList" value="<?php echo UserDataVisibility::ALL?>" <?php if ($userSettings->getDisplay_wishlist() == UserDataVisibility::ALL) echo "checked" ?> />
                        <label for="settings_DisplayWishList"><?php echo __("tout le monde", "s1b"); ?></label>
                        <br/>
                        <input type="radio" name="settings_DisplayWishList" id="settings_DisplayWishList" value="<?php echo UserDataVisibility::MEMBERS_ONLY?>" <?php if ($userSettings->getDisplay_wishlist() == UserDataVisibility::MEMBERS_ONLY) echo "checked" ?> />
                        <label for="settings_DisplayWishList"><?php echo __("membres", "s1b") . " " . Constants::SITENAME; ?></label>                        
                    </div>
                </div>
                <br/>
                <div class="buttons-bar">   
                    <div class="inner-padding">
                        <button class="float-right button bt-blue-m margin-right margin-left"><?php _e("Valider","s1b");?></button>
                        <?php if (!$_POST) {?>
                        <a class="button bt-black-xs float-right" href="javascript:history.back()" class="link"><?php _e("Annuler", "s1b") ?></a>
                        <?php } ?>
                    </div>
               </div>
            </form>
            <div class="horizontal-sep-1"></div>
            <div class="profile-delete">                
                <div><?php _e("Vous souhaitez supprimer votre compte?","s1b");?></div>
                <a class="button bt-black-l bt-delete-account" href="<?php echo \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_PROFILE_DELETE_ACCOUNT); ?>"><?php echo _e("supprimer votre compte", "s1b"); ?></a>
            </div>
        </div>
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