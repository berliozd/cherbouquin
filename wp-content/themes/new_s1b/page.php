<?php

$noAuthentification = true;
require_once 'includes/init.php';
get_header();

if ($context->getConnectedUser()) {
    $userNavigation = new \Sb\View\Components\UserNavigation;
    echo $userNavigation->get();
}
?>
<?php showFlashes(); ?>
<div id="content-wrap">
    <div id="content-center">
        <?php
            while (have_posts()) :
                the_post();
                get_template_part('content', 'page');
                comments_template('', true);
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