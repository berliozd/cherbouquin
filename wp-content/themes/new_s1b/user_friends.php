<?php
use Sb\Helpers\ArrayHelper;
use Sb\Helpers\UserHelper;
use Sb\Helpers\HTTPHelper;

require_once 'includes/init.php';
get_header();
require_once 'user_friends_1.php';

use Sb\View\Components\FriendsWidget;
use Sb\View\Components\Ad;

/**
 * Template Name: user_friends
 */
/* * **********************************************************************************
 * Functions
 * *********************************************************************************** */
?>
<div id="content-wide">
    <div class="friends-list-header">
        <?php
        $friendsPageNavigation = new \Sb\View\Components\FriendsPageNavigation("friends");
        echo $friendsPageNavigation->get();
        ?>
        <div class="fl-search">                
            <div class="inner-padding">
                <div class="fls-label"><?php _e("Rechercher un ami", "s1b"); ?></div>
                <div class="fls-form">
                    <?php $search_member = htmlspecialchars(ArrayHelper::getSafeFromArray($_GET, "q", ""));?>
                    <form charset="utf-8" action="" method="get" >
                        <div class="search-field">
                            <input class="search-field-input" type="text" name="q" id="q" value="<?php echo $search_member;?>" />
                            <button class="search-button"></button>
                        </div>
                    </form>
                </div>                
                <a class="link" href="<?php echo HTTPHelper::Link(\Sb\Entity\Urls::USER_FRIENDS); ?>" ><?php _e("Réinitialiser", "s1b"); ?></a>
            </div>
        </div>
    </div>        
</div>
<div id="content-center">
    <div class="friends-list">
        <?php if ($nbFriends == '0') {?>
        <div class="">
            <span class="" ><?php _e("Vous n'avez pas encore d'amis, invitez en pour échanger avec eux", "s1b"); ?></span>
        </div>
        <?php } else { ?>
        <div class="navigation">
           <div class="inner-padding">
               <div class="nav-links">
                   <?php echo $navigation;?>    
               </div>
               <div class="nav-position"><?php echo sprintf(__("Ami(s) %s à %s sur %s","s1b"), $firstItemIdx, $lastItemIdx, $nbItemsTot) ;?></div>
           </div>
        </div>        
    <?php
    $i = 0;
    foreach ($friends as $friend) {                    
        $friendLibraryLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::FRIEND_LIBRARY, array("fid" => $friend->getId()));
        $friendProfileLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_PROFILE, array("uid" => $friend->getId()));
        if (($i%3 == 0) && ($i != 0)) {echo "<div class=\"horizontal-sep-1\"></div>";}
        $i += 1;
        ?>                        
        <div class="friend-item">                
            <div class="inner-padding">
                <a href="<?php echo $friendProfileLink;?>">
                    <img class="image-frame image-thumb-square" src="<?php echo $friend->getGravatar();?>" />
                </a>
                <div class="fi-line">
                    <span class="fil-value fil-username">
                        <?php echo \Sb\Helpers\StringHelper::tronque(\Sb\Helpers\UserHelper::getFullName($friend), 25);?>                            
                    </span>
                </div>
                <div class="fi-line">
                    <span class="fil-label"><?php _e("Email : ","s1b");?></span>
                    <span class="fil-value"><?php echo (($friend->getSetting()->getDisplayEmail() != \Sb\Entity\UserDataVisibility::NO_ONE)? \Sb\Helpers\StringHelper::tronque($friend->getEmail(), 30) : __("donnée privée", "s1b"));?></span>
                </div>
                <div class="fi-line">
                    <span class="fil-value"><?php echo UserHelper::getFullGenderAndAge($friend) ;?></span>
                </div>
                <div class="fi-line">
                    <span class="fil-label"><?php _e("Identifiant : ","s1b");?></span>
                    <span class="fil-value"><?php echo $friend->getUserName();?></span>
                </div>
                <div class="fi-line">
                    <span class="fil-label"><?php _e("Membre depuis : ","s1b");?></span>
                    <span class="fil-value"><?php echo $friend->getCreated()->format(__("d/m/Y", "s1b"));?></span>
                </div>
                <div class="fi-line">
                    <a href="<?php echo $friendProfileLink;?>" class="link"><?php _e("Voir son profil","s1b");?></a>
                </div>
                <div class="fi-line-sep"></div>
                <?php
                // Get friend not deleted userbooks
                $criteria = array();
                $criteria["is_deleted"] = array(false, "=", 0);
                $criteria["user"] = array(true, "=", $friend);
                $orderBy = array("id" => "DESC");
                $friendUserBooks = Sb\Db\Dao\UserBookDao::getInstance()->getList($criteria, $orderBy, 1);
                if (count($friendUserBooks)) {
                    $book = $friendUserBooks[count($friendUserBooks)-1]->getBook(); // getting last book
                    $bookLink = \Sb\Helpers\HTTPHelper::Link($book->getLink());
                ?>
                    <div class="fi-image">
                        <a href="<?php echo $bookLink;?>"><?php echo \Sb\Helpers\BookHelper::getSmallImageTag($book, $context->getDefaultImage());?></a>
                    </div>
                    <div class="fi-text">
                        <div class="fit-title">
                            <?php echo \Sb\Helpers\StringHelper::tronque($book->getTitle(), 100);?>
                        </div>
                        <div class="fit-publication-info">
                            <?php echo \Sb\Helpers\StringHelper::tronque($book->getPublicationInfo(), 100);?>
                        </div>
                        <div class="fit-author">
                            <?php echo \Sb\Helpers\StringHelper::tronque($book->getOrderableContributors(), 100);?>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="fi-image">
                        <img class="image-thumb-small image-frame" src="<?php echo $context->getDefaultImage(); ?>"/>
                    </div>
                <?php } ?>
                <div class="fi-line">
                    <a href="<?php echo $friendLibraryLink; ?>" class="link"><?php echo __("voir sa bibliothèque", "s1b"); ?></a>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="navigation">
            <div class="inner-padding">
                <div class="nav-links">
                    <?php echo $navigation;?>    
                </div>
            </div>
        </div>
        <?php }?>
    </div>
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
<?php get_footer(); ?>