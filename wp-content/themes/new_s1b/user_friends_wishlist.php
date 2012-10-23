<?php
require_once 'includes/init.php';
get_header();
require_once 'user_friends_wishlist_1.php';

/**
 * Template Name: user_friends_wishlist
 */
?>
<?php
$userNavigation = new \Sb\View\Components\UserNavigation;
echo $userNavigation->get();
?>
<?php showFlashes(); ?>
<div id="content-wrap">
    <div id="content-wide">
        <div class="friends-list-header">
        <?php
            $friendsPageNavigation = new \Sb\View\Components\FriendsPageNavigation("wishes");
            echo $friendsPageNavigation->get();
        ?>
            <div class="fl-search">
                <div class="inner-padding">
                    <div class="fls-label"><?php _e("Sélectionner un ami pour accéder à sa la liste.","s1b");?></div>
                    <div class="fls-form">
                        <form action="" name="friendSelectionForm" method="post">                            
                            <select class="input-item selectinput fls-select" onchange="friendSelectionForm.submit()" name="friendId">
                                <option value=""><?php _e("Aucun","s1b"); ?></option>
                            <?php foreach ($friends as $friend) { ?>
                                <option <?php echo (($selectedFriend && ($friend->getId() == $selectedFriend->getId())) ? "selected" : "");?> value="<?php echo $friend->getId();?>"><?php echo $friend->getUserName();?></option>
                            <?php }?>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="content-center">
        <div class="friends-wished-books">
            <?php if ($selectedFriend && $friendWishedBooks) {?>
                <div class="friend-title"><?php echo sprintf(__("Livres souhaités par %s","s1b"), $selectedFriend->getUserName()); ?></div>
                <div class="friend-books">
                    <?php
                    foreach ($friendWishedBooks as $friendUserBook) {
                        if ($friendUserBook->getIsWished()) {
                            $bookView = new \Sb\View\PushedUserBook(false, $friendUserBook);
                            echo $bookView->get();
                        }
                    } ?>
                </div>
            <?php } elseif (!$selectedFriend ){?>
                <div class="fl-choosefriend"><?php _e("Merci de sélectionner un ami dans la liste." , "s1b"); ?> </div>
            <?php } elseif (!$friendWishedBooks) {?>
                <div class="fl-friendnobooks"><?php _e("Cet ami n'a aucun livre souhaité." , "s1b"); ?> </div>
            <?php }?>
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
            $ad = new \Sb\View\Components\Ad("user_friends", "2432422854");
            echo $ad->get();
        ?>
        </div>
    </div>
<?php get_footer(); ?>
