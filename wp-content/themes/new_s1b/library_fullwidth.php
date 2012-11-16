<?php

$noAuthentification = true;
require_once 'includes/init.php';
get_header();
/**
 * Template Name: library_fullwidth
 */

?>
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