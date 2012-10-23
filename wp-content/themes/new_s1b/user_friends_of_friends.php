<?php
require_once 'includes/init.php';
get_header();
require_once 'user_friends_of_friends_1.php';
/**
 * Template Name: user_friends_of_friends
 */
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
        <?php if (count($friendsFriends) == '0') { ?>
            <span class=""><?php _e("Vous n'avez pas encore d'amis, invitez en pour voir leurs amis", "s1b"); ?></span>
            <?php
        } else {?>
            <div class="navigation">
                <div class="inner-padding">
                    <div class="nav-links">
                        <?php echo $navigation;?>    
                    </div>
                    <div class="nav-position"><?php echo sprintf(__("Ami(s) d'ami(s) %s à %s sur %s","s1b"), $firstItemIdx, $lastItemIdx, $nbItemsTot) ;?></div>
                </div>
            </div>
            <div class="friends-list">
                <?php
                $i = 0;
                foreach ($friendsFriends as $friendFriend) {
                    $friendProfileLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::FRIEND_PROFILE, array("fid" => $friendFriend->getId()));
                    $friendRequestLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_FRIENDS_REQUEST, array("fid" => $friendFriend->getId()));
                    if (($i%3 == 0) && ($i != 0)) {echo "<div class=\"horizontal-sep-1\"></div>";}
                    $i += 1;
                    ?>
                <div class="friend-item">                
                    <div class="inner-padding">
                        <a href="<?php echo $friendProfileLink; ?>">
                            <?php
                            $avatar = $friendFriend->getGravatar();
                            if ($avatar == "")
                                $avatar = $context->getBaseUrl() . "/Resources/images/avatars/noavatar.png";
                            ?>
                            <img class="image-frame image-thumb-square" src="<?php echo $avatar;?>" />
                        </a>
                        <div class="fi-line">
                            <span class="fil-username">
                                <?php echo \Sb\Helpers\StringHelper::tronque($friendFriend->getFirstName(), 20) . " " . mb_substr($friendFriend->getLastName(), 0, 1) . ".";?>
                            </span>
                        </div>                    
                        <div class="fi-line">
                            <span class="fil-label"><?php echo __("Pays : ", "s1b") ;?></span>
                            <?php                             
                            if ($friendFriend->getCountry() != '') {
                                $country = \Sb\Db\Dao\CountryDao::getInstance()->getCountryByCode($friendFriend->getCountry());
                                $countryLabel = ($_SESSION['WPLANG'] <> "en_US" ? $country->getLabel_french(): $country->getLabel_english());
                                echo $countryLabel;
                            }?>
                        </div>
                        <div class="fi-line">
                            <span class="fil-label"><?php echo __("Sexe : ", "s1b");?></span>
                            <?php
                            if ($friendFriend->getGender() != "")
                                echo (($friendFriend->getGender() == "male") ? __("Masculin", "s1b") : __("Féminin", "s1b")) ;
                            ?>
                        </div>
                        <div class="fi-line">
                            <span class="fil-label"><?php echo __("Langue : ", "s1b");?></span>
                            <?php if ($friendFriend->getLanguage() != '') echo $friendFriend->getLanguage(); ?>
                        </div>                        
                        <div class="fi-line">
                            <span class="fil-label"><?php echo __("Membre depuis : ", "s1b"); ?></span>
                            <?php echo $friendFriend->getCreated()->format('d/m/Y'); ?>
                        </div>
                        <div class="fi-line">
                            <a class="button bt-blue-addfriend margin-top-l" href="<?php echo $friendRequestLink; ?> " alt="<?php _e("Ajouter en ami", "s1b"); ?>">
                                <?php _e("Ajouter en ami", "s1b"); ?>
                            </a>
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
            <?php } ?>
    </div>
    <div id="content-right">
        <?php
        $userToolBox = new \Sb\View\Components\UserToolBox;
        echo $userToolBox->get();
        ?>
    </div>
<?php get_footer(); ?>