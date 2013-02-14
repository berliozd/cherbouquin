<?php

namespace Sb\Helpers;

use Sb\Db\Model\User;
use Sb\Entity\Constants;

class MailHelper {

    public static function validationAccountEmailBody($firstName, $token, $to) {
        $body = __("Bonjour", "s1b") . " " . $firstName
                . '<br/><br/>'
                . __("Veuillez activer votre compte en cliquant sur le lien ci-après --> ", "s1b")
                . "<a href='" . \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::ACTIVATE) . "?Token=" . $token . "&Email=" . urlencode($to) . "'>" . __("activez votre compte", "s1b") . "</a>"
                . '<br/><br/>'
                . __("Nous vous conseillons de mettre à jour votre profil ainsi que vos paramètres puis d'ajouter vos amis afin de profiter au maximum du site", "s1b")
                . '<br/>'
                . __("Pour toute question ou remarque n'hésitez pas à nous envoyer un email à", "s1b") . " " . \Sb\Entity\Constants::SITENAME
                . " " . __("ou à utiliser le formulaire de contact du site", "s1b")
                . '<br/><br/>' . __("L'équipe", "s1b") . " " . \Sb\Entity\Constants::SITENAME;
        return $body;
    }

    public static function faceBookAccountCreationEmailBody($firstName) {
        $body = __("Bonjour", "s1b") . " " . $firstName
                . '<br/><br/>'
                . __("Votre compte a bien été créé avec Facebook.", "s1b")
                . '<br/><br/>'
                . __("Nous vous conseillons de mettre à jour votre profil ainsi que vos paramètres puis d'ajoouter vos amis afin de profiter au maximum du site", "s1b")
                . '<br/>'
                . __("Pour toute question ou remarque n'hésitez pas à nous envoyer un email à", "s1b") . " " . \Sb\Entity\Constants::SITENAME
                . " " . __("ou à utiliser le formulaire de contact du site", "s1b")
                . '<br/><br/>' . __("L'équipe", "s1b") . " " . \Sb\Entity\Constants::SITENAME;
        return $body;
    }

    public static function lendingRequestEmailBody($password, $login, $bookTitle) {
        $bodyTpl = new \Sb\Templates\Template('email/lendingRequest');
        $bodyTpl->set("url", \Sb\Helpers\HTTPHelper::Link(""));
        $bodyTpl->set("password", $password);
        $bodyTpl->set("login", $login);
        $bodyTpl->set("title", $bookTitle);
        $body = $bodyTpl->output();
        return $body;
    }

    public static function newPasswordBody($newPassword) {
        $body = __("Bonjour,", "s1b") . "<br \>" . __("Vous recevez cet e-mail suite à votre demande d'un nouveau mot de passe.", "s1b")
                . "<br/>" . __("Votre nouveau mot de passe est:", "s1b")
                . " " . $newPassword . "<br/>" . __("Rendez-vous dans votre profil section mot de passe pour le modifier.", "s1b")
                . "<br/><br/>" . __("L'équipe", "s1b") . " " . \Sb\Entity\Constants::SITENAME;
        return $body;
    }

    public static function newMessageArrivedBody($from) {
        $body = __("Bonjour", "s1b") . ','
                . '<br/>' . __("Vous avez reçu un message de", "s1b") . " " . $from . "."
                . '<br/>' . __("Rendez-vous sur votre", "s1b")
                . " " . "<a href='" . \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_MAILBOX) . "'>" . __("messagerie", "s1b") . "</a>"
                . " " . __("pour le découvrir", "s1b")
                . '<br/><br/>' . __("L'équipe", "s1b") . " " . \Sb\Entity\Constants::SITENAME
                . '<br/><br/>'
                . self::getProfileEditSettingLine();
        return $body;
    }

    public static function recommandationBody(\Sb\Db\Model\Book $book) {
        $body = __("Découvrez", "s1b")
                . " " . "<a href='" . \Sb\Helpers\HTTPHelper::Link($book->getLink()) . "'>"
                . $book->getTitle() . "</a>"
                . " " . __("de", "s1b") . " " . $book->getOrderableContributors()
                . '<br/><br/>' . __("L'équipe", "s1b") . " " . \Sb\Entity\Constants::SITENAME
                . '<br/><br/>'
                . self::getProfileEditSettingLine();
        return $body;
    }

    public static function friendRequestEmailBody($requestingUserUserName) {
        $body = sprintf(__("Bonjour,<br/><br/>Vous avez reçu une demande d'ami de %s<br/>Rendez-vous sur le site pour la valider <a href=\"%s\">%s</a>", "s1b"), $requestingUserUserName, \Sb\Helpers\HTTPHelper::Link(""), \Sb\Entity\Constants::SITENAME);
        return $body;
    }

    public static function friendShipAcceptationEmailBody($acceptingUserUserName) {
        $body = __("Bonjour,", "s1b") . '<br/><br/>' . $acceptingUserUserName . " " . __("a accepté votre demande d'ami", "s1b")
                . '<br/><br/>' . __("Retrouvez son profil sur", "s1b")
                . " " . "<a href='" . HTTPHelper::Link("") . "'>" . \Sb\Entity\Constants::SITENAME . "</a>"
                . '<br/><br/>'
                . self::getProfileEditSettingLine();
        return $body;
    }

    public static function friendShipDenyEmailBody($denyingUserUserName) {
        $body = __("Votre demande d'ami a été refusée par", "s1b") . " " . $denyingUserUserName
                . '<br/><br/>'
                . self::getProfileEditSettingLine();
        return $body;
    }

    public static function wishedUserBooksEmailBody(User $user, $userBooks) {

        $body = sprintf(__("Bonjour, <br/><br/>Voici les livres que souhaite %s : ", "s1b"), $user->getFriendlyName());

        $body .= "<ul>";
        foreach ($userBooks as $userbook) {
            $hasActiveGift = ($userbook->getActiveGiftRelated() != null);
            $body .= "<li>"
                    . "<a href=\"" . HTTPHelper::Link($userbook->getBook()->getLink()) . "\">" . $userbook->getBook()->getTitle() . "</a>"
                    . "&nbsp;"
                    . sprintf(__("de %s", "s1b"), $userbook->getBook()->getOrderableContributors())
                    . ($hasActiveGift ? "&nbsp;-&nbsp;" . __("ATTENTION ce livre a déjà été acheté par quelqu'un.", "s1b") : "")
                    . "</li>";
        }
        $body .= "</ul>";

        $body .= sprintf(__("<br/><br/>Cette liste vous a été envoyée depuis le site <a href=\"%s\">%s</a>", "s1b"), HTTPHelper::Link(""), Constants::SITENAME);

        return $body;
    }

    private static function getProfileEditSettingLine() {
        return '<h5>' . __("Si vous souhaitez ne plus recevoir par email les alertes modifiez les paramètres de votre profil dans", "s1b")
                . " " . "<a href='" . \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_PROFILE_SETTINGS) . "'>" . __("Mes paramètres", "s1b") . "</a>" . '</h5>';
    }

    public static function newCommentPosted($comment, $book) {
        $body = __("Bonjour<br/>Un commentaire a été posté sur la critique que vous avez émise sur le livre <a href=\"%s\">%s</a>.<br/><strong>Commentaire :</strong> %s<br/><br/><strong>L'équipe %s</strong>", "s1b");
        $body .= '<br/>' . self::getProfileEditSettingLine();
        $body = sprintf($body, HTTPHelper::Link($book->getLink()), $book->getTitle(), $comment, \Sb\Entity\Constants::SITENAME);
        return $body;
    }
}