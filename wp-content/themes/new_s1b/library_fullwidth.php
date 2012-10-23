<?php

$noAuthentification = true;
require_once 'includes/init.php';
get_header();
/**
 * Template Name: library_fullwidth
 */
if ($context->getConnectedUser()) {
    $userNavigation = new \Sb\View\Components\UserNavigation;
    echo $userNavigation->get();
}
?>
<?php showFlashes(); ?>
<div id="content-wrap">
    <div id="content-wide">
        <?php
            while (have_posts()) :
                the_post();
                get_template_part('content', 'page');
                comments_template('', true);
            endwhile;
        ?>
    </div>
<?php get_footer(); ?>