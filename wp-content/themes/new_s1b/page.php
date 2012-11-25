<?php
$noAuthentification = true;
require_once 'includes/init.php';
get_header();
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
    <?php if ($context->getConnectedUser()) {?>
    <div class="right-frame">
    <?php
        $userToolBox = new \Sb\View\Components\UserToolBox;
        echo $userToolBox->get();
    ?>
    </div>
    <?php }?>
    <div class="right-frame">
    <?php
        $ad = new \Sb\View\Components\Ad("bibliotheque", "1223994660");
        echo $ad->get();
    ?>
    </div>
</div> 
<?php get_footer(); ?>