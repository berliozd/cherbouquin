<?php

/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clefs secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/Editing_wp-config.php Modifier
 * wp-config.php} (en anglais). C'est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d'installation. Vous n'avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */
// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'share1book');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'share1book');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'share1book');

/** Adresse de l'hébergement MySQL. */
define('DB_HOST', 'localhost');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8');

/** Type de collation de la base de données.
 * N'y touchez que si vous savez ce que vous faites.
 */
define('DB_COLLATE', '');

/* * #@+
 * Clefs uniques d'authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n'importe quel moment, afin d'invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '^OFi?Ia!phO9DC~&!yqT_(Pto>KnBgG87NhX=mK=gL0gF4]1zVq.4I%79zfZ[M+z');
define('SECURE_AUTH_KEY', 'Vx1#;xpOO~e#fgeZUbYgzG6a{*$D|7FuE1{h`c$2xX12du!.dl[R2i}d3dyaT`:Q');
define('LOGGED_IN_KEY', '$BIOS#NL6uo6(6kbY8_?Blo tv}+N/#2ea%u}EO) (%PX/UL4hZ.jI*Wd?s~pJjJ');
define('NONCE_KEY', 'xvVlBN6s5v>v.n}Yc+%>/(jo+?c()CnuhB.1_VMJL>HM2CpSj|)~wD0GVJ^Ej9$l');
define('AUTH_SALT', 'd]+pfq_`ui?b2BmFx*ZuDk]qG&!QvBn;P}&WBshkt;Vo!Hg@::(SkrU}FRMlkq,2');
define('SECURE_AUTH_SALT', 'ZR_+nfk0|l2G;=oT:(Df/5.3XaP.7nI=qM.(gS?rseP1%AYOw5p6aL(ZFXa^!^Kj');
define('LOGGED_IN_SALT', 'X*mIAV3{jB8,%Y(BB8[)BTq1:%R?O@1)OLd2d3j24C~PAo^9{5Hid`8?uE6U7x9C');
define('NONCE_SALT', 'U{:<]/vY}C3!,4/ie*]C)?nraTGc,aA>4w-cu#7IQ`Wa-M9xO0!ia@DD6gM&^IK[');
/* * #@- */

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N'utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés!
 */
$table_prefix = 'wp_';

/**
 * Langue de localisation de WordPress, par défaut en Anglais.
 *
 * Modifiez cette valeur pour localiser WordPress. Un fichier MO correspondant
 * au langage choisi doit être installé dans le dossier wp-content/languages.
 * Par exemple, pour mettre en place une traduction française, mettez le fichier
 * fr_FR.mo dans wp-content/languages, et réglez l'option ci-dessous à "fr_FR".
 */
//define('WPLANG', 'fr_FR');
//define('WPLANG', 'en_US');

session_start();
if (isset($_REQUEST['lang'])) {
    $_SESSION['WPLANG'] = $_REQUEST['lang'];
    define('WPLANG', $_SESSION['WPLANG']);
} else {
    if (isset($_SESSION['WPLANG'])) {
        define('WPLANG', $_SESSION['WPLANG']);
        $_GET['lang'] = $_SESSION['WPLANG'];
    } else {
        define('WPLANG', 'fr_FR');
    }
}

/**
 * Pour les développeurs : le mode deboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l'affichage des
 * notifications d'erreurs pendant votre essais.
 * Il est fortemment recommandé que les développeurs d'extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 */
define('WP_DEBUG', false);

/* C'est tout, ne touchez pas à ce qui suit ! Bon blogging ! */

/** Chemin absolu vers le dossier de WordPress. */
if (!defined('ABSPATH'))
    define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');