<?php
/**
 * Template Name: testdidier
 */
get_header();
global $s1b;
$s1b->prepare();
?>

<div id="primary">
    <div id="content" role="main">

        <?php while (have_posts()) : the_post(); ?>

            <?php get_template_part('content', 'page'); ?>

            <?php comments_template('', true); ?>

        <?php endwhile; // end of the loop.  ?>

        <?php share1book_userCurrentlyReading(1); ?>
        <?php share1book_userBlowsOfHeart(1); ?>
        <?php share1book_userLastlyReadOrCurrentlyReading(2); ?>



    </div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>