<?php
require_once 'includes/init.php';
get_header();
require_once 'user_friends_pending_request_1.php';
/**
 * Template Name: user_friends_pending_request
 */
?>
<?php
$userNavigation = new \Sb\View\Components\UserNavigation;
echo $userNavigation->get();
?>
<?php showFlashes(); ?>
<div id="content-wrap" class="add-friend-bkg">
    <div id="content-center">
        <div class="pending-request-header">
            <div class="adh-title"><?php _e("Demande(s) d'ami","s1b");?></div>
            <div class="adh-subtitle"><?php echo sprintf(__("%s demande(s) en attente","s1b"), count($totalPendingRequests));?></div>
        </div>                
        <?php
        if (count($pendingRequests) == 0) { ?>
            <div class="message_info">
                <span class="message_info_arrondi"><?php echo __("Vous n'avez aucune requête à valider", "s1b"); ?></span>
            </div>
        <?php } else { ?>
            <div class="navigation">
                <div class="inner-padding">
                    <div class="nav-links">
                        <?php echo $navigation;?>    
                    </div>
                    <div class="nav-position"><?php echo sprintf(__("Demande d'ami(s) %s à %s sur %s","s1b"), $firstItemIdx, $lastItemIdx, $nbItemsTot) ;?></div>
                </div>
            </div>
            <div class="friends-list">
            <?php foreach ($pendingRequests as $pendingRequest) {
                $userRequested = $pendingRequest->getUser_source();
                $userProfileLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::FRIEND_PROFILE, array("fid" => $userRequested->getId())); ?>
                
                <div class="friend-item">                
                    <div class="inner-padding">
                        <a href="<?php echo $userProfileLink; ?>">
                            <?php
                            $avatar = $userRequested->getGravatar();
                            if ($avatar == "")
                                $avatar = $context->getBaseUrl() . "/Resources/images/avatars/noavatar.png";
                            ?>
                            <img class="image-frame image-thumb-square" src="<?php echo $avatar;?>" />
                        </a>
                        <div class="fi-line margin-top-l">
                            <span class="fil-username">
                                <?php echo \Sb\Helpers\StringHelper::tronque($userRequested->getFirstName(), 20) . " " . mb_substr($userRequested->getLastName(), 0, 1) . ".";?>
                            </span>
                        </div>                        
                        <div class="fi-line">
                            <?php _e("vous demande de l'ajouter à vos amis","s1b"); ?>
                        </div>                        
                        <div class="fi-line">
                            <span class="fil-label">
                                <?php echo __("Demande reçue : ", "s1b");?>
                            </span>
                            <?php echo $pendingRequest->getCreationDate()->format('d/m/Y'); ?>
                        </div>                        
                        <div class="fi-line margin-top-l">
                            <form action="" name="validation" method="post" class="float-left">
                                <input type="hidden" name="friendShipId" value="<?php echo $pendingRequest->getId(); ?>"/>
                                <input type="hidden" name="Title" value="<?php echo __("Demande d'ami", "s1b"); ?>">
                                <input type="hidden" name="Message" value="<?php echo $user->getUserName() . " " . __("a accepté votre demande d'ami", "s1b"); ?>"/>
                                <input type="hidden" name="Refused" value="0">
                                <button class="float-left button bt-black-s margin-right"><?php echo __("Accepter", "s1b"); ?></button>
                            </form>
                            <form action="" name="refused" method="post" class="float-left">
                                <input type="hidden" name="friendShipId" value="<?php echo $pendingRequest->getId(); ?>"/>
                                <input type="hidden" name="Title" value="<?php echo __("Votre demande d'ami a été refusée", "s1b"); ?>">
                                <input type="hidden" name="Message" value="<?php echo __("Votre demande d'ami a été ignorée par", "s1b") . " " . $user->getUserName(); ?>"/>
                                <input type="hidden" name="Refused" value="1">
                                <button class="float-left button bt-blue-s"><?php echo __("Refuser", "s1b"); ?></button>                                
                            </form>
                        </div>
                    </div>
                </div>
                
            <?php } ?>
            </div>
            <div class="navigation">
                <div class="inner-padding">
                    <div class="nav-links">
                        <?php echo $navigation;?>    
                    </div>
                </div>
            </div>
            <?php } ?>
    </div>
    <div id="content-right">
       <div class="right-frame">
        <?php
            $userToolBox = new \Sb\View\Components\UserToolBox;
            echo $userToolBox->get();
        ?>
        </div>
        <div class="right-frame">
        <?php
            $ad = new \Sb\View\Components\Ad("user_friends_pending_request", "1961774989");
            echo $ad->get();
        ?>
        </div>
    </div>
<?php get_footer(); ?>