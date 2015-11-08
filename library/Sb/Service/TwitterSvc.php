<?php

namespace Sb\Service;

/**
 * Description of TwitterSvc
 * @author Didier
 */
class TwitterSvc extends Service {

    const CONTENT = "CONTENT";

    private static $instance;
    /* @var \Sb\Config\Model\Config $config */
    private $config;

    protected function __construct($config) {

        $this->config = $config;
        parent::__construct("Twitter");
    }

    /**
     *
     * @return TwitterSvc
     */
    public static function getInstance($config) {

        if (!self::$instance)
            self::$instance = new TwitterSvc($config);
        return self::$instance;
    }

    public function getContent() {

        try {
            $dataKey = self::CONTENT;
            $content = $this->getData($dataKey);
            if ($content === false) {
                require_once ('Twitter/twitteroauth.php');
                $consumer_key = $this->config->getTwitterConsummerKey();
                $consumer_secret = $this->config->getTwitterConsummerSecret();
                $oauth_token = $this->config->getTwitterAuthToken();
                $oauth_token_secret = $this->config->getTwitterAuthTokenSecret();

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