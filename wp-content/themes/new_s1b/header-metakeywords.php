<?php
$defaultKeyords = "cher bouquin, cherbouquin, achat, acheter, art, atlas, auteur, avis, bande dessinee, bandes dessinées, bd, bibliotheque, bibliotheque en ligne, commentaires, communaute, communauté de lecteurs, contes, critiques, critiques de livres, cuisine, dictionnaire, ecrivain, editeur, emprunt, emprunter, fantasy, histoire, lecture, lire, littérature, livre, livre ancien, livre enfant, livre jeunesse, livre occasion, livre photo, livre scolaire, livres en ligne, logiciel gestion bibliotheque, manga, notes, notice, partage, philosophie, poesie, policier, prêt, prêter, recommandation livres, reseau, roman, science fiction, thriller, tourisme, vente livre, vin, voyage";
$aboutKeywords = "cher bouquin, cherbouquin, achat, acheter, avis, bande dessinee, bd, bibliotheque, bibliotheque en ligne, commentaires, communaute, communauté de lecteurs, critiques, critiques de livres, ecrivain, editeur, emprunt, lecture, lire, littérature, livre, livres en ligne, logiciel gestion bibliotheque, notes, notice, partage, prêt, prêter, recommandation livres, reseau, mentions légales, le projet, équipe, collaborateurs, formulaire, contact";
$pressKeywords = "cher bouquin, cherbouquin, achat, acheter, avis, bande dessinee, bd, bibliotheque, bibliotheque en ligne, commentaires, communaute, communauté de lecteurs, critiques, critiques de livres, ecrivain, editeur, emprunt, lecture, lire, littérature, livre, livres en ligne, logiciel gestion bibliotheque, notes, notice, partage, prêt, prêter, recommandation livres, reseau, presse, journal, Le Parisien, blog, mes petites idées, cultur'elle, l'insatiable, carnets d'une flaneuse parisienne";
//$newsLetterKeywords = "cher bouquin, cherbouquin, achat, acheter, avis, bande dessinee, bd, bibliotheque, bibliotheque en ligne, commentaires, communaute, communauté de lecteurs, critiques, critiques de livres, ecrivain, editeur, emprunt, lecture, lire, littérature, livre, livres en ligne, logiciel gestion bibliotheque, notes, notice, partage, prêt, prêter, recommandation livres, reseau, newsletter, courrier, lettre d'information";
$howItWorksKeywords = "cher bouquin, cherbouquin, achat, acheter, avis, bande dessinee, bd, bibliotheque, bibliotheque en ligne, commentaires, communaute, communauté de lecteurs, critiques, critiques de livres, ecrivain, editeur, emprunt, lecture, lire, littérature, livre, livres en ligne, logiciel gestion bibliotheque, notes, notice, partage, prêt, prêter, recommandation livres, reseau, comment ca marche, pas à pas, aide";
$helpUsKeywords = "cher bouquin, cherbouquin, achat, acheter, avis, bande dessinee, bd, bibliotheque, bibliotheque en ligne, commentaires, communaute, communauté de lecteurs, critiques, critiques de livres, ecrivain, editeur, emprunt, lecture, lire, littérature, livre, livres en ligne, logiciel gestion bibliotheque, notes, notice, partage, prêt, prêter, recommandation livres, reseau, nous aider, nous soutenir, help us, simple comme un clic";

$requestUri = substr($_SERVER["REQUEST_URI"], 0, strlen($_SERVER["REQUEST_URI"])-1);

switch ($requestUri) {
    case Sb\Helpers\HTTPHelper::Link(Sb\Entity\Urls::ABOUT, null, false, false):
        $keywords = $aboutKeywords;
        break;
    case Sb\Helpers\HTTPHelper::Link(Sb\Entity\Urls::HOW_TO, null, false, false):
        $keywords = $howItWorksKeywords;
        break;
    case Sb\Helpers\HTTPHelper::Link(Sb\Entity\Urls::PRESS, null, false, false):
        $keywords = $pressKeywords;
        break;
    case Sb\Helpers\HTTPHelper::Link(Sb\Entity\Urls::HELP_US, null, false, false):
        $keywords = $helpUsKeywords;
        break;
    default:
        $keywords = $defaultKeyords;
        break;
}
?>
<meta name="keywords" content="<?php echo $keywords; ?>" />