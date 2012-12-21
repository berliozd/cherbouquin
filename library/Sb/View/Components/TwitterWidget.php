<?php

namespace Sb\View\Components;

class TwitterWidget extends \Sb\View\AbstractView {

    function __construct() {
        parent::__construct();
    }

    public function get() {
        if ($this->getConfig()->getIsProduction()) {
            require_once('Twitter/twitteroauth.php');

            $consumer_key = '12cHf5Xj6VybkZxf8JsXQ';
            $consumer_secret = 'zb7tYe0fACKMULaGBPkrnT9De6qef7iulBkfdSqE';
            $oauth_token = '420353564-IU7k6sDvko07kwLZ9Z2FNVOB4dkJ6fPOpO9bGYDk';
            $oauth_token_secret = 'XXpAsdDB2qPiKJNlpZZ1wQ25Ky5KtepjjJGqmt1qNY';

            /* Create a TwitterOauth object with consumer/user tokens. */
            $connection = new \TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);

            $user = "Cherbouquin"; /* Nom d'utilisateur sur Twitter */
            $tag = "livre";
            $user_image = "https://si0.twimg.com/profile_images/2583277032/yxdfpkrxzfqs5et4s5vt.png";
            $count = 3; /* Nombre de message à afficher */

            $query = 'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' . $user . '&count=' . $count; //Your Twitter API query
            $content = $connection->get($query);

            $tpl = new \Sb\Templates\Template("components/twitterWidget");
            $tpl->setVariables(array("content" => $content,
                "user" => $user,
                "user_image" => $user_image));

            return $tpl->output();
        }
        else
            return "";
    }
}