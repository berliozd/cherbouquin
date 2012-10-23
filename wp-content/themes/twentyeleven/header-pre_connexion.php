<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <!--<![endif]-->
    <head>
        <link rel="icon" type="image/png" href="http://www.share1book.com/favicon.png" />
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/><!--HTML4-->
        <meta name="viewport" content="width=device-width" />
        <meta charset="UTF-8"><!--HTML5-->
        <title><?php
/*
 * Print the <title> tag based on what is being viewed.
 */
global $page, $paged;

wp_title('|', true, 'right');

// Add the blog name.
bloginfo('name');

// Add the blog description for the home/front page.
$site_description = get_bloginfo('description', 'display');
if ($site_description && ( is_home() || is_front_page() ))
    echo " | $site_description";

// Add a page number if necessary:
if ($paged >= 2 || $page >= 2)
    echo ' | ' . sprintf(__('Page %s', 'twentyeleven'), max($paged, $page));
?>
        </title>
        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_url'); ?>" />
        <link type="text/css" media="screen" rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/s1b_style.css"  />
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
        <!--[if lt IE 9]>
        <script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
        <![endif]-->
        <?php
        /* We add some JavaScript to pages with the comment form
         * to support sites with threaded comments (when in use).
         */
        if (is_singular() && get_option('thread_comments'))
            wp_enqueue_script('comment-reply');

        /* Always have wp_head() just before the closing </head>
         * tag of your theme, or you will break many plugins, which
         * generally use this hook to add elements to <head> such
         * as styles, scripts, and meta tags.
         */
        var_dump("avant wp_head");
        wp_head();
        ?>
    </head>


    <body <?php body_class(); ?>>
        <div id="page" class="hfeed">
            <header id="branding" role="banner">

                <div id="s1b_menu" role="navigation"><!-- #Social -->
                    <div class="Logo"><!-- #branding banner-->
                        <a href="<?php echo bloginfo('url'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/headers/s1B.png" /></a>
                        <div class="clear"></div>
                    </div><!-- #fermeture branding banner-->

                    <div class="navigation">
                        <ul class="navigation" class="clearfloat">
                            <li>
                                <a href="<?php dirname(__FILE__); ?>/membre/connexion/"><?php _e("S'identifier", "s1b"); ?></a>
                            </li>
                    </div>

                    <div class="navigation">
                        <ul class="navigation" class="clearfloat">
                            <li>
                                <a href="<?php dirname(__FILE__); ?>/membre/inscription/"><?php _e("S'inscrire", "s1b"); ?></a>
                            </li>
                        </ul>
                    </div>

                    <div class="s1b_sub_menu">
                        <div class="lang">
                            <a href = "http://share1book.com/<?php echo (buildLangUrl("fr_FR")); ?>"><img src = "<?php bloginfo('stylesheet_directory'); ?>/images/FR-25x18.png" style = "width:20px ; height:18px; padding-right:0" /></a>
                        </div>

                        <div class="lang">
                            <a href = "http://share1book.com/<?php echo (buildLangUrl("en_US")); ?>"><img src = "<?php bloginfo('stylesheet_directory'); ?>/images/EN-25x18.png" style = "width:20px ; height:18px; padding-right:0"></a>
                        </div>
                    </div>


                    <div class = "clear"></div>

                </div><!--#fermeture Social -->

            </header><!--#branding -->

            <div id = "main">