<?php
require_once 'includes/init.php';
get_header();
require_once 'user_friends_of_friends_1.php';

use Sb\View\Components\FriendsWidget;
use Sb\View\Components\Ad;
use Sb\Helpers\HTTPHelper;
use Sb\Helpers\UserHelper;

/**
 * Template Name: user_friends_of_friends
 */
?>

<div class="add-friend-bkg">
    <div id="content-center">
        <div class="add-friend-header">
            <div class="adh-title"><?php _e("Ajouter un ami", "s1b"); ?></div>
            <div class="adh-subtitle"><?php echo sprintf(__("%s membres sur %s", "s1b"), $nbUsers, \Sb\Entity\Constants::SITENAME); ?></div>
        </div>
        <?php if (count($friendsFriends) == '0') { ?>
            <span class=""><?php _e("Vous n'avez pas encore d'amis, invitez en pour voir leurs amis", "s1b"); ?></span>
        <?php } else { ?>
            <div class="navigation">
                <div class="inner-padding">
                    <div class="nav-links">
                        <?php echo $navigation; ?>    
                    </div>
                    <div class="nav-position"><?php echo sprintf(__("Ami(s) d'ami(s) %s à %s sur %s", "s1b"), $firstItemIdx, $lastItemIdx, $nbItemsTot); ?></div>
                </div>
            </div>
            <div class="friends-list">
                <?php
                $i = 0;
                foreach ($friendsFriends as $friendFriend) {
                    $friendProfileLink = HTTPHelper::Link(\Sb\Entity\Urls::USER_PROFILE, array("uid" => $friendFriend->getId()));
                    $friendRequestLink = HTTPHelper::Link(\Sb\Entity\Urls::USER_FRIENDS_REQUEST, array("fid" => $friendFriend->getId()));
                    if (($i % 3 == 0) && ($i != 0)) {
                        echo "<div class=\"horizontal-sep-1\"></div>";
                    }
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
                                <img class="image-frame image-thumb-square" src="<?php echo $avatar; ?>" />
                            </a>
                            <div class="fi-line">
                                <span class="fil-username">
                                    <?php echo $friendFriend->getFriendlyName(); ?>
                                </span>
                            </div>
                            <div class="fi-line">
                                <span class="fil-value"><?php echo UserHelper::getFullGenderAndAge($friendFriend) ;?></span>
                            </div>
                            <div class="fi-line">
                                <span class="fil-label"><?php _e("Identifiant : ","s1b");?></span>
                                <span class="fil-value"><?php echo $friendFriend->getUserName();?></span>
                            </div>
                            <div class="fi-line">
                                <?php echo UserHelper::getFullCityAndCountry($friendFriend);?>
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
            <?php echo $navigation; ?>    
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div id="content-right">
        <div class="right-frame">
            <?php
            $friendWidget = new FriendsWidget;
            echo $friendWidget->get();
            ?>
        </div>
        <div class="right-frame">
        <?php
            $ad = new Ad("user_friends", "2432422854");
            echo $ad->get();
        ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>