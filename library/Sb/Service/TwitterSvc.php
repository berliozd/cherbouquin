<?php

namespace Sb\Service;

/**
 * Description of TwitterSvc
 * @author Didier
 */
class TwitterSvc extends Service {

    const CONTENT = "CONTENT";

    private static $instance;

    protected function __construct() {

        parent::__construct("Twitter");
    }

    /**
     *
     * @return TwitterSvc
     */
    public static function getInstance() {

        if (!self::$instance)
            self::$instance = new TwitterSvc();
        return self::$instance;
    }

    public function getContent() {

        try {
            $dataKey = self::CONTENT;
            $content = $this->getData($dataKey);
            if ($content === false) {
                require_once ('Twitter/twitteroauth.php');
                $consumer_key = '12cHf5Xj6VybkZxf8JsXQ';
                $consumer_secret = 'zb7tYe0fACKMULaGBPkrnT9De6qef7iulBkfdSqE';
                $oauth_token = '420353564-IU7k6sDvko07kwLZ9Z2FNVOB4dkJ6fPOpO9bGYDk';
                $oauth_token_secret = 'XXpAsdDB2qPiKJNlpZZ1wQ25Ky5KtepjjJGqmt1qNY';
                
                /* Create a TwitterOauth object with consumer/user tokens. */
                $connection = new \TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
                
                $user = "Cherbouquin"; /* Nom d'utilisateur sur Twitter */
                $count = 10; /* Nombre de message à afficher */
                
                $query = 'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' . $user . '&count=' . $count; // Your Twitter API query
                
                $content = $connection->get($query);
                
                $this->setData($dataKey, $content);
            }
            return $content;
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
    }

}