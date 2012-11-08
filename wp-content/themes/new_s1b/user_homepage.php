<?php
require_once 'includes/init.php';
get_header();

use \Sb\Helpers\BookHelper;
use \Sb\Helpers\HTTPHelper;
use \Sb\Db\Dao\BookDao;
use \Sb\Db\Dao\UserEventDao;
use \Sb\View\UserEvents;

/**
 * Template Name: user_homepage
 */
?>
<?php
$userNavigation = new \Sb\View\Components\UserNavigation;
echo $userNavigation->get();
?>
<?php showFlashes(); ?>
<div id="content-wrap">
    <div id="content-center">
        
        <?php 
        function getBookId(\Sb\Db\Model\Book $book) {
            return $book->getId();
        }
        function notInArray(\Sb\Db\Model\Book $book) {
            global $blowOfHeartFriendsBooksId;
            return !in_array($book->getId(), $blowOfHeartFriendsBooksId);
        }
        // Getting friends boh
        $blowOfHeartFriendsBooks = BookDao::getInstance()->getListBOHFriends($context->getConnectedUser()->getId()); 
        if (!$blowOfHeartFriendsBooks || count($blowOfHeartFriendsBooks) < 5) {
            $blowOfHeartFriendsBooksId = array_map("getBookId", $blowOfHeartFriendsBooks);
            // Getting all users boh
            $blowOfHeartBooks = BookDao::getInstance()->getListBOH();
            $blowOfHeartBooks = array_filter($blowOfHeartBooks, "notInArray");
            // Merging 2 arrays
            if ($blowOfHeartFriendsBooks && $blowOfHeartBooks)                
                $blowOfHeartBooks = array_merge($blowOfHeartFriendsBooks, $blowOfHeartBooks);
            $blowOfHeartBooks = array_slice($blowOfHeartBooks, 0, 5);
        } else
            $blowOfHeartBooks = $blowOfHeartFriendsBooks;
        ?>
        <?php if ($blowOfHeartBooks && count($blowOfHeartBooks) > 0) { ?>
        <div class="pushed-books pushedBooks">
            <div class="pb-title">
                <?php ($blowOfHeartFriendsBooks ? _e("Coups de coeur de vos amis", "s1b") : _e("Coups de coeur", "s1b")); ?>
            </div>
            <div class="pb-shelf">
                <div class="inner-side-padding-30">
                <?php foreach ($blowOfHeartBooks as $blowOfHeartBook) { ?>
                    <div class="pb-bookOnShelf">
                        <a href="<?php echo HTTPHelper::Link($blowOfHeartBook->getLink()); ?>"><?php echo BookHelper::getMediumImageTag($blowOfHeartBook, $context->getDefaultImage());?></a>
                    </div>
                <?php }?>
                </div>
            </div>
        </div>
        <div class="horizontal-sep-1"></div>
        <?php } ?>
        <?php 
        $userEvents = UserEventDao::getInstance()->getListUserFriendsUserEvents($context->getConnectedUser()->getId()); 
        if ($userEvents && count($userEvents > 0)) { 
        ?>
        <div class="pushed-books pushedBooks">
            <div class="pb-title">
                <?php _e("Activités de vos amis", "s1b"); ?>
            </div>
            <?php            
            $userEventsView = new UserEvents($userEvents);
            echo $userEventsView->get();
            ?>
        </div>
        <div class="horizontal-sep-1"></div>
        <?php  } ?>
        
        <div class="pushed-books pushedBooks">
            <div class="pb-title">
                <?php _e("Top 10 des membres", "s1b"); ?>
            </div>
            <?php
            $topsBooks = BookDao::getInstance()->getListTops();
            if (count($topsBooks) == 0) {
                $noTopBooks = new \Sb\View\Components\NoBooksWidget(__("Aucun livre n'a encore été noté par les membres", "s1b"));
                echo $noTopBooks->get();
            } else {
                $view = new \Sb\View\PushedBooks($topsBooks, 3, false);
                echo $view->get();
            }
            ?>
        </div>
    </div>
    <div id="content-right">
        <div class="right-frame">
            <?php
            $userToolBox = new \Sb\View\Components\UserToolBox(true, true);
            echo $userToolBox->get();
            ?>
        </div>       
        <div class="right-frame">
            <?php
            $ad = new \Sb\View\Components\Ad("user_homepage", "6697829998");
            echo $ad->get();
            ?>
        </div>  
    </div>
    <?php get_footer(); ?>