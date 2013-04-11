<?php

namespace Sb\Db\Service;

/**
 * Description of UserSvc
 *
 * @author Didier
 */
class MessageSvc extends \Sb\Db\Service\Service {

    private static $instance;

    /**
     *
     * @param String $baseDir
     * @return \Sb\Db\Service\MessageSvc
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Service\MessageSvc;
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct(\Sb\Db\Dao\MessageDao::getInstance(), "Message");
    }

    public function createWelcomeMessage($userId) {
        $siteName = \Sb\Entity\Constants::SITENAME;

        $title = __("Bienvenue au sein de la communauté", "s1b") . " " . $siteName;

        $body = __("Bonjour,", "s1b")
                . "<br/><br/>" . __("Merci d'avoir rejoint", "s1b") . " " . $siteName
                . "<br/>" . __("Toute l'équipe espère que vous profiterez pleinement des fonctionnalités du site, à savoir:",
                        "s1b")
                . "<br/>" . __("* partager vos lectures avec vos amis", "s1b")
                . "<br/>" . __("* leurs recommander un coup de coeur", "s1b")
                . "<br/>" . __("* trouver vos prochaines lectures grâce à leurs conseils", "s1b")
                . "<br/>" . __("* suivre les livres que vous prêtez ou que vous avez emprunté", "s1b")
                . "<br/>" . __("* utiliser les bibliothèques de vos amis, surtout leurs envies de lecture, pour leurs faire un cadeau réussi",
                        "s1b")
                . "<br/><br/>" . __("Bref, cette liste n'est pas exhaustive et nous espérons que vous nous aiderez à continuer à vous proposer de nouvelles fonctionnalités, par exemple en proposant à vos amis de rejoindre",
                        "s1b") . " " . $siteName . "."
                . "<br/>" . '<a href=' . \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_FRIENDS_FIND) . ' onclick="newInvite(); return false;">'
                . __("Inviter vos amis de Facebook à rejoindre", "s1b") . " " . $siteName . '</a>'
                . "<br/>" . __("Bonne expérience.", "s1b")
                . "<br/><br/>" . __("L'équipe", "s1b") . " " . $siteName;

        $message = new \Sb\Db\Model\Message;
        $message->setMessage($body);
        $message->setTitle($title);
        $message->setIs_read(false);
        $message->setRecipient(\Sb\Db\Dao\UserDao::getInstance()->get($userId));
        $message->setSender(\Sb\Db\Dao\UserDao::getInstance()->get(1));

        return \Sb\Db\Dao\MessageDao::getInstance()->add($message);
    }

}