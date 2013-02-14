<?php
/**
 * Twenty Eleven functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, twentyeleven_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'twentyeleven_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
/**
 * Set the content width based on the theme's design and stylesheet.
 */
if (!isset($content_width))
    $content_width = 584;

// --------------------DIDIER [2012-05-21] TRES IMPORTANT!!!!!!!!!!!!! A ne pas supprimer
// Cela permet de supprimer les liens placés dans <head> vers la page précédente et la page suivante.
// Ces liens étant suivi par le navigateur, Le code présent dans ces pages était donc exécuté et
// cela avait par exemple comme conséquence d'appeler la page de déconnexion lorsque nous étions sur la page d'accueil des membres
// La page de déconnexion étant la page suivant la page
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
// --------------------------------------------------------------------------------------------------


/* * **************************************************************************
 * Utiliser son propre Avatar pour les discussion WordPress
 * ************************************************************************** */
add_filter('avatar_defaults', 'newgravatar');

function newgravatar($avatar_defaults) {
    $myavatar = get_bloginfo('template_directory') . '/images/votre_avatar.png';
    $avatar_defaults[$myavatar] = "Avatar UBPP";
    return $avatar_defaults;
}

/**
 * Tell WordPress to run twentyeleven_setup() when the 'after_setup_theme' hook is run.
 */
add_action('after_setup_theme', 'twentyeleven_setup');

if (!function_exists('twentyeleven_setup')):

    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which runs
     * before the init hook. The init hook is too late for some features, such as indicating
     * support post thumbnails.
     *
     * To override twentyeleven_setup() in a child theme, add your own twentyeleven_setup to your child theme's
     * functions.php file.
     *
     * @uses load_theme_textdomain() For translation/localization support.
     * @uses add_editor_style() To style the visual editor.
     * @uses add_theme_support() To add support for post thumbnails, automatic feed links, and Post Formats.
     * @uses register_nav_menus() To add support for navigation menus.
     * @uses add_custom_background() To add support for a custom background.
     * @uses add_custom_image_header() To add support for a custom header.
     * @uses register_default_headers() To register the default custom header images provided with the theme.
     * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
     *
     * @since Twenty Eleven 1.0
     */
    function twentyeleven_setup() {

        /* Make Twenty Eleven available for translation.
         * Translations can be added to the /languages/ directory.
         * If you're building a theme based on Twenty Eleven, use a find and replace
         * to change 'twentyeleven' to the name of your theme in all the template files.
         */
        load_theme_textdomain('twentyeleven', BASE_PATH . 'languages');

        $locale = get_locale();
        $locale_file = get_template_directory() . "/languages/$locale.php";
        if (is_readable($locale_file))
            require_once( $locale_file );

        // This theme styles the visual editor with editor-style.css to match the theme style.
        add_editor_style();

        // Load up our theme options page and related code.
        require( get_template_directory() . '/inc/theme-options.php' );

        // Grab Twenty Eleven's Ephemera widget.
        require( get_template_directory() . '/inc/widgets.php' );

        // Add default posts and comments RSS feed links to <head>.
        add_theme_support('automatic-feed-links');

        // This theme uses wp_nav_menu() in one location.
        register_nav_menu('primary', __('Primary Menu', 'twentyeleven'));

        // Add support for a variety of post formats
        add_theme_support('post-formats', array('aside', 'link', 'gallery', 'status', 'quote', 'image'));

        // Add support for custom backgrounds
        add_custom_background();

        // This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
        add_theme_support('post-thumbnails');

        // The next four constants set how Twenty Eleven supports custom headers.
        // The default header text color
        define('HEADER_TEXTCOLOR', '000');

        // By leaving empty, we allow for random image rotation.
        define('HEADER_IMAGE', '');

        // The height and width of your custom header.
        // Add a filter to twentyeleven_header_image_width and twentyeleven_header_image_height to change these values.
        define('HEADER_IMAGE_WIDTH', apply_filters('twentyeleven_header_image_width', 1000));
        define('HEADER_IMAGE_HEIGHT', apply_filters('twentyeleven_header_image_height', 288));

        // We'll be using post thumbnails for custom header images on posts and pages.
        // We want them to be the size of the header image that we just defined
        // Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
        set_post_thumbnail_size(HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true);

        // Add Twenty Eleven's custom image sizes
        add_image_size('large-feature', HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true); // Used for large feature (header) images
        add_image_size('small-feature', 500, 300); // Used for featured posts if a large-feature doesn't exist
        // Turn on random header image rotation by default.
        add_theme_support('custom-header', array('random-default' => true));

        // Add a way for the custom header to be styled in the admin panel that controls
        // custom headers. See twentyeleven_admin_header_style(), below.
        add_custom_image_header('twentyeleven_header_style', 'twentyeleven_admin_header_style', 'twentyeleven_admin_header_image');

        // ... and thus ends the changeable header business.
        // Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
        register_default_headers(array(
            'wheel' => array(
                'url' => '%s/images/headers/wheel.jpg',
                'thumbnail_url' => '%s/images/headers/wheel-thumbnail.jpg',
                /* translators: header image description */
                'description' => __('Wheel', 'twentyeleven')
            ),
            'shore' => array(
                'url' => '%s/images/headers/shore.jpg',
                'thumbnail_url' => '%s/images/headers/shore-thumbnail.jpg',
                /* translators: header image description */
                'description' => __('Shore', 'twentyeleven')
            ),
            'trolley' => array(
                'url' => '%s/images/headers/trolley.jpg',
                'thumbnail_url' => '%s/images/headers/trolley-thumbnail.jpg',
                /* translators: header image description */
                'description' => __('Trolley', 'twentyeleven')
            ),
            'pine-cone' => array(
                'url' => '%s/images/headers/pine-cone.jpg',
                'thumbnail_url' => '%s/images/headers/pine-cone-thumbnail.jpg',
                /* translators: header image description */
                'description' => __('Pine Cone', 'twentyeleven')
            ),
            'chessboard' => array(
                'url' => '%s/images/headers/chessboard.jpg',
                'thumbnail_url' => '%s/images/headers/chessboard-thumbnail.jpg',
                /* translators: header image description */
                'description' => __('Chessboard', 'twentyeleven')
            ),
            'lanterns' => array(
                'url' => '%s/images/headers/lanterns.jpg',
                'thumbnail_url' => '%s/images/headers/lanterns-thumbnail.jpg',
                /* translators: header image description */
                'description' => __('Lanterns', 'twentyeleven')
            ),
            'willow' => array(
                'url' => '%s/images/headers/willow.jpg',
                'thumbnail_url' => '%s/images/headers/willow-thumbnail.jpg',
                /* translators: header image description */
                'description' => __('Willow', 'twentyeleven')
            ),
            'hanoi' => array(
                'url' => '%s/images/headers/hanoi.jpg',
                'thumbnail_url' => '%s/images/headers/hanoi-thumbnail.jpg',
                /* translators: header image description */
                'description' => __('Hanoi Plant', 'twentyeleven')
            )
        ));
    }

endif; // twentyeleven_setup

if (!function_exists('twentyeleven_header_style')) :

    /**
     * Styles the header image and text displayed on the blog
     *
     * @since Twenty Eleven 1.0
     */
    function twentyeleven_header_style() {

        // If no custom options for text are set, let's bail
        // get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value
        if (HEADER_TEXTCOLOR == get_header_textcolor())
            return;
        // If we get this far, we have custom styles. Let's do this.
        ?>
        <style type="text/css">
        <?php
// Has the text been hidden?
        if ('blank' == get_header_textcolor()) :
            ?>
                #site-title,
                #site-description {
                    position: absolute !important;
                    clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
                    clip: rect(1px, 1px, 1px, 1px);
                }
            <?php
// If the user has set a custom color for the text use that
        else :
            ?>
                #site-title a,
                #site-description {
                    color: #<?php echo get_header_textcolor(); ?> !important;
                }
        <?php endif; ?>
        </style>
        <?php
    }

endif; // twentyeleven_header_style

if (!function_exists('twentyeleven_admin_header_style')) :

    /**
     * Styles the header image displayed on the Appearance > Header admin panel.
     *
     * Referenced via add_custom_image_header() in twentyeleven_setup().
     *
     * @since Twenty Eleven 1.0
     */
    function twentyeleven_admin_header_style() {
        ?>
        <style type="text/css">
            .appearance_page_custom-header #headimg {
                border: none;
            }
            #headimg h1,
            #desc {
                font-family: "Helvetica Neue", Arial, Helvetica, "Nimbus Sans L", sans-serif;
            }
            #headimg h1 {
                margin: 0;
            }
            #headimg h1 a {
                font-size: 32px;
                line-height: 36px;
                text-decoration: none;
            }
            #desc {
                font-size: 14px;
                line-height: 23px;
                padding: 0 0 3em;
            }
            <?php
            // If the user has set a custom color for the text use that
            if (get_header_textcolor() != HEADER_TEXTCOLOR) :
                ?>
                #site-title a,
                #site-description {
                    color: #<?php echo get_header_textcolor(); ?>;
                }
            <?php endif; ?>
            #headimg img {
                max-width: 1000px;
                height: auto;
                width: 100%;
            }
        </style>
        <?php
    }

endif; // twentyeleven_admin_header_style

if (!function_exists('twentyeleven_admin_header_image')) :

    /**
     * Custom header image markup displayed on the Appearance > Header admin panel.
     *
     * Referenced via add_custom_image_header() in twentyeleven_setup().
     *
     * @since Twenty Eleven 1.0
     */
    function twentyeleven_admin_header_image() {
        ?>
        <div id="headimg">
            <?php
            if ('blank' == get_theme_mod('header_textcolor', HEADER_TEXTCOLOR) || '' == get_theme_mod('header_textcolor', HEADER_TEXTCOLOR))
                $style = ' style="display:none;"';
            else
                $style = ' style="color:#' . get_theme_mod('header_textcolor', HEADER_TEXTCOLOR) . ';"';
            ?>
            <h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></h1>
            <div id="desc"<?php echo $style; ?>><?php bloginfo('description'); ?></div>
            <?php
            $header_image = get_header_image();
            if (!empty($header_image)) :
                ?>
                <img src="<?php echo esc_url($header_image); ?>" alt="" />
            <?php endif; ?>
        </div>
        <?php
    }

endif; // twentyeleven_admin_header_image

/**
 * Sets the post excerpt length to 40 words.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 */
function twentyeleven_excerpt_length($length) {
    return 40;
}

add_filter('excerpt_length', 'twentyeleven_excerpt_length');

/**
 * Returns a "Continue Reading" link for excerpts
 */
function twentyeleven_continue_reading_link() {
    return ' <a href="' . esc_url(get_permalink()) . '">' . __('Continue reading <span class="meta-nav">&rarr;</span>', 'twentyeleven') . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and twentyeleven_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 */
function twentyeleven_auto_excerpt_more($more) {
    return ' &hellip;' . twentyeleven_continue_reading_link();
}

add_filter('excerpt_more', 'twentyeleven_auto_excerpt_more');

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 */
function twentyeleven_custom_excerpt_more($output) {
    if (has_excerpt() && !is_attachment()) {
        $output .= twentyeleven_continue_reading_link();
    }
    return $output;
}

add_filter('get_the_excerpt', 'twentyeleven_custom_excerpt_more');

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function twentyeleven_page_menu_args($args) {
    $args['show_home'] = true;
    return $args;
}

add_filter('wp_page_menu_args', 'twentyeleven_page_menu_args');

/**
 * Register our sidebars and widgetized areas. Also register the default Epherma widget.
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_widgets_init() {

    register_widget('Twenty_Eleven_Ephemera_Widget');

    register_sidebar(array(
        'name' => __('Main Sidebar', 'twentyeleven'),
        'id' => 'sidebar-1',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => __('Showcase Sidebar', 'twentyeleven'),
        'id' => 'sidebar-2',
        'description' => __('The sidebar for the optional Showcase Template', 'twentyeleven'),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => __('Footer Area One', 'twentyeleven'),
        'id' => 'sidebar-3',
        'description' => __('An optional widget area for your site footer', 'twentyeleven'),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => __('Footer Area Two', 'twentyeleven'),
        'id' => 'sidebar-4',
        'description' => __('An optional widget area for your site footer', 'twentyeleven'),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => __('Footer Area Three', 'twentyeleven'),
        'id' => 'sidebar-5',
        'description' => __('An optional widget area for your site footer', 'twentyeleven'),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}

add_action('widgets_init', 'twentyeleven_widgets_init');

if (!function_exists('twentyeleven_content_nav')) :

    /**
     * Display navigation to next/previous pages when applicable
     */
    function twentyeleven_content_nav($nav_id) {
        global $wp_query;

        if ($wp_query->max_num_pages > 1) :
            ?>
            <nav id="<?php echo $nav_id; ?>">
                <h3 class="assistive-text"><?php _e('Post navigation', 'twentyeleven'); ?></h3>
                <div class="nav-previous"><?php next_posts_link(__('<span class="meta-nav">&larr;</span> Older posts', 'twentyeleven')); ?></div>
                <div class="nav-next"><?php previous_posts_link(__('Newer posts <span class="meta-nav">&rarr;</span>', 'twentyeleven')); ?></div>
            </nav><!-- #nav-above -->
            <?php
        endif;
    }

endif; // twentyeleven_content_nav

/**
 * Return the URL for the first link found in the post content.
 *
 * @since Twenty Eleven 1.0
 * @return string|bool URL or false when no link is present.
 */
function twentyeleven_url_grabber() {
    if (!preg_match('/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', get_the_content(), $matches))
        return false;

    return esc_url_raw($matches[1]);
}

/**
 * Count the number of footer sidebars to enable dynamic classes for the footer
 */
function twentyeleven_footer_sidebar_class() {
    $count = 0;

    if (is_active_sidebar('sidebar-3'))
        $count++;

    if (is_active_sidebar('sidebar-4'))
        $count++;

    if (is_active_sidebar('sidebar-5'))
        $count++;

    $class = '';

    switch ($count) {
        case '1':
            $class = 'one';
            break;
        case '2':
            $class = 'two';
            break;
        case '3':
            $class = 'three';
            break;
    }

    if ($class)
        echo 'class="' . $class . '"';
}

if (!function_exists('twentyeleven_comment')) :

    /**
     * Template for comments and pingbacks.
     *
     * To override this walker in a child theme without modifying the comments template
     * simply create your own twentyeleven_comment(), and that function will be used instead.
     *
     * Used as a callback by wp_list_comments() for displaying the comments.
     *
     * @since Twenty Eleven 1.0
     */
    function twentyeleven_comment($comment, $args, $depth) {
        $GLOBALS['comment'] = $comment;
        switch ($comment->comment_type) :
            case 'pingback' :
            case 'trackback' :
                ?>
                <li class="post pingback">
                    <p><?php _e('Pingback:', 'twentyeleven'); ?> <?php comment_author_link(); ?><?php edit_comment_link(__('Edit', 'twentyeleven'), '<span class="edit-link">', '</span>'); ?></p>
                    <?php
                    break;
                default :
                    ?>
                <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
                    <article id="comment-<?php comment_ID(); ?>" class="comment">
                        <footer class="comment-meta">
                            <div class="comment-author vcard">
                                <?php
                                $avatar_size = 68;
                                if ('0' != $comment->comment_parent)
                                    $avatar_size = 39;

                                echo get_avatar($comment, $avatar_size);

                                /* translators: 1: comment author, 2: date and time */
                                printf(__('%1$s on %2$s <span class="says">said:</span>', 'twentyeleven'), sprintf('<span class="fn">%s</span>', get_comment_author_link()), sprintf('<a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a>', esc_url(get_comment_link($comment->comment_ID)), get_comment_time('c'),
                                                /* translators: 1: date, 2: time */ sprintf(__('%1$s at %2$s', 'twentyeleven'), get_comment_date(), get_comment_time())
                                        )
                                );
                                ?>

                                <?php edit_comment_link(__('Edit', 'twentyeleven'), '<span class="edit-link">', '</span>'); ?>
                            </div><!-- .comment-author .vcard -->

                            <?php if ($comment->comment_approved == '0') : ?>
                                <em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.', 'twentyeleven'); ?></em>
                                <br />
                            <?php endif; ?>

                        </footer>

                        <div class="comment-content"><?php comment_text(); ?></div>

                        <div class="reply">
                            <?php comment_reply_link(array_merge($args, array('reply_text' => __('Reply <span>&darr;</span>', 'twentyeleven'), 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                        </div><!-- .reply -->
                    </article><!-- #comment-## -->

                    <?php
                    break;
            endswitch;
        }

    endif; // ends check for twentyeleven_comment()

    if (!function_exists('twentyeleven_posted_on')) :

        /**
         * Prints HTML with meta information for the current post-date/time and author.
         * Create your own twentyeleven_posted_on to override in a child theme
         *
         * @since Twenty Eleven 1.0
         */
        function twentyeleven_posted_on() {
            printf(__('<span class="sep">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a><span class="by-author"> <span class="sep"> by </span> <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'twentyeleven'), esc_url(get_permalink()), esc_attr(get_the_time()), esc_attr(get_the_date('c')), esc_html(get_the_date()), esc_url(get_author_posts_url(get_the_author_meta('ID'))), esc_attr(sprintf(__('View all posts by %s', 'twentyeleven'), get_the_author())), get_the_author()
            );
        }

    endif;

    /**
     * Adds two classes to the array of body classes.
     * The first is if the site has only had one author with published posts.
     * The second is if a singular post being displayed
     *
     * @since Twenty Eleven 1.0
     */
    function twentyeleven_body_classes($classes) {

        if (function_exists('is_multi_author') && !is_multi_author())
            $classes[] = 'single-author';

        if (is_singular() && !is_home() && !is_page_template('showcase.php') && !is_page_template('sidebar-page.php'))
            $classes[] = 'singular';

        return $classes;
    }

    add_filter('body_class', 'twentyeleven_body_classes');



    if (!function_exists('barre_navigation_Mailbox')) :
        /*         * ***********************************************************************************
         * script affichage nb de mail dans Mailbox
         * ************************************************************************************ */

        function barre_navigation_Mailbox($nb_total, $nb_affichage_par_page, $debut, $nb_liens_dans_la_barre) {
            $barre = '';

            /* $nb_affichage_par_page = 12;
              $nb_liens_dans_la_barre = 3; */

// on recherche l'URL courante munie de ses paramÃ¨tre auxquels on ajoute le paramÃ¨tre 'debut' qui jouera le role du premier Ã©lÃ©ment de notre LIMIT
            if ($_SERVER['QUERY_STRING'] == "") {

// METTRE A JOUR L'URL DE LA PAGE ATTENDUE
                $query = getS1bUrl("MY_MAILBOX") . '?debut=';
            } else {
                $tableau = explode("debut=", $_SERVER['QUERY_STRING']);
                $nb_element = count($tableau);
                if ($nb_element == 1) {
                    $query = getS1bUrl("MY_MAILBOX") . '?' . $_SERVER['QUERY_STRING'] . '&debut=';
                } else {
                    if ($tableau[0] == "") {
                        $query = getS1bUrl("MY_MAILBOX") . '?debut=';
                    } else {
                        $query = getS1bUrl("MY_MAILBOX") . '?' . $tableau[0] . 'debut=';
                    }
                }
            }

            /* on calcul le numÃ©ro de la page active */
            $page_active = floor(($debut / $nb_affichage_par_page) + 1);
            /* on calcul le nombre de pages total que va prendre notre affichage */
            $nb_pages_total = ceil($nb_total / $nb_affichage_par_page);

            /* on calcul le premier numero de la barre qui va s'afficher, ainsi que le dernier ($cpt_deb et $cpt_fin)
              exemple : 2 3 4 5 6 7 8 9 10 11 << $cpt_deb = 2 et $cpt_fin = 11 */
            if ($nb_liens_dans_la_barre % 2 == 0) {
                $cpt_deb1 = $page_active - ($nb_liens_dans_la_barre / 2) + 1;
                $cpt_fin1 = $page_active + ($nb_liens_dans_la_barre / 2);
            } else {
                $cpt_deb1 = $page_active - floor(($nb_liens_dans_la_barre / 2));
                $cpt_fin1 = $page_active + floor(($nb_liens_dans_la_barre / 2));
            }

            if ($cpt_deb1 <= 1) {
                $cpt_deb = 1;
                $cpt_fin = $nb_liens_dans_la_barre;
            } elseif ($cpt_deb1 > 1 && $cpt_fin1 < $nb_pages_total) {
                $cpt_deb = $cpt_deb1;
                $cpt_fin = $cpt_fin1;
            } else {
                $cpt_deb = ($nb_pages_total - $nb_liens_dans_la_barre) + 1;
                $cpt_fin = $nb_pages_total;
            }

            if ($nb_pages_total <= $nb_liens_dans_la_barre) {
                $cpt_deb = 1;
                $cpt_fin = $nb_pages_total;
            }

            /* si le premier numÃ©ro qui s'affiche est diffÃ©rent de 1, on affiche << qui sera un lien vers la premiere page */
            if ($cpt_deb != 1) {
                $cible = $query . (0);
                $lien = '<a href="' . $cible . '">' . __("Début", "s1b") . '</a>&nbsp;';
            } else {
                $lien = '';
            }
            $barre .= $lien;

            /* on affiche tous les liens de notre barre, tout en vÃ©rifiant de ne pas mettre de lien pour la page active */
            for ($cpt = $cpt_deb; $cpt <= $cpt_fin; $cpt++) {
                if ($cpt == $page_active) {
                    if ($cpt == $nb_pages_total) {
                        $barre .= $cpt;
                    } else {
                        $barre .= $cpt . '&nbsp;';
                    }
                } else {
                    if ($cpt == $cpt_fin) {
                        $barre .= "<a href='" . $query . (($cpt - 1) * $nb_affichage_par_page);
                        $barre .= "'>" . $cpt . "</a>";
                    } else {
                        $barre .= "<a href='" . $query . (($cpt - 1) * $nb_affichage_par_page);
                        $barre .= "'>" . $cpt . "</a>&nbsp;";
                    }
                }
            }

            $fin = ($nb_total - ($nb_total % $nb_affichage_par_page));
            if (($nb_total % $nb_affichage_par_page) == 0) {
                $fin = $fin - $nb_affichage_par_page;
            }

            /* si $cpt_fin ne vaut pas la derniÃ¨re page de la barre de navigation, on affiche un >> qui sera un lien vers la derniÃ¨re page de navigation */
            if ($cpt_fin != $nb_pages_total) {
                $cible = $query . $fin;
                $lien = '&nbsp;<a href="' . $cible . '">' . __("Fin", "s1b") . '</a>';
            } else {
                $lien = '';
            }
            $barre .= $lien;

            return $barre;
        }

    endif;


    if (!function_exists('barre_navigation_FriendsList')) :
        /*         * ***********************************************************************************
         * script affichage nb de mail dans Mailbox
         * ************************************************************************************ */

        function barre_navigation_FriendsList($nb_total, $nb_affichage_par_page, $debut, $nb_liens_dans_la_barre) {
            $barre = '';

            /* $nb_affichage_par_page = 8;
              $nb_liens_dans_la_barre = 5; */

// on recherche l'URL courante munie de ses paramÃ¨tre auxquels on ajoute le paramÃ¨tre 'debut' qui jouera le role du premier Ã©lÃ©ment de notre LIMIT
            if ($_SERVER['QUERY_STRING'] == "") {

// METTRE A JOUR L'URL DE LA PAGE ATTENDUE
                $query = getS1bUrl("MY_FRIENDS") . '?debut=';
            } else {
                $tableau = explode("debut=", $_SERVER['QUERY_STRING']);
                $nb_element = count($tableau);
                if ($nb_element == 1) {
                    $query = getS1bUrl("MY_FRIENDS") . '?' . $_SERVER['QUERY_STRING'] . '&debut=';
                } else {
                    if ($tableau[0] == "") {
                        $query = getS1bUrl("MY_FRIENDS") . '?debut=';
                    } else {
                        $query = getS1bUrl("MY_FRIENDS") . '?' . $tableau[0] . 'debut=';
                    }
                }
            }

            /* on calcul le numÃ©ro de la page active */
            $page_active = floor(($debut / $nb_affichage_par_page) + 1);
            /* on calcul le nombre de pages total que va prendre notre affichage */
            $nb_pages_total = ceil($nb_total / $nb_affichage_par_page);

            /* on calcul le premier numero de la barre qui va s'afficher, ainsi que le dernier ($cpt_deb et $cpt_fin)
              exemple : 2 3 4 5 6 7 8 9 10 11 << $cpt_deb = 2 et $cpt_fin = 11 */
            if ($nb_liens_dans_la_barre % 2 == 0) {
                $cpt_deb1 = $page_active - ($nb_liens_dans_la_barre / 2) + 1;
                $cpt_fin1 = $page_active + ($nb_liens_dans_la_barre / 2);
            } else {
                $cpt_deb1 = $page_active - floor(($nb_liens_dans_la_barre / 2));
                $cpt_fin1 = $page_active + floor(($nb_liens_dans_la_barre / 2));
            }

            if ($cpt_deb1 <= 1) {
                $cpt_deb = 1;
                $cpt_fin = $nb_liens_dans_la_barre;
            } elseif ($cpt_deb1 > 1 && $cpt_fin1 < $nb_pages_total) {
                $cpt_deb = $cpt_deb1;
                $cpt_fin = $cpt_fin1;
            } else {
                $cpt_deb = ($nb_pages_total - $nb_liens_dans_la_barre) + 1;
                $cpt_fin = $nb_pages_total;
            }

            if ($nb_pages_total <= $nb_liens_dans_la_barre) {
                $cpt_deb = 1;
                $cpt_fin = $nb_pages_total;
            }

            /* si le premier numÃ©ro qui s'affiche est diffÃ©rent de 1, on affiche << qui sera un lien vers la premiere page */
            if ($cpt_deb != 1) {
                $cible = $query . (0);
                $lien = '<a href="' . $cible . '">' . __("Début", "s1b") . '</a>&nbsp;';
            } else {
                $lien = '';
            }
            $barre .= $lien;

            /* on affiche tous les liens de notre barre, tout en vÃ©rifiant de ne pas mettre de lien pour la page active */
            for ($cpt = $cpt_deb; $cpt <= $cpt_fin; $cpt++) {
                if ($cpt == $page_active) {
                    if ($cpt == $nb_pages_total) {
                        $barre .= $cpt;
                    } else {
                        $barre .= $cpt . '&nbsp;';
                    }
                } else {
                    if ($cpt == $cpt_fin) {
                        $barre .= "<a href='" . $query . (($cpt - 1) * $nb_affichage_par_page);
                        $barre .= "'>" . $cpt . "</a>";
                    } else {
                        $barre .= "<a href='" . $query . (($cpt - 1) * $nb_affichage_par_page);
                        $barre .= "'>" . $cpt . "</a>&nbsp;";
                    }
                }
            }

            $fin = ($nb_total - ($nb_total % $nb_affichage_par_page));
            if (($nb_total % $nb_affichage_par_page) == 0) {
                $fin = $fin - $nb_affichage_par_page;
            }

            /* si $cpt_fin ne vaut pas la derniÃ¨re page de la barre de navigation, on affiche un >> qui sera un lien vers la derniÃ¨re page de navigation */
            if ($cpt_fin != $nb_pages_total) {
                $cible = $query . $fin;
                $lien = '&nbsp;<a href="' . $cible . '">' . __("Fin", "s1b") . '</a>';
            } else {
                $lien = '';
            }
            $barre .= $lien;

            return $barre;
        }

    endif;


    if (!function_exists('barre_navigation_FriendsSearch')) :
        /*         * ***********************************************************************************
         * script affichage nb de mail dans Mailbox
         * ************************************************************************************ */

        function barre_navigation_FriendsSearch($nb_total, $nb_affichage_par_page, $debut, $nb_liens_dans_la_barre) {
            $barre = '';

            /* $nb_affichage_par_page = 8;
              $nb_liens_dans_la_barre = 5; */

// on recherche l'URL courante munie de ses paramÃ¨tre auxquels on ajoute le paramÃ¨tre 'debut' qui jouera le role du premier Ã©lÃ©ment de notre LIMIT
            if ($_SERVER['QUERY_STRING'] == "") {

// METTRE A JOUR L'URL DE LA PAGE ATTENDUE
                $query = getS1bUrl("FIND_FRIENDS") . '?debut=';
            } else {
                $tableau = explode("debut=", $_SERVER['QUERY_STRING']);
                $nb_element = count($tableau);
                if ($nb_element == 1) {
                    $query = getS1bUrl("FIND_FRIENDS") . '?' . $_SERVER['QUERY_STRING'] . '&debut=';
                } else {
                    if ($tableau[0] == "") {
                        $query = getS1bUrl("FIND_FRIENDS") . '?debut=';
                    } else {
                        $query = getS1bUrl("FIND_FRIENDS") . '?' . $tableau[0] . 'debut=';
                    }
                }
            }

            /* on calcul le numÃ©ro de la page active */
            $page_active = floor(($debut / $nb_affichage_par_page) + 1);
            /* on calcul le nombre de pages total que va prendre notre affichage */
            $nb_pages_total = ceil($nb_total / $nb_affichage_par_page);

            /* on calcul le premier numero de la barre qui va s'afficher, ainsi que le dernier ($cpt_deb et $cpt_fin)
              exemple : 2 3 4 5 6 7 8 9 10 11 << $cpt_deb = 2 et $cpt_fin = 11 */
            if ($nb_liens_dans_la_barre % 2 == 0) {
                $cpt_deb1 = $page_active - ($nb_liens_dans_la_barre / 2) + 1;
                $cpt_fin1 = $page_active + ($nb_liens_dans_la_barre / 2);
            } else {
                $cpt_deb1 = $page_active - floor(($nb_liens_dans_la_barre / 2));
                $cpt_fin1 = $page_active + floor(($nb_liens_dans_la_barre / 2));
            }

            if ($cpt_deb1 <= 1) {
                $cpt_deb = 1;
                $cpt_fin = $nb_liens_dans_la_barre;
            } elseif ($cpt_deb1 > 1 && $cpt_fin1 < $nb_pages_total) {
                $cpt_deb = $cpt_deb1;
                $cpt_fin = $cpt_fin1;
            } else {
                $cpt_deb = ($nb_pages_total - $nb_liens_dans_la_barre) + 1;
                $cpt_fin = $nb_pages_total;
            }

            if ($nb_pages_total <= $nb_liens_dans_la_barre) {
                $cpt_deb = 1;
                $cpt_fin = $nb_pages_total;
            }

            /* si le premier numÃ©ro qui s'affiche est diffÃ©rent de 1, on affiche << qui sera un lien vers la premiere page */
            if ($cpt_deb != 1) {
                $cible = $query . (0);
                $lien = '<a href="' . $cible . '">' . __("Début", "s1b") . '</a>&nbsp;';
            } else {
                $lien = '';
            }
            $barre .= $lien;

            /* on affiche tous les liens de notre barre, tout en vÃ©rifiant de ne pas mettre de lien pour la page active */
            for ($cpt = $cpt_deb; $cpt <= $cpt_fin; $cpt++) {
                if ($cpt == $page_active) {
                    if ($cpt == $nb_pages_total) {
                        $barre .= $cpt;
                    } else {
                        $barre .= $cpt . '&nbsp;';
                    }
                } else {
                    if ($cpt == $cpt_fin) {
                        $barre .= "<a href='" . $query . (($cpt - 1) * $nb_affichage_par_page);
                        $barre .= "'>" . $cpt . "</a>";
                    } else {
                        $barre .= "<a href='" . $query . (($cpt - 1) * $nb_affichage_par_page);
                        $barre .= "'>" . $cpt . "</a>&nbsp;";
                    }
                }
            }

            $fin = ($nb_total - ($nb_total % $nb_affichage_par_page));
            if (($nb_total % $nb_affichage_par_page) == 0) {
                $fin = $fin - $nb_affichage_par_page;
            }

            /* si $cpt_fin ne vaut pas la derniÃ¨re page de la barre de navigation, on affiche un >> qui sera un lien vers la derniÃ¨re page de navigation */
            if ($cpt_fin != $nb_pages_total) {
                $cible = $query . $fin;
                $lien = '&nbsp;<a href="' . $cible . '">' . __("Fin", "s1b") . '</a>';
            } else {
                $lien = '';
            }
            $barre .= $lien;

            return $barre;
        }

    endif;



    if (!function_exists('barre_navigation_FriendsOfFriends')) :
        /*         * ***********************************************************************************
         * script affichage nb de mail dans Mailbox
         * ************************************************************************************ */

        function barre_navigation_FriendsOfFriends($nb_total, $nb_affichage_par_page, $debut, $nb_liens_dans_la_barre) {
            $barre = '';

            /* $nb_affichage_par_page = 8;
              $nb_liens_dans_la_barre = 5; */

// on recherche l'URL courante munie de ses paramÃ¨tre auxquels on ajoute le paramÃ¨tre 'debut' qui jouera le role du premier Ã©lÃ©ment de notre LIMIT
            if ($_SERVER['QUERY_STRING'] == "") {

// METTRE A JOUR L'URL DE LA PAGE ATTENDUE
                $query = getS1bUrl("FRIENDS_OF_FRIENDS") . '?debut=';
            } else {
                $tableau = explode("debut=", $_SERVER['QUERY_STRING']);
                $nb_element = count($tableau);
                if ($nb_element == 1) {
                    $query = getS1bUrl("FRIENDS_OF_FRIENDS") . '?' . $_SERVER['QUERY_STRING'] . '&debut=';
                } else {
                    if ($tableau[0] == "") {
                        $query = getS1bUrl("FRIENDS_OF_FRIENDS") . '?debut=';
                    } else {
                        $query = getS1bUrl("FRIENDS_OF_FRIENDS") . '?' . $tableau[0] . 'debut=';
                    }
                }
            }

            /* on calcul le numÃ©ro de la page active */
            $page_active = floor(($debut / $nb_affichage_par_page) + 1);
            /* on calcul le nombre de pages total que va prendre notre affichage */
            $nb_pages_total = ceil($nb_total / $nb_affichage_par_page);

            /* on calcul le premier numero de la barre qui va s'afficher, ainsi que le dernier ($cpt_deb et $cpt_fin)
              exemple : 2 3 4 5 6 7 8 9 10 11 << $cpt_deb = 2 et $cpt_fin = 11 */
            if ($nb_liens_dans_la_barre % 2 == 0) {
                $cpt_deb1 = $page_active - ($nb_liens_dans_la_barre / 2) + 1;
                $cpt_fin1 = $page_active + ($nb_liens_dans_la_barre / 2);
            } else {
                $cpt_deb1 = $page_active - floor(($nb_liens_dans_la_barre / 2));
                $cpt_fin1 = $page_active + floor(($nb_liens_dans_la_barre / 2));
            }

            if ($cpt_deb1 <= 1) {
                $cpt_deb = 1;
                $cpt_fin = $nb_liens_dans_la_barre;
            } elseif ($cpt_deb1 > 1 && $cpt_fin1 < $nb_pages_total) {
                $cpt_deb = $cpt_deb1;
                $cpt_fin = $cpt_fin1;
            } else {
                $cpt_deb = ($nb_pages_total - $nb_liens_dans_la_barre) + 1;
                $cpt_fin = $nb_pages_total;
            }

            if ($nb_pages_total <= $nb_liens_dans_la_barre) {
                $cpt_deb = 1;
                $cpt_fin = $nb_pages_total;
            }

            /* si le premier numÃ©ro qui s'affiche est diffÃ©rent de 1, on affiche << qui sera un lien vers la premiere page */
            if ($cpt_deb != 1) {
                $cible = $query . (0);
                $lien = '<a href="' . $cible . '">' . __("Début", "s1b") . '</a>&nbsp;';
            } else {
                $lien = '';
            }
            $barre .= $lien;

            /* on affiche tous les liens de notre barre, tout en vÃ©rifiant de ne pas mettre de lien pour la page active */
            for ($cpt = $cpt_deb; $cpt <= $cpt_fin; $cpt++) {
                if ($cpt == $page_active) {
                    if ($cpt == $nb_pages_total) {
                        $barre .= $cpt;
                    } else {
                        $barre .= $cpt . '&nbsp;';
                    }
                } else {
                    if ($cpt == $cpt_fin) {
                        $barre .= "<a href='" . $query . (($cpt - 1) * $nb_affichage_par_page);
                        $barre .= "'>" . $cpt . "</a>";
                    } else {
                        $barre .= "<a href='" . $query . (($cpt - 1) * $nb_affichage_par_page);
                        $barre .= "'>" . $cpt . "</a>&nbsp;";
                    }
                }
            }

            $fin = ($nb_total - ($nb_total % $nb_affichage_par_page));
            if (($nb_total % $nb_affichage_par_page) == 0) {
                $fin = $fin - $nb_affichage_par_page;
            }

            /* si $cpt_fin ne vaut pas la derniÃ¨re page de la barre de navigation, on affiche un >> qui sera un lien vers la derniÃ¨re page de navigation */
            if ($cpt_fin != $nb_pages_total) {
                $cible = $query . $fin;
                $lien = '&nbsp;<a href="' . $cible . '">' . __("Fin", "s1b") . '</a>';
            } else {
                $lien = '';
            }
            $barre .= $lien;

            return $barre;
        }

    endif;



    if (!function_exists('barre_navigation_FriendsOfFriendsList')) :
        /*         * ***********************************************************************************
         * script affichage nb de mail dans Mailbox
         * ************************************************************************************ */

        function barre_navigation_FriendsOfFriendsList($nb_total, $nb_affichage_par_page, $debut, $nb_liens_dans_la_barre) {
            $barre = '';

            /* $nb_affichage_par_page = 8;
              $nb_liens_dans_la_barre = 5; */

// on recherche l'URL courante munie de ses paramÃ¨tre auxquels on ajoute le paramÃ¨tre 'debut' qui jouera le role du premier Ã©lÃ©ment de notre LIMIT
            if ($_SERVER['QUERY_STRING'] == "") {

// METTRE A JOUR L'URL DE LA PAGE ATTENDUE
                $query = getS1bUrl("FRIENDS_OF") . '?debut=';
            } else {
                $tableau = explode("debut=", $_SERVER['QUERY_STRING']);
                $nb_element = count($tableau);
                if ($nb_element == 1) {
                    $query = getS1bUrl("FRIENDS_OF") . '?' . $_SERVER['QUERY_STRING'] . '&debut=';
                } else {
                    if ($tableau[0] == "") {
                        $query = getS1bUrl("FRIENDS_OF") . '?debut=';
                    } else {
                        $query = getS1bUrl("FRIENDS_OF") . '?' . $tableau[0] . 'debut=';
                    }
                }
            }

            /* on calcul le numÃ©ro de la page active */
            $page_active = floor(($debut / $nb_affichage_par_page) + 1);
            /* on calcul le nombre de pages total que va prendre notre affichage */
            $nb_pages_total = ceil($nb_total / $nb_affichage_par_page);

            /* on calcul le premier numero de la barre qui va s'afficher, ainsi que le dernier ($cpt_deb et $cpt_fin)
              exemple : 2 3 4 5 6 7 8 9 10 11 << $cpt_deb = 2 et $cpt_fin = 11 */
            if ($nb_liens_dans_la_barre % 2 == 0) {
                $cpt_deb1 = $page_active - ($nb_liens_dans_la_barre / 2) + 1;
                $cpt_fin1 = $page_active + ($nb_liens_dans_la_barre / 2);
            } else {
                $cpt_deb1 = $page_active - floor(($nb_liens_dans_la_barre / 2));
                $cpt_fin1 = $page_active + floor(($nb_liens_dans_la_barre / 2));
            }

            if ($cpt_deb1 <= 1) {
                $cpt_deb = 1;
                $cpt_fin = $nb_liens_dans_la_barre;
            } elseif ($cpt_deb1 > 1 && $cpt_fin1 < $nb_pages_total) {
                $cpt_deb = $cpt_deb1;
                $cpt_fin = $cpt_fin1;
            } else {
                $cpt_deb = ($nb_pages_total - $nb_liens_dans_la_barre) + 1;
                $cpt_fin = $nb_pages_total;
            }

            if ($nb_pages_total <= $nb_liens_dans_la_barre) {
                $cpt_deb = 1;
                $cpt_fin = $nb_pages_total;
            }

            /* si le premier numÃ©ro qui s'affiche est diffÃ©rent de 1, on affiche << qui sera un lien vers la premiere page */
            if ($cpt_deb != 1) {
                $cible = $query . (0);
                $lien = '<a href="' . $cible . '">' . __("Début", "s1b") . '</a>&nbsp;';
            } else {
                $lien = '';
            }
            $barre .= $lien;

            /* on affiche tous les liens de notre barre, tout en vÃ©rifiant de ne pas mettre de lien pour la page active */
            for ($cpt = $cpt_deb; $cpt <= $cpt_fin; $cpt++) {
                if ($cpt == $page_active) {
                    if ($cpt == $nb_pages_total) {
                        $barre .= $cpt;
                    } else {
                        $barre .= $cpt . '&nbsp;';
                    }
                } else {
                    if ($cpt == $cpt_fin) {
                        $barre .= "<a href='" . $query . (($cpt - 1) * $nb_affichage_par_page);
                        $barre .= "'>" . $cpt . "</a>";
                    } else {
                        $barre .= "<a href='" . $query . (($cpt - 1) * $nb_affichage_par_page);
                        $barre .= "'>" . $cpt . "</a>&nbsp;";
                    }
                }
            }

            $fin = ($nb_total - ($nb_total % $nb_affichage_par_page));
            if (($nb_total % $nb_affichage_par_page) == 0) {
                $fin = $fin - $nb_affichage_par_page;
            }

            /* si $cpt_fin ne vaut pas la derniÃ¨re page de la barre de navigation, on affiche un >> qui sera un lien vers la derniÃ¨re page de navigation */
            if ($cpt_fin != $nb_pages_total) {
                $cible = $query . $fin;
                $lien = '&nbsp;<a href="' . $cible . '">' . __("Fin", "s1b") . '</a>';
            } else {
                $lien = '';
            }
            $barre .= $lien;

            return $barre;
        }

    endif;



    if (!function_exists('barre_navigation_FriendsRequest')) :
        /*         * ***********************************************************************************
         * script affichage nb de mail dans Mailbox
         * ************************************************************************************ */

        function barre_navigation_FriendsRequest($NbTotal, $nb_affichage_par_page, $debut, $nb_liens_dans_la_barre) {
            $barre = '';

            /* $nb_affichage_par_page = 8;
              $nb_liens_dans_la_barre = 5; */

// on recherche l'URL courante munie de ses paramÃ¨tre auxquels on ajoute le paramÃ¨tre 'debut' qui jouera le role du premier Ã©lÃ©ment de notre LIMIT
            if ($_SERVER['QUERY_STRING'] == "") {

// METTRE A JOUR L'URL DE LA PAGE ATTENDUE
                $query = getS1bUrl("FRIEND_PENDING_REQUEST") . '?debut=';
            } else {
                $tableau = explode("debut=", $_SERVER['QUERY_STRING']);
                $nb_element = count($tableau);
                if ($nb_element == 1) {
                    $query = getS1bUrl("FRIEND_PENDING_REQUEST") . '?' . $_SERVER['QUERY_STRING'] . '&debut=';
                } else {
                    if ($tableau[0] == "") {
                        $query = getS1bUrl("FRIEND_PENDING_REQUEST") . '?debut=';
                    } else {
                        $query = getS1bUrl("FRIEND_PENDING_REQUEST") . '?' . $tableau[0] . 'debut=';
                    }
                }
            }

            /* on calcul le numÃ©ro de la page active */
            $page_active = floor(($debut / $nb_affichage_par_page) + 1);
            /* on calcul le nombre de pages total que va prendre notre affichage */
            $nb_pages_total = ceil($NbTotal / $nb_affichage_par_page);

            /* on calcul le premier numero de la barre qui va s'afficher, ainsi que le dernier ($cpt_deb et $cpt_fin)
              exemple : 2 3 4 5 6 7 8 9 10 11 << $cpt_deb = 2 et $cpt_fin = 11 */
            if ($nb_liens_dans_la_barre % 2 == 0) {
                $cpt_deb1 = $page_active - ($nb_liens_dans_la_barre / 2) + 1;
                $cpt_fin1 = $page_active + ($nb_liens_dans_la_barre / 2);
            } else {
                $cpt_deb1 = $page_active - floor(($nb_liens_dans_la_barre / 2));
                $cpt_fin1 = $page_active + floor(($nb_liens_dans_la_barre / 2));
            }

            if ($cpt_deb1 <= 1) {
                $cpt_deb = 1;
                $cpt_fin = $nb_liens_dans_la_barre;
            } elseif ($cpt_deb1 > 1 && $cpt_fin1 < $nb_pages_total) {
                $cpt_deb = $cpt_deb1;
                $cpt_fin = $cpt_fin1;
            } else {
                $cpt_deb = ($nb_pages_total - $nb_liens_dans_la_barre) + 1;
                $cpt_fin = $nb_pages_total;
            }

            if ($nb_pages_total <= $nb_liens_dans_la_barre) {
                $cpt_deb = 1;
                $cpt_fin = $nb_pages_total;
            }

            /* si le premier numÃ©ro qui s'affiche est diffÃ©rent de 1, on affiche << qui sera un lien vers la premiere page */
            if ($cpt_deb != 1) {
                $cible = $query . (0);
                $lien = '<a href="' . $cible . '">' . __("Début", "s1b") . '</a>&nbsp;';
            } else {
                $lien = '';
            }
            $barre .= $lien;

            /* on affiche tous les liens de notre barre, tout en vÃ©rifiant de ne pas mettre de lien pour la page active */
            for ($cpt = $cpt_deb; $cpt <= $cpt_fin; $cpt++) {
                if ($cpt == $page_active) {
                    if ($cpt == $nb_pages_total) {
                        $barre .= $cpt;
                    } else {
                        $barre .= $cpt . '&nbsp;';
                    }
                } else {
                    if ($cpt == $cpt_fin) {
                        $barre .= "<a href='" . $query . (($cpt - 1) * $nb_affichage_par_page);
                        $barre .= "'>" . $cpt . "</a>";
                    } else {
                        $barre .= "<a href='" . $query . (($cpt - 1) * $nb_affichage_par_page);
                        $barre .= "'>" . $cpt . "</a>&nbsp;";
                    }
                }
            }

            $fin = ($NbTotal - ($NbTotal % $nb_affichage_par_page));
            if (($NbTotal % $nb_affichage_par_page) == 0) {
                $fin = $fin - $nb_affichage_par_page;
            }

            /* si $cpt_fin ne vaut pas la derniÃ¨re page de la barre de navigation, on affiche un >> qui sera un lien vers la derniÃ¨re page de navigation */
            if ($cpt_fin != $nb_pages_total) {
                $cible = $query . $fin;
                $lien = '&nbsp;<a href="' . $cible . '">' . __("Fin", "s1b") . '</a>';
            } else {
                $lien = '';
            }
            $barre .= $lien;

            return $barre;
        }

    endif;

    /*     * ********************************************************************************************** */


    add_action('after_setup_theme', 'twentyeleven_setup');

    function my_theme_setup() {
        load_theme_textdomain('s1b', BASE_PATH . 'languages');
    }

    if (!function_exists('tronque')) :
        /*         * ***********************************************************************************
         * script tronquer un texte
         * ************************************************************************************ */

        function tronque($chaine, $longueur) {

            if (empty($chaine)) {
                return "";
            } elseif (strlen($chaine) < $longueur) {
                return $chaine;
            } elseif (preg_match("/(.{1,$longueur})\s./ms", $chaine, $match)) {
                return $match [1] . "...";
            } else {
                return substr($chaine, 0, $longueur) . "...";
            }
        }

    endif;


    if (!function_exists('buildLangUrl')) :
        /*         * ***********************************************************************************
         * script tronquer un texte
         * ************************************************************************************ */

        function buildLangUrl($lang) {
            $protocol = ($_SERVER['SERVER_PROTOCOL'] == "HTTP/1.1" ? "http://" : "https://");
            $host = $_SERVER['HTTP_HOST'];
            $uri = $_SERVER['REQUEST_URI'];
            $qs = $_SERVER['QUERY_STRING'];
            if ($qs === "") {
                $page = $uri . "?lang=$lang";
            } else {
                $page = $uri . "&lang=$lang";
            }
            $url = $protocol . $host . $page;
            return $url;
        }

    endif;

    if (!function_exists('getS1bUrl')) {

        function getS1bUrl($urlKey) {
            global $url;
            if ($url) {
                if (array_key_exists($urlKey, $url)) {
                    $retUrl = sprintf("%s/%s", get_option('home'), $url[$urlKey]);
                    return $retUrl;
                }
            }
        }

    }

    if (!function_exists("showFlashes")) {

        function showFlashes() {

            $flashes = null;
            // Récupération des messages flashes éventuels
            if (\Sb\Flash\Flash::hasItems()) {
                $flashes = \Sb\Flash\Flash::getItems();
                \Sb\Trace\Trace::addItem("Récupération des messages flashes");
            }

            $ret = "";
            if ($flashes) {
                $ret .= "<div id=\"flashes-wrap\"><div id=\"flashes-background\"></div><div id='flashes'><div id='flashes-close-button'></div><ul>";
                foreach ($flashes as $flash) {
                    $ret .= "<li>" . $flash . "</li>";
                }
                $ret .= "</ul></div></div>";
            }
            echo $ret;
        }

    }

    if (!function_exists("loginSucces")) {

        function loginSucces(\Sb\Db\Model\User $activeUser) {
            $connectionSuccess = __("Connexion réussie", "");
            // Initialisation des infos de connexion dans la session
            initAuthenticatedUser($activeUser);
            // Redirection vers la page d'accueil
            //\Sb\Flash\Flash::addItem($connectionSuccess);
            \Sb\Trace\Trace::addItem($connectionSuccess);
            wp_redirect(getS1bUrl('PRIVATE_HOMEPAGE'));
        }

    }
    if (!function_exists("initAuthenticatedUser")) {

        function initAuthenticatedUser(\Sb\Db\Model\User $activeUser) {
            $_SESSION['Auth'] = array(
                'Email' => $activeUser->getEmail(),
                'Password' => $activeUser->getPassword(),
                'Id' => $activeUser->getId());
            if ($activeUser->getFacebookId())
                $_SESSION['Auth']['FacebookId'] = $activeUser->getFacebookId();
        }

    }

    if (!function_exists('buildSortUrl')) :
        /*         * ***********************************************************************************
         * script tronquer un texte
         * ************************************************************************************ */

        function buildSortUrl($sort) {
            $protocol = ($_SERVER['SERVER_PROTOCOL'] == "HTTP/1.1" ? "http://" : "https://");
            $host = $_SERVER['HTTP_HOST'];
            $uri = $_SERVER['REQUEST_URI'];
            $qs = $_SERVER['QUERY_STRING'];
            if ($qs === "") {
                $page = $uri . "?sortby=$sort";
            } else {
                $page = $uri . "&sortby=$sort";
            }
            $url = $protocol . $host . $page;
            return $url;
        }

    endif;


    if (!function_exists('buildRequestUrl')) :
        /*         * ***********************************************************************************
         * script
         * ************************************************************************************ */

        function buildRequestUrl($FriendRequest) {
            $protocol = ($_SERVER['SERVER_PROTOCOL'] == "HTTP/1.1" ? "http://" : "https://");
            $host = $_SERVER['HTTP_HOST'];
            $uri = $_SERVER['REQUEST_URI'];
            $url = $protocol . $host . $uri;
            return $url;
        }

    endif;

    
    
    /**
     * Add an option for a particular blog.
     *
     * @since MU
     *
     * @param int $id The blog id
     * @param string $key The option key
     * @param mixed $value The option value
     * @return bool True on success, false on failure.
     */
    function add_blog_option($id, $key, $value) {
        $id = (int) $id;

        switch_to_blog($id);
        $return = add_option($key, $value);
        restore_current_blog();
        if ($return)
            wp_cache_set($id . '-' . $key . '-blog_option', $value, 'site-options');
        return $return;
    }

    /**
     * Delete an option for a particular blog.
     *
     * @since MU
     *
     * @param int $id The blog id
     * @param string $key The option key
     * @return bool True on success, false on failure.
     */
    function delete_blog_option($id, $key) {
        $id = (int) $id;

        switch_to_blog($id);
        $return = delete_option($key);
        restore_current_blog();
        if ($return)
            wp_cache_set($id . '-' . $key . '-blog_option', '', 'site-options');
        return $return;
    }

    /**
     * Update an option for a particular blog.
     *
     * @since MU
     *
     * @param int $id The blog id
     * @param string $key The option key
     * @param mixed $value The option value
     * @return bool True on success, false on failrue.
     */
    function update_blog_option($id, $key, $value, $deprecated = null) {
        $id = (int) $id;

        if (null !== $deprecated)
            _deprecated_argument(__FUNCTION__, '3.1');

        switch_to_blog($id);
        $return = update_option($key, $value);
        restore_current_blog();

        refresh_blog_details($id);

        if ($return)
            wp_cache_set($id . '-' . $key . '-blog_option', $value, 'site-options');
        return $return;
    }

    /**
     * Switch the current blog.
     *
     * This function is useful if you need to pull posts, or other information,
     * from other blogs. You can switch back afterwards using restore_current_blog().
     *
     * Things that aren't switched:
     *  - autoloaded options. See #14992
     *  - plugins. See #14941
     *
     * @see restore_current_blog()
     * @since MU
     *
     * @param int $new_blog The id of the blog you want to switch to. Default: current blog
     * @param bool $validate Whether to check if $new_blog exists before proceeding
     * @return bool True on success, False if the validation failed
     */
    function switch_to_blog($new_blog, $validate = false) {
        global $wpdb, $table_prefix, $blog_id, $switched, $switched_stack, $wp_roles, $wp_object_cache;

        if (empty($new_blog))
            $new_blog = $blog_id;

        if ($validate && !get_blog_details($new_blog))
            return false;

        if (empty($switched_stack))
            $switched_stack = array();

        $switched_stack[] = $blog_id;

        /* If we're switching to the same blog id that we're on,
         * set the right vars, do the associated actions, but skip
         * the extra unnecessary work */
        if ($blog_id == $new_blog) {
            do_action('switch_blog', $blog_id, $blog_id);
            $switched = true;
            return true;
        }

        $wpdb->set_blog_id($new_blog);
        $table_prefix = $wpdb->prefix;
        $prev_blog_id = $blog_id;
        $blog_id = $new_blog;

        if (is_object($wp_roles)) {
            $wpdb->suppress_errors();
            if (method_exists($wp_roles, '_init'))
                $wp_roles->_init();
            elseif (method_exists($wp_roles, '__construct'))
                $wp_roles->__construct();
            $wpdb->suppress_errors(false);
        }

        if (did_action('init')) {
            $current_user = wp_get_current_user();
            if (is_object($current_user))
                $current_user->for_blog($blog_id);
        }

        if (is_object($wp_object_cache) && isset($wp_object_cache->global_groups))
            $global_groups = $wp_object_cache->global_groups;
        else
            $global_groups = false;

        wp_cache_init();
        if (function_exists('wp_cache_add_global_groups')) {
            if (is_array($global_groups))
                wp_cache_add_global_groups($global_groups);
            else
                wp_cache_add_global_groups(array('users', 'userlogins', 'usermeta', 'user_meta', 'site-transient', 'site-options', 'site-lookup', 'blog-lookup', 'blog-details', 'rss', 'global-posts'));
            wp_cache_add_non_persistent_groups(array('comment', 'counts', 'plugins'));
        }

        do_action('switch_blog', $blog_id, $prev_blog_id);
        $switched = true;
        return true;
    }

    /**
     * Restore the current blog, after calling switch_to_blog()
     *
     * @see switch_to_blog()
     * @since MU
     *
     * @return bool True on success, False if we're already on the current blog
     */
    function restore_current_blog() {
        global $table_prefix, $wpdb, $blog_id, $switched, $switched_stack, $wp_roles, $wp_object_cache;

        if (!$switched)
            return false;

        if (!is_array($switched_stack))
            return false;

        $blog = array_pop($switched_stack);
        if ($blog_id == $blog) {
            do_action('switch_blog', $blog, $blog);
            /* If we still have items in the switched stack, consider ourselves still 'switched' */
            $switched = ( is_array($switched_stack) && count($switched_stack) > 0 );
            return true;
        }

        $wpdb->set_blog_id($blog);
        $prev_blog_id = $blog_id;
        $blog_id = $blog;
        $table_prefix = $wpdb->prefix;

        if (is_object($wp_roles)) {
            $wpdb->suppress_errors();
            if (method_exists($wp_roles, '_init'))
                $wp_roles->_init();
            elseif (method_exists($wp_roles, '__construct'))
                $wp_roles->__construct();
            $wpdb->suppress_errors(false);
        }

        if (did_action('init')) {
            $current_user = wp_get_current_user();
            if (is_object($current_user))
                $current_user->for_blog($blog_id);
        }

        if (is_object($wp_object_cache) && isset($wp_object_cache->global_groups))
            $global_groups = $wp_object_cache->global_groups;
        else
            $global_groups = false;

        wp_cache_init();
        if (function_exists('wp_cache_add_global_groups')) {
            if (is_array($global_groups))
                wp_cache_add_global_groups($global_groups);
            else
                wp_cache_add_global_groups(array('users', 'userlogins', 'usermeta', 'user_meta', 'site-transient', 'site-options', 'site-lookup', 'blog-lookup', 'blog-details', 'rss', 'global-posts'));
            wp_cache_add_non_persistent_groups(array('comment', 'counts', 'plugins'));
        }

        do_action('switch_blog', $blog_id, $prev_blog_id);

        /* If we still have items in the switched stack, consider ourselves still 'switched' */
        $switched = ( is_array($switched_stack) && count($switched_stack) > 0 );
        return true;
    }