<?php
$noAuthentification = true;
require_once 'includes/init.php';
get_header();

/**
 * Template Name: user_how_to
 */
?>
<div id="content-wide">
    <div class="annexe-page">
        <div class="ap-left">
            <div class="inner-padding-10">
                <div class="ap-title"><?php _e("Comment &ccedil;a marche ?", "s1b"); ?></div>
                <div class="tm-librarylink" style="font-size:1.4em"><a href="#preambule" class="link"><?php _e("Pr&eacute;ambule", "s1b"); ?></a></div>
                <div class="tm-librarylink" style="font-size:1.4em"><a href="#Cherbouquin" class="link"><?php _e("Quelles sont les fonctionnalit&eacute;s ?", "s1b"); ?></a></div>
                <div class="tm-librarylink" style="font-size:1.4em"><a href="#inscription" class="link"><?php _e("S'inscrire", "s1b"); ?></a></div>
                <div class="tm-librarylink" style="font-size:1.4em"><a href="#environnement" class="link"><?php _e("L'environnement", "s1b"); ?></a></div>
                <div class="tm-librarylink" style="font-size:1.4em"><a href="#premiers_pas" class="link"><?php _e("Vos premiers pas", "s1b"); ?></a></div>
                <div class="tm-librarylink" style="font-size:1.4em"><a href="#fonctions" class="link"><?php _e("Utilisation des fonctionnalit&eacute;s", "s1b"); ?></a></div>
            </div>        
        </div>
        <div class="ap-right">
            <div class="inner-padding-10">
                <div class="ap-line team-member">
                    <div class="tm-resume">
                        <div class="tm-name" id="preambule">Pr&eacute;ambule <a href="#" class="link" style="font-size:0.6em;">retour en haut</a></div>
                        <div class="tm-description" style="font-size:1.4em">
                            Cherbouquin est une communaut&eacute; de lecteurs qui partagent leurs livres, leurs coups de coeur et commentaires sur des oeuvres et auteurs. Cette approche de r&eacute;seau social autour du livre vous permettra de trouver des recommandations et conseils sur des livres mais &eacute;galement d'en exprimer. Ces recommandations et conseils seront d'autant plus nombreux que vous &eacute;tendrez votre r&eacute;seau &agrave; d'autres lecteurs.
                        </div>
                    </div>
                </div>

                <div class="ap-line team-member">
                    <div class="tm-resume">
                        <div class="tm-name" id="Cherbouquin">Quelles sont les fonctionnalit&eacute;s ?<a href="#" class="link" style="font-size:0.6em;">retour en haut</a></div>
                        <div class="tm-description" style="font-size:1.4em">
                            Les principales fonctionnalit&eacute;s de Cherbouquin sont : <br/>
                            - La gestion de votre biblioth&egrave;que de livres <br/>
                            - L'acc&egrave;s aux recommandations et conseils de lecture des membres ou d'amis <br/>
                            - Le suivi des pr&ecirc;ts et emprunts de livres r&eacute;alis&eacute;s aupr&egrave;s d'amis ou de tiers <br/>
                            - La cr&eacute;ation d'une liste d'envie de livres partag&eacute;e avec ses amis, &ccedil;a peut toujours servir pour les cadeaux ! <br/>
                            - La gestion de votre r&eacute;seau d'amis vous permettant d'acc&eacute;der &agrave; leurs lectures, coups de coeur, biblioth&egrave;que <br/>
                            - Le partage de vos coups de coeur par messagerie ou directement sur votre mur Facebook <br/>
                        </div>
                    </div>
                </div>

                <div class="ap-line team-member">
                    <div class="tm-resume">
                        <div class="tm-name" id="inscription">S'inscrire <a href="#" class="link" style="font-size:0.6em;">retour en haut</a></div>

                        <div class="tm-description" style="font-size:1.4em">
                            L'inscription sur Cherbouquin est totalement gratuite pour les particuliers. Elle vous permet d'acc&eacute;der aux fonctionnalit&eacute;s du site sans limite de livres.<br/>
                        </div>

                        <div class="tm-librarylink"><a href="/membre/inscription/" class="link" style="font-size:1.3em"><?php _e("Acc&eacute;der &agrave; la page d'inscription", "s1b"); ?></a></div>
                    </div>
                    <div class="tm-description" style="font-size:1.4em">
                        Deux possibilit&eacute;s pour vous inscrire : <br/>
                        * utiliser votre compte Facebook <br/>
                        * utiliser le formulaire (image n&deg;1). Une fois le formulaire valid&eacute; vous devrez activer votre compte gr&acirc;ce au lien qui se trouve dans l'email que vous allez recevoir dans les 5 minutes suivant votre inscription.<br/> 
                        ATTENTION cet email pourrait arriver dans vos courriers ind&eacute;sirables (Spams) de votre bo&icirc;te de r&eacute;ception.<br/>
                        Si vous n'avez pas re&ccedil;u cet email merci de nous contacter &agrave; l'aide du <a href="/contact" class="link">formulaire de contact</a><br/>
                        <br/>
                    </div>
                    <div class="tm-image">
                        <img class="image-frame" src="/images/tuto/page_inscription.png" />
                    </div>
                </div>



                <div class="ap-line team-member">
                    <div class="tm-resume">
                        <div class="tm-name" id="environnement">L'environnement <a href="#" class="link" style="font-size:0.6em;">retour en haut</a></div>
                        <div class="tm-description" style="font-size:1.4em">
                            Votre inscription r&eacute;alis&eacute;e, connectez-vous au site (image n&deg;2)<br/>
                            <br/>
                        </div>
                        <div class="tm-image">
                            <img class="image-frame" src="/images/tuto/connexion.png" /><br/>
                            <br/>
                        </div>
                    </div>

                    <div class="tm-description" style="font-size:1.4em">
                        Une fois connect&eacute; voici l'environnement que vous trouverez tout au long du site : <br/>
                        * La barre de navigation (image n&deg;3)<br/>
                        <br/>
                    </div>

                    <div class="tm-image">
                        <img class="image-frame" src="/images/tuto/barre_navigation.png" /><br/>
                        <br/>
                    </div>

                    <div class="tm-description" style="font-size:1.4em">
                        * Sur la droite de vos pages, votre menu d&eacute;di&eacute; &agrave; vos amis et votre messagerie (image n&deg;4)<br/>
                        <br/>
                    </div>

                    <div class="tm-image">
                        <img class="image-frame" src="/images/tuto/sidebare.png" /><br/>
                        <br/>
                    </div>
                </div>


                <div class="ap-line team-member">
                    <div class="tm-resume">
                        <div class="tm-name" id="premiers_pas">Vos premiers pas <a href="#" class="link" style="font-size:0.6em;">retour en haut</a></div>                           
                    </div>
                    <div class="tm-description" style="font-size:1.4em">
                        Lors de votre premi&egrave;re connexion commencez par mettre &agrave; jours vos donn&eacute;es de profil, votre photo ainsi que vos param&egrave;tres afin de d&eacute;terminier la confidentialit&eacute; que vous souhaitez appliquer &agrave; votre profil et vos donn&eacute;es. Pour cela cliquez sur Mon Profil dans votre barre de navigation (image n&deg;3). <br/>
                        <br/>Une fois vos donn&eacute;es de profil et vos param&egrave;tres d&eacute;termin&eacute;s, commencez par vous cr&eacute;er votre communaut&eacute; d'amis afin de pouvoir &eacute;changer avec eux sur vos livres pr&eacute;f&eacute;r&eacute;s et vos coups de coeur. Pour cela utilisez la barre de recherche d'amis de votre menu vertical (image n&deg;4) ou invitez des amis &agrave; rejoindre Cherbouquin en leur envoyant un email ou via Facebook.<br/>
                        <br/>Une fois votre communaut&eacute; r&eacute;unie commencez &agrave; renseigner vos livres dans votre biblioth&egrave;que.<br/>
                        <br/>
                    </div>
                </div>

                <div class="ap-line team-member">
                    <div class="tm-resume">
                        <div class="tm-name" id="fonctions">Utilisation des fonctionnalit&eacute;s  <a href="#" class="link" style="font-size:0.6em;">retour en haut</a></div>                           
                    </div>
                    <div class="tm-description" style="font-size:1.4em">
                        Prenons un exemple pour illustrer les fonctionnalit&eacute;s : L'Homme qui rit de Victor Hugo que nous recherchons dans la barre de recherche (images n&deg;3 et 5)<br/> 
                        <br/> 
                        La recherche ressort plusieurs r&eacute;sultats (image n&deg;5). Si vous souhaitez :<br/>
                        * ajouter le livre &agrave; votre biblioth&egrave;que : cliquez sur le bouton "Ajouter", un message vous indiquant que le livre a &eacute;t&eacute; correctement ajout&eacute; s'affiche et vous laisse la possibilt&eacute; de remplir votre fiche de lecture (vous arrivez sur l'image n&deg;8) ou de voir le livre (vous arrivez sur l'image n&deg;6)<br/>
                        * plus de d&eacute;tails sur le livre : cliquez sur le lien "Voir ce livre" (vous arrivez sur l'image n&deg;6)<br/>
                        * emprunter ce livre &agrave; un membre d&eacute;j&agrave; inscrit sur Cherbouquin ou une personne dont vous indiquerez le nom de votre choix : cliquez sur "Emprunter" (vous arrivez sur l'image n&deg;7)<br/> 
                        <br/>
                        <strong>Page de r&eacute;sultats de recherche<br/></strong>
                        <br/>
                    </div>
                    <div class="tm-image">
                        <img class="image-frame" src="/images/tuto/recherche_livre.png" /><br/>
                        <br/>
                    </div>
                    <div class="tm-description" style="font-size:1.4em">
                        <strong>Page d&eacute;tail du livre<br/></strong>
                        <br/>
                    </div>
                    <div class="tm-image">
                        <img class="image-frame" src="/images/tuto/voir_livre.png" /><br/>
                        <br/>
                    </div>
                    <div class="tm-description" style="font-size:1.4em">
                        <strong>Emprunt d'un livre<br/></strong>
                        <br/>
                    </div>
                    <div class="tm-image">
                        <img class="image-frame" src="/images/tuto/emprunt.png" /><br/>
                        <br/>
                    </div>
                    <div class="tm-description" style="font-size:1.4em">
                        <strong>Votre fiche de lecture<br/></strong>
                        <br/>
                    </div>
                    <div class="tm-image">
                        <img class="image-frame" src="/images/tuto/fiche_lecture.png" /><br/>
                        <br/>
                    </div>
                    <div class="tm-description" style="font-size:1.4em">
                        <strong>Votre biblioth&egrave;que<br/></strong>
                        Une fois votre livre ajout&eacute; vous le retrouvez dans votre biblioth&egrave;que.<br/>
                        <br/>
                    </div>
                    <div class="tm-image">
                        <img class="image-frame" src="/images/tuto/bibliotheque.png" /><br/>
                        <br/>
                    </div>
                    <div class="tm-description" style="font-size:1.4em">
                        <strong>Partagez les livres que vous aimez<br/></strong>
                        Lorsque vous cliquez sur le lien "Voir ce livre" dans votre biblioth&egrave;que (image n&deg;9) vous acc&eacute;dez &agrave; la fiche d&eacute;taill&eacute;e du livre qui vous permet &eacute;galement de partager ce livre avec vos amis.<br/>
                        <br/>
                    </div>
                    <div class="tm-image">
                        <img class="image-frame" src="/images/tuto/partage.png" /><br/>
                        <br/>
                    </div>


                    <div class="tm-description" style="font-size:1.4em">
                        <br/>Il ne nous reste plus qu'a vous souhaiter de belles lectures<br/>
                        <br/>
                    </div>
                    <div class="tm-resume">
                        <div class="tm-name" style="color:#239CD3">L'&eacute;quipe Cher bouquin<br/>
                        <a href="#" class="link" style="font-size:0.6em;">retour en haut</a></div>
                        <br/>
                    </div>       
                </div>

            </div>
        </div>        
    </div>
</div>
<?php get_footer(); ?>
