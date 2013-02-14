<?php
$noAuthentification = true;
require_once 'includes/init.php';
get_header();
global $globalContext;
?>
<div id="content-center">
    <?php
    while (have_posts()) :
        the_post();
        get_template_part('content', 'page');
    endwhile;
    ?>
</div>
<div id="content-right">
    <div class="right-frame">
        <?php
        $ad = new \Sb\View\Components\Ad("bibliotheque", "1223994660");
        echo $ad->get();
        ?>
    </div>    
    <?php
    $bookId = Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, "bid", null);
    if ($bookId) {
        $bookId = str_replace("/", "", $bookId);
        // Books with same contributors
        $sameAuthorBooks = \Sb\Db\Service\BookSvc::getInstance()->getBooksWithSameContributors($bookId);
        if (count($sameAuthorBooks) > 0) {
        ?>
        <script src='<?php echo $globalContext->getBaseUrl();?>Resources/js/waterwheel-carousel/jquery.waterwheelCarousel.min.js'></script>
        <script>$(function() {initCoverFlip('sameAuthorBooks', 30)});</script>
        <div class="right-frame">
            <?php 
            $sameAuthorBooksView = new Sb\View\SameAuthorBooks($sameAuthorBooks);
            echo $sameAuthorBooksView->get();
            ?>        
        </div>
        <?php } ?>
    <?php } ?>

</div> 
<?php get_footer(); ?>