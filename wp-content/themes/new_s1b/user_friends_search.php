<?php
require_once 'includes/init.php';
get_header();
require_once 'user_friends_search_1.php';
/**
 * Template Name: user_friends_search
 */
/* * **********************************************************************************
 * Functions
 * *********************************************************************************** */
?>
<?php
$userNavigation = new \Sb\View\Components\UserNavigation;
echo $userNavigation->get();
?>
<?php showFlashes(); ?>
<div id="content-wrap" class="add-friend-bkg">
    <div id="content-center">
        <div class="add-friend-header">
            <div class="adh-title"><?php _e("Ajouter un ami","s1b");?></div>
            <div class="adh-subtitle"><?php echo sprintf(__("%s membres sur %s","s1b"), $nbUsers, \Sb\Entity\Constants::SITENAME);?></div>
        </div>
        <?php if (!$query) { ?>

        <div class="friends-search">
            <div class="">
                <a href="#" class="link" onclick="facebookNewInvite(); return false;">
                    <?php echo sprintf(__("Inviter vos amis de Facebook à rejoindre %s", "s1b"), \Sb\Entity\Constants::SITENAME); ?>
                    <br/>
                    <img src="<?php echo $context->getBaseUrl(); ?>Resources/images/Facebook_join_button.jpg" />                
                </a>
            </div>
            <div class="horizontal-sep-1"></div>
            <div class="">
                <a class="link" href="<?php echo \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_FRIENDS_FRIENDS); ?>">
                    <?php echo __("Afficher les amis d'amis membres de", "s1b") . " " . \Sb\Entity\Constants::SITENAME; ?>
                </a>
            </div>
            <div class="horizontal-sep-1"></div>        
            <div class="">
                <?php echo __("Ou chercher un membre déjà inscrit à l'aide de son nom, prénom, identifiant ou email", "s1b"); ?>
                <div>
                    <?php
                    $searchFromView = new \Sb\View\Components\FriendsSearchForm;
                    echo $searchFromView->get();
                    ?>
                </div>
            </div>
        </div>
        
        <?php } else { if ($foundUsers && count($foundUsers) > 0) { ?>
        
        <div class="navigation">
            <div class="inner-padding">
                <div class="nav-links">
                    <?php echo $navigation;?>    
                </div>
                <div class="nav-position"><?php echo sprintf(__("Utilisateur(s) %s à %s sur %s","s1b"), $firstItemIdx, $lastItemIdx, $nbItemsTot) ;?></div>
            </div>
        </div>
        
        <div class="friends-list">
        <?php
        $i = 0;
        foreach($foundUsers as $foundUser) {
            $foundUserLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::FRIEND_PROFILE, array("fid" => $foundUser->getId()));
            $foundUserLRequestLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_FRIENDS_REQUEST, array("fid" => $foundUser->getId()));
            if (($i%3 == 0) && ($i != 0)) {echo "<div class=\"horizontal-sep-1\"></div>";}
            $i += 1;
            ?>
            <div class="friend-item">                
                <div class="inner-padding">
                    <a href="<?php echo $foundUserLink; ?>">
                        <?php
                        $avatar = $foundUser->getGravatar();
                        if ($avatar == "")
                            $avatar = $context->getBaseUrl() . "/Resources/images/avatars/noavatar.png";
                        ?>
                        <img class="image-frame image-thumb-square" src="<?php echo $avatar;?>" />
                    </a>
                    <div class="fi-line margin-top-l">
                        <span class="fil-username">
                            <?php echo \Sb\Helpers\StringHelper::tronque($foundUser->getFirstName(), 20) . " " . mb_substr($foundUser->getLastName(), 0, 1) . ".";?>
                        </span>
                    </div>                    
                    <div class="fi-line">
                        <span class="fil-label"><?php echo __("Pays : ", "s1b") ;?></span>
                        <?php if ($foundUser->getCountry() != '') { 
                            $country = \Sb\Db\Dao\CountryDao::getInstance()->getCountryByCode($foundUser->getCountry());
                            echo ($_SESSION['WPLANG']=='fr-FR'? $country->getLabel_french() : $country->getLabel_english());
                        } ;?>
                    </div> 
                    <div class="fi-line">
                        <span class="fil-label"><?php echo __("Langue : ", "s1b") ;?></span>
                        <?php if ($foundUser->getLanguage() != '') { echo $foundUser->getLanguage(); } ;?>
                    </div> 
                    <div class="fi-line">
                        <span class="fil-label"><?php echo __("Sexe : ", "s1b") ;?></span>
                        <?php
                        if ($foundUser->getGender() != "") {
                            if ($foundUser->getGender() == "male") {
                                echo __("masculin", "s1b");
                            } else {
                                echo __("féminin", "s1b");
                            }
                        }
                        ?>
                    </div>
                    <div class="fi-line">
                        <span class="fil-label"><?php _e("Membre depuis le", "s1b") ;?></span>
                        <?php echo $foundUser->getCreated()->format('d/m/Y'); ?>
                    </div>                    
                    <div class="fi-line">
                        <a class="button bt-blue-addfriend margin-top-l" href="<?php echo $foundUserLRequestLink; ?>" ><?php _e("Ajouter en ami","s1b");?></a>    
                    </div>                    
                </div>
        </div>
        <?php } ?>
        </div>
        <div class="navigation">
            <div class="inner-padding">
                <div class="nav-links">
                    <?php echo $navigation;?>    
                </div>
            </div>
        </div>

        <?php } else { ?>
        <span class="message_info_arrondi"><?php _e("aucun résultat", "s1b"); ?> </span>
        <div class="search_FriendsList_refresh" style="margin-left:10px;">
            <a href="<?php echo \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_FRIENDS_FIND); ?>" ><?php echo _e("Réinitialiser", "s1b"); ?></a>
        </div>
        <?php } ?>
    <?php } ?>
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
            $ad = new \Sb\View\Components\Ad("user_friends_search", "0462240993");
            echo $ad->get();
        ?>
        </div>
    </div>
<?php get_footer(); ?>