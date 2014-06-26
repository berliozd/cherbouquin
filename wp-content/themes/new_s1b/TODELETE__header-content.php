<?php
global $globalConfig;
global $globalContext;
?>
<link type="text/css" media="screen" rel="stylesheet" href="<?php echo $globalContext->getBaseUrl(); ?>Resources/css/share1book<?php echo(IS_PRODUCTION ? "-min": "");?>.css?v=<?php echo VERSION;?>"  />
<link type="text/css" media="screen" rel="stylesheet" href="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/jquery/overcast/jquery-ui-1.8.18.custom.css"  />

<?php
if ($globalConfig->getIsProduction()) { ?>
    <!-- PRODUCTION -->
<?php } ?>

<link rel="icon" type="image/png" href="<?php echo $globalContext->getBaseUrl() . "Resources/images/favicons/favicon.ico"; ?>" />
<link rel="shortcut icon" type="image/x-icon" href="<?php echo $globalContext->getBaseUrl(); ?>Resources/images/favicons/favicon.ico" />

<script type="text/javascript" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/js/jquery/jquery-1.7.1.min.js"></script>

<meta charset="UTF-8" />
<meta name="language" content="fr-FR" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/><!--HTML4-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><!--XHTML 1.1-->
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta http-equiv="content-language" content="ll-cc">
<meta name="verification" content="90976ef46595e2d7ff7ce4419ff6dc05" />
<meta name="msvalidate.01" content="E62B545AC4DE99DA99D381175475444F" />
<meta name="norton-safeweb-site-verification" content="jh6la0lgxqne8t-qrkb1vtxf9tqfdhqepiff6e6qkbcv6951uvuam70bh7fp2e371h08q687xlh7v1641xjypiwmqs03aaii5sh9gwbns3hptuzhdp2mk5d-hkj4jwz7" />