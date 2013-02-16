<?php
global $globalContext;
global $globalConfig;

$facebookJs = 'http://connect.facebook.net/fr_FR/all.js#xfbml=1&appId=' . $globalConfig->getFacebookApiId();
$facebookInviteText = __("Rejoignez vos amis, suivez les livres que vous leurs prêtez et partagez avec eux vos dernières lectures et envies", "s1b");
$ajaxUrl = str_replace("index.php", "", str_replace("public/index.php", "", $_SERVER['SCRIPT_NAME']));
?>
<script type='text/javascript'>
    var share1BookAjax = {
        "url" : "<?php echo $ajaxUrl; ?>",
        "facebookJs" : "<?php echo $facebookJs; ?>",
        "facebookInviteText" : "<?php echo $facebookInviteText; ?>"
    };
</script>

<script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/jquery/jquery-ui-1.8.18.custom.min.js" ></script>
<script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/jquery/jquery.ui.datepicker-fr.js" ></script>

<script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/ajax.js" ></script>
<script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/book.js?v=1" ></script>
<script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/library.js" ></script>                    
<script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/pushedBooks.js" ></script>
<script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/searchResult.js" ></script>

<script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/facebook.js" ></script>        


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
    