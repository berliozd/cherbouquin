<?php
global $globalConfig;
global $globalContext;

$facebookJs = 'http://connect.facebook.net/fr_FR/all.js#xfbml=1&appId=' . $globalConfig->getFacebookApiId();
$facebookInviteText = __("Rejoignez vos amis, suivez les livres que vous leurs prêtez et partagez avec eux vos dernières lectures et envies", "s1b");
$ajaxUrl = str_replace("index.php", "", str_replace("public/index.php", "", $_SERVER['SCRIPT_NAME']));
?>
<script type='text/javascript'>
var share1BookAjax = {
    "url" : "<?php echo $ajaxUrl;?>",
    "facebookJs" : "<?php echo $facebookJs;?>",
    "facebookInviteText" : "<?php echo $facebookInviteText;?>"
};
</script>

<link type="text/css" media="screen" rel="stylesheet" href="<?php echo $globalContext->getBaseUrl(); ?>Resources/css/share1book.css?v=23"  />
<link type="text/css" media="screen" rel="stylesheet" href="<?php echo $globalContext->getBaseUrl(); ?>Resources/css/overcast/jquery-ui-1.8.18.custom.css"  />

<script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/ajax.js" ></script>
<script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/jquery-ui-1.8.18.custom.min.js" ></script>
<script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/jquery.ui.datepicker-fr.js" ></script>
<script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/addBook.js" ></script>
<script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/pushedBooks.js" ></script>                    
<script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/facebook.js" ></script>        
<script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/tiny_mce/tiny_mce.js"></script>

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

<?php
if ($globalConfig->getIsProduction()) { ?>
    <!-- PRODUCTION -->
<?php } ?>
            
<link rel="icon" type="image/png" href="<?php echo $globalContext->getBaseUrl() . "Resources/images/favicons/favicon.ico"; ?>" />
<link rel="shortcut icon" type="image/x-icon" href="<?php echo $globalContext->getBaseUrl(); ?>Resources/images/favicons/favicon.ico" />

<meta charset="UTF-8" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/><!--HTML4-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><!--XHTML 1.1-->
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta name="verification" content="90976ef46595e2d7ff7ce4419ff6dc05" />
<meta name="msvalidate.01" content="E62B545AC4DE99DA99D381175475444F" />
<meta name="norton-safeweb-site-verification" content="jh6la0lgxqne8t-qrkb1vtxf9tqfdhqepiff6e6qkbcv6951uvuam70bh7fp2e371h08q687xlh7v1641xjypiwmqs03aaii5sh9gwbns3hptuzhdp2mk5d-hkj4jwz7" />