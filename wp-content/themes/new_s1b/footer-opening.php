<?php
use \Sb\Helpers\HTTPHelper;
use \Sb\Entity\Urls;
use \Sb\Db\Service\BookSvc;
?>

                    <!-- Début div footer-wrap -->
                    <div id="footer-wrap">
                        <div id="footer-top">
                                    
                            <div class="ft-item">
                                <div class="inner-padding-12">
                                    <h2 class="ft-item-title"><a class="link" href="<?php echo HTTPHelper::Link(Urls::TOPS_BOOKS);?>"><?php _e("<strong>Top</strong> des livres", "s1b");?></a></h2>
                                    <div class="ft-item-content">                                       
                                        <?php
                                        
                                        $topsBooks = BookSvc::getInstance()->getTopsFooter();
                                        
                                        echo "<ul>";
                                        foreach ($topsBooks as $topsBook) {                                            
                                            echo "<li><h3><a title=\"" . $topsBook->getTitle() . " - ". $topsBook->getOrderableContributors() . "\" href=\"" . HTTPHelper::Link($topsBook->getLink()) . "\">" 
                                                    . \Sb\Helpers\StringHelper::tronque($topsBook->getTitle(), 40) 
                                                    . "</a></h3></li>";
                                        }
                                        echo "</ul>";
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="ft-item">
                                <div class="inner-padding-12">
                                    <h2 class="ft-item-title"><a class="link" href="<?php echo HTTPHelper::Link(Urls::BLOW_OF_HEARTS_BOOKS);?>"><strong><?php _e("Coups de coeur", "s1b");?></strong></a></h2>
                                    <div class="ft-item-content">                                        
                                        <?php
                                        $bohs = BookSvc::getInstance()->getBOHForFooter();
                                        echo "<ul>";
                                        foreach ($bohs as $boh) {
                                            echo "<li><h3><a title=\"" . $boh->getTitle() . " - ". $boh->getOrderableContributors() . "\" href=\"" . HTTPHelper::Link($boh->getLink()) . "\">" 
                                                    . \Sb\Helpers\StringHelper::tronque($boh->getTitle(), 40) 
                                                    . "</a></h3></li>";
                                        }
                                        echo "</ul>";
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="ft-item">
                                <div class="inner-padding-12">
                                    <h2 class="ft-item-title"><a class="link" href="<?php echo HTTPHelper::Link(Urls::LAST_ADDED_BOOKS);?>"><strong><?php _e("Derniers livres ajoutés", "s1b");?></strong></a></h2>
                                    <div class="ft-item-content">                                        
                                        <?php
                                        $lastlyAddedBooks = BookSvc::getInstance()->getLastlyAddedForFooter();
                                        echo "<ul>";
                                        foreach ($lastlyAddedBooks as $lastlyAddedBook) {
                                            echo "<li><h3><a title=\"" . $lastlyAddedBook->getTitle() . " - ". $lastlyAddedBook->getOrderableContributors() . "\" href=\"" . HTTPHelper::Link($lastlyAddedBook->getLink()) . "\">" 
                                                    . \Sb\Helpers\StringHelper::tronque($lastlyAddedBook->getTitle(), 40) 
                                                    . "</a></h3></li>";
                                        }
                                        echo "</ul>";
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="ft-item last">
                                <div class="inner-padding-12">
                                    <h2 class="ft-item-title"><?php echo sprintf(__("<strong>%s</strong> sur les réseaux", "s1b"), \Sb\Entity\Constants::SITENAME);?></h2>
                                    <div class="ft-item-content"><a target="_blank" href="http://www.facebook.com/CherBouquin" class="picto-facebook-m"></a></div>
                                    <div class="ft-item-content"><a target="_blank" href="https://twitter.com/#!/cherbouquin" class="ft-twitter"></a></div>
                                    <div class="ft-item-content"><a target="_blank" href="http://pinterest.com/cherbouquin" class="ft-pinterest"></a></div>
                                </div>
                            </div>
                        </div>
                        <div id="footer-bottom">
                            <div class="inner-padding">
                                <div class="fb-left float-left">&copy; 2013 <?php echo \Sb\Entity\Constants::SITENAME;?></div>
                                <div class="fb-right float-right">
                                    <ul>
                                        <li>
                                            <a class="link" href="<?php echo HTTPHelper::Link(Urls::ABOUT);?>"><?php _e("A propos", "s1b");?></a><br/>
                                            <a class="link" href="<?php echo HTTPHelper::Link(Urls::TEAM);?>"><?php _e("L'équipe", "s1b");?></a><br/>
                                            <a class="link" href="<?php echo HTTPHelper::Link(Urls::CONTACT);?>"><?php _e("Contact", "s1b");?></a>
                                        </li>                                    
                                        <li>
                                            <a class="link" href="<?php echo HTTPHelper::Link(Urls::PRESS);?>"><?php _e("Presse", "s1b");?></a><br/>
                                            <a class="link" href="<?php echo HTTPHelper::Link(Urls::NEWSLETTERS);?>"><?php _e("Newsletters", "s1b");?></a><br/>
                                            <a class="link" href="<?php echo HTTPHelper::Link(Urls::PRESS_REVIEW);?>"><?php _e("Revues de presse", "s1b");?></a>
                                        </li>
                                        <li>
                                            <a class="link" href="<?php echo HTTPHelper::Link(Urls::HOW_TO);?>"><?php _e("Comment ça marche?", "s1b");?></a>
                                        </li>
                                        <li>
                                            <a class="link" href="<?php echo HTTPHelper::Link(Urls::HELP_US);?>"><?php _e("Nous aider", "s1b");?></a>
                                        </li>
                                        <li>
                                            <a class="link" target="_blank" href="<?php echo HTTPHelper::Link("CGU/CGU-26-09-2012.pdf");?>"><?php _e("Conditions générales d'utilisations", "s1b");?></a>
                                        </li>                                        
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Fin div footer-wrap -->
                </div>
                <!-- Fin div content-wrap -->    
                <?php \Sb\Flash\Flash::showFlashes(); ?>
            </div>
            <!-- Fin div content -->            
        </div>
        
        