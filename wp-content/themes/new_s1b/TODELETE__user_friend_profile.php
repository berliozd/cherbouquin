<?php
require_once 'includes/init.php';
get_header();
require_once 'user_friend_profile_1.php';

use Sb\View\Components\AutoPromoWishlistWidget;

/**
 * Template Name: user_friend_profile
 */
?>

<script>
toInit.push("attachUserEventsExpandCollapse()");
function attachUserEventsExpandCollapse() {
    _attachExpandCollapseBehavior("js_userLastEvents", "userEvent", "Voir moins d'activités", "Voir plus d'activités");

}
</script>
<div id="other-user-profile" class="other-user-profile-bkg">
    <div id="content-center" >
        <div class="friend-profile">
            
            <?php
            $profileView =new \Sb\View\OtherUserProfile($friend, $friendSetting, $isFriend);
            echo $profileView->get();
            ?>
            
            <div class="horizontal-sep-1"></div>
            
            <!-- blow of hearts START -->
            <div class="pushed-books pushedBooks margin-bottom-xl">
                <?php
                $friendBohBooks = new \Sb\View\BookShelf($bohBooks, sprintf(__("Derniers coups de coeur de %s","s1b"), ucwords($friend->getFirstName())));
                echo $friendBohBooks->get();
                ?>
            </div>
            <!-- blow of hearts END -->
            
            <!-- last reading START -->
            <div class="pushed-books pushedBooks margin-bottom-xl">
                <div class="pb-title"><?php echo sprintf(__("Dernières lectures de %s","s1b"), ucwords($friend->getFirstName())); ?></div>
                <?php if ($currentlyReadingOrLastlyReadBooks) {
                    $friendLastlyReadBooks = new \Sb\View\PushedUserBooks($currentlyReadingOrLastlyReadBooks, 3, false);                    
                    echo $friendLastlyReadBooks->get();
                } else {?>
                    <div class="pb-nobooks"><?php _e("Non renseigné","s1b");?></div>
                <?php }?>
            </div>
            <!-- last reading END -->
            
            <!-- books he could like START -->
            <div class="pushed-books margin-bottom-xl">
                <?php if ($booksHeCouldLikes && (count($booksHeCouldLikes)>0)) {?>
                <script src="<?php echo $context->getBaseUrl() . 'Resources/js/waterwheel-carousel/jquery.waterwheelCarousel.min.js' ?>" ></script>
                <script>
                    $(document).ready(function () {
                        $("#bookUserCouldLike").waterwheelCarousel({
                        startingWaveSeparation: 0,
                        centerOffset: 30,
                        startingItemSeparation: 90,
                        itemSeparationFactor: .7,
                        opacityDecreaseFactor: 1,
                        autoPlay: 1500,
                        movedToCenter: function(newCenterItem) { 
                            $(".coverflip-caption", $("#bookUserCouldLike")).hide();
                            $(".coverflip-caption", newCenterItem.parent()).show();
                        },
                        clickedCenter: function(clickedItem) {document.location.href = clickedItem.attr('href');}
                        });
                  });
                </script>
                <?php }?>
                <?php 
                $bookUseCouldLikeCoverFlip = new Sb\View\BookCoverFlip($booksHeCouldLikes, __("Idées cadeaux...ça pourrait lui plaire","s1b"), "bookUserCouldLike", "books-user-could-like");
                echo $bookUseCouldLikeCoverFlip->get();
                ?>
            </div>            
            <!-- books he could like END -->
            
            <?php 
            $friendLastReviewsView = new Sb\View\LastReviews($friendLastReviews, sprintf(__("Dernières critiques postées par %s", "s1b"), ucwords($friend->getFirstName())));
            echo $friendLastReviewsView->get();
            ?>
            
        </div>
    </div>
    <div id="content-right">
        <div class="right-frame">
            <?php
            $autoPromoWishlist = new AutoPromoWishlistWidget();
            echo $autoPromoWishlist->get();
            ?>
        </div>
        <div class="right-frame">
            <div class="carousel">
            <?php if (count($allCurrentlyReadingUserBooks) > 0) {?>
                
                <?php if (count($allCurrentlyReadingUserBooks) > 1) {?>
            
                <script type="text/javascript" src="<?php echo BASE_URL . 'Resources/js/simple-carousel/simple.carousel.js';?>"></script>
                <script>jQuery(document).ready(function() {
                    $("ul.carousel-currentreadings").simplecarousel({
                        width:298,
                        height:190,
                        auto: 8000,
                        fade: 200,
                        pagination: true
                    });
                });
                </script>
            
                <?php } ?>
                
                <?php 
                $userReading = new \Sb\View\Components\UserReadingWidget($friend, $allCurrentlyReadingUserBooks, false);    
                echo $userReading->get();
                ?>            
                
            <?php } ?>
            </div>
        </div>
        <div class="right-frame">
            <?php
            $userWishedBooks = new \Sb\View\Components\UserWishedBooksWidget($friend, false);
            echo $userWishedBooks->get();
            ?>
        </div>
        <div class="right-frame">
            <?php
            $ad = new \Sb\View\Components\Ad("user_friend_profile", "6033296049");
            echo $ad->get();
            ?>
        </div>
        <div class="right-frame">
            <div class="carousel">

                <?php if (count($friendLastFriendsAddedEvents) > 1) { ?>
                    <script type="text/javascript" src="<?php echo BASE_URL . 'Resources/js/simple-carousel/simple.carousel.js';?>"></script>
                    <script>jQuery(document).ready(function() {
                        $("ul.carousel-friendlastfriends").simplecarousel({
                            width:298,
                            height:85,
                            auto: 8000,
                            fade: 200,
                            pagination: true
                        });
                    });
                    </script>
                <?php }?>

                <?php 
                $friendLastAddedFriend = new \Sb\View\OtherUserLastFriends($friend, $friendLastFriendsAddedEvents);
                echo $friendLastAddedFriend->get();
                ?>

            </div>
        </div>
        
        <div class="right-frame">            
            <!-- BLOCK friend last activities START -->
            <?php
            $userLastEvents = new Sb\View\Components\UserLastEvents($friend, $friendLastEvents);
            echo $userLastEvents->get();
            ?>
            <!-- BLOCK friend last activities START -->
        </div>
        
    </div>
</div>
<?php get_footer(); ?>