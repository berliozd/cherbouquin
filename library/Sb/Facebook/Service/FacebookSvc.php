<?php

namespace Sb\Facebook\Service;
/**
 * Description of \Sb\Facebook\Service\FacebookSvc
 *
 * @author Didier
 */
class FacebookSvc {

    private $appId;
    private $secret;
    private $baseUrl;
    private $logOutUrl;
    private $facebook;

    function __construct($appId, $secret, $baseUrl, $logOutUrl) {
        $this->appId = $appId;
        $this->secret = $secret;
        $this->baseUrl = $baseUrl;
        $this->logOutUrl = $logOutUrl;
        $this->facebook = new \Facebook\Facebook(array('appId' => $this->appId, 'secret' => $this->secret, 'cookie' => true,));
    }

    public function getAppId() {
        return $this->appId;
    }

    public function getSecret() {
        return $this->secret;
    }

    public function getBaseUrl() {
        return $this->baseUrl;
    }

    public function getLogOutUrl() {
        return $this->logOutUrl;
    }

    public function getFacebook() {
        return $this->facebook;
    }

    public function getUser() {

        $facebookUser = null;
        $user_facebook = null;
        $user_facebook = $this->facebook->getUser();
        if ($user_facebook) {
            try {
                $user_profile = $this->facebook->api('/me');
            } catch (FacebookApiException $e) {
                $user_facebook = null;
            }
        }

        if (isset($user_profile) && $user_facebook) {
            try {
                $fql = "select uid, email, first_name, last_name, name, sex, hometown_location, birthday, locale, pic_small, pic from user where uid = " . $user_facebook;
                $param = array(
                    'method' => 'fql.query',
                    'query' => $fql,
                    'callback' => ''
                );
                $fb = $this->facebook->api($param);
                $fb = $fb[0];
                // Création d'un objet FacebookUser (\Sb\Facebook\Model\FacebookUser)
                $facebookUser = new \Sb\Facebook\Model\FacebookUser;
                if ($fb) {
                    \Sb\Facebook\Mapping\FacebookUserMapper::map($facebookUser, $fb);
                    return $facebookUser;
                }
            } catch (Exception $o) {

            }
        }
        return null;
    }

    public function cleanUser() {
        \Sb\Trace\Trace::addItem("svc facebook cleanUser");
        $this->facebook->destroySession();
    }

    public function post($message, $title, $caption, $link, $cover) {
        try {
            $ret = $this->facebook->api('/me/feed/', 'POST',
                    array(
                'message' => stripslashes($message),
                'name' => stripslashes($title),
                'caption' => stripslashes($caption),
                'link' => $link,
                'picture' => $cover
            ));
            return true;
        } catch (FacebookApiException $e) {
            return false;
        }
        return false;
    }

    public function getFacebookLogInUrl() {
        return $this->facebook->getLoginUrl(array( 'scope' => 'email,offline_access,publish_stream,user_birthday,user_location,user_about_me,user_hometown', 'next' => $this->baseUrl));
    }

    public function getFacebookLogOutUrl() {
        return $this->facebook->getLogoutUrl(array('next' => $this->logOutUrl));
    }

}

?>
