<!DOCTYPE html>
<?php
/**
 * The Header for our theme.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>
<html xmlns:fb="http://ogp.me/ns/fb#">
    <head>        
        <?php
        global $globalConfig;
        global $globalContext;
        if ($globalConfig->getIsProduction()) { ?>
            <!-- PRODUCTION -->
        <?php } ?>
        <link rel="icon" type="image/png" href="<?php echo $globalContext->getBaseUrl() . "Resources/images/favicons/favicon.ico"; ?>" />
        <meta charset="UTF-8" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/><!--HTML4-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><!--XHTML 1.1-->
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
        <meta name="verification" content="90976ef46595e2d7ff7ce4419ff6dc05" />
        <title>
            <?php
            $bookId = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, "bid", null);
            $book = null;
            if ($bookId)
                $book = \Sb\Db\Dao\BookDao::getInstance()->get($bookId);

            if ($book)
                echo $book->getTitle() . " - " . $book->getOrderableContributors();
            ?>
        </title>
        <?php        
        $facebookJs = 'http://connect.facebook.net/fr_FR/all.js#xfbml=1&appId=' . $globalConfig->getFacebookApiId();
        $facebookInviteText = __("Rejoignez vos amis, suivez les livres que vous leurs prêtez et partagez avec eux vos dernières lectures et envies", "s1b");
        $ajaxUrl = str_replace("index.php", "", str_replace("public/index.php", "", $_SERVER['SCRIPT_NAME'])) . "wp-admin/admin-ajax.php";
        ?>
        <script type='text/javascript'>
        var share1BookAjax = {
            "url" : "<?php echo $ajaxUrl;?>",
            "facebookJs" : "<?php echo $facebookJs;?>",
            "facebookInviteText" : "<?php echo $facebookInviteText;?>"
        };
        </script>        
        
        <link type="text/css" media="screen" rel="stylesheet" href="<?php echo $globalContext->getBaseUrl(); ?>Resources/css/share1book.css?v=20"  />
        <link type="text/css" media="screen" rel="stylesheet" href="<?php echo $globalContext->getBaseUrl(); ?>Resources/css/overcast/jquery-ui-1.8.18.custom.css"  />
        
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo $globalContext->getBaseUrl(); ?>Resources/images/favicons/favicon.ico" />
        
        <script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/ajax.js" ></script>
        <script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/jquery-ui-1.8.18.custom.min.js" ></script>
        <script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/jquery.ui.datepicker-fr.js" ></script>
        <script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/addBook.js" ></script>
        <script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/bookList.js" ></script>
        <script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/pushedBooks.js" ></script>                    
        <script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/facebook.js" ></script>
        <script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/simple.carousel.js" ></script>
        
        <?php if ($globalConfig->getIsProduction()) { ?>
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
        <div id="loading"><div id="loadingMsg"><?php _e("Chargement en cours...", "s1b");?></div></div>
        <!-- Debut div page -->
        <div id="page">
            <!-- Debut div header -->
            <div id="header">
                <div id="header-top">
                    <div class="header-inner">                        
                        <a href="<?php echo \Sb\Helpers\HTTPHelper::Link(""); ?>">
                            <img border="0" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/images/logo.png"  />
                        </a>
                    </div>
                </div>
                <div id="header-bottom">
                    <div class="header-inner">
                        <div id="nav-main">
                            <?php
                            $searchForm = new \Sb\View\Components\SearchForm;
                            echo $searchForm->get();

                            if (!$globalContext->getConnectedUser()) {
                                $loginForm = new \Sb\View\Components\LoginForm;
                                echo $loginForm->get();
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Debut div content -->
            <div id="content">
                <?php
                if ($globalContext->getConnectedUser()) {
                    $userNavigation = new \Sb\View\Components\UserNavigation;
                    echo $userNavigation->get();
                }                
                ?>
                <?php \Sb\Flash\Flash::showFlashes(); ?>
                <!-- Debut div content-wrap -->
                <div id="content-wrap">
