<!DOCTYPE html>
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
global $context;
global $config;
global $page;
// test commit develop
?>
<html xmlns:fb="http://ogp.me/ns/fb#">
    <head>
        <?php if ($config->getIsProduction()) {?>
        <!-- PRODUCTION -->
        <?php }?>
        <link rel="icon" type="image/png" href="<?php echo $context->getBaseUrl() . "Resources/images/favicons/favicon.ico";?>" />
        <meta charset="UTF-8" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/><!--HTML4-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><!--XHTML 1.1-->
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
        <meta name="description" content="<?php bloginfo('description'); ?>" />
        <meta name="verification" content="90976ef46595e2d7ff7ce4419ff6dc05" />
        <title>
            <?php
            $bookId = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, "bid", null);
            if ($bookId) {
                $book = \Sb\Db\Dao\BookDao::getInstance()->get($bookId);                
            } 
            if ($book)
                echo $book->getTitle() . " - " . $book->getOrderableContributors();
            else {
                wp_title('|', true, 'right');
                // Add the blog name.
                bloginfo('name');
                // Add the blog description for the home/front page.
                $site_description = get_bloginfo('description', 'display');
                if ($site_description && ( is_home() || is_front_page() ))
                    echo " | $site_description";                
            }
            ?>
        </title>
        <script type="text/javascript" src="<?php echo $context->getBaseUrl() . "Resources/js/jquery-1.7.1.min.js"; ?>"></script>
        <?php
        /* Always have wp_head() just before the closing </head>
         * tag of your theme, or you will break many plugins, which
         * generally use this hook to add elements to <head> such
         * as styles, scripts, and meta tags.
         */
        wp_head();
        ?>
        <link type="text/css" media="screen" rel="stylesheet" href="<?php echo $context->getBaseUrl() . "Resources/css/share1book.css?v=9"; ?>"  />
        <link type="text/css" media="screen" rel="stylesheet" href="<?php echo $context->getBaseUrl() . "Resources/css/overcast/jquery-ui-1.8.18.custom.css"; ?>"  />

        <link rel="shortcut icon" type="image/x-icon" href="<?php echo $context->getBaseUrl() . "Resources/images/favicons/favicon.ico"; ?>" />

        <script type="text/javascript" src="<?php echo $context->getBaseUrl() . "Resources/js/jquery-ui-1.8.18.custom.min.js"; ?>"></script>
        <script type="text/javascript" src="<?php echo $context->getBaseUrl() . "Resources/js/jquery.ui.datepicker-fr.js"; ?>"></script>
        <script type="text/javascript" src="<?php echo $context->getBaseUrl() . "Resources/js/addBook.js?v=4"; ?>"></script>
        <script type="text/javascript" src="<?php echo $context->getBaseUrl() . "Resources/js/bookList.js"; ?>"></script>
        <script type="text/javascript" src="<?php echo $context->getBaseUrl() . "Resources/js/pushedBooks.js"; ?>"></script>
        <script type="text/javascript" src="<?php echo $context->getBaseUrl() . "Resources/js/facebook.js"; ?>"></script>
        <script type="text/javascript" src="<?php echo $context->getBaseUrl() . "Resources/js/simple.carousel.js"; ?>"></script>
        <?php if ($config->getIsProduction()) {?>
        <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-34691855-1']);
        _gaq.push(['_trackPageview']);
        _gaq.push(['_trackPageLoadTime']);

        (function() {
          var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
          ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
          var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
        </script>
        <?php } ?>
    </head>
    <body>
        <div id="loading"><div id="loadingMsg"><?php _e("Chargement en cours...", "s1b") ?></div></div>
        <div id="page">
            <div id="header">
                <div id="header-top">
                    <div class="header-inner">
                        <a href="<?php echo \Sb\Helpers\HTTPHelper::Link(""); ?>"><img border="0" src="<?php echo $context->getBaseUrl() . "Resources/images/logo.png"; ?>" /></a>
                    </div>
                </div>
                <div id="header-bottom">
                    <div class="header-inner">

                        <div id="nav-main">
                            <?php
                            $searchForm = new \Sb\View\Components\SearchForm;
                            echo $searchForm->get();
                            if (!$context->getConnectedUser()) {
                                $loginForm = new \Sb\View\Components\LoginForm;
                                echo $loginForm->get();
                            ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div id="content">