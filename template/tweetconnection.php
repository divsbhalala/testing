<?php

//session_start();
/* ----------Twitter authentication required file---------- */
require_once('lib/twitteroauth/TwitterOAuth.php');



require_once('config.php');

class tweetconnection {

    public $access_token = array();
    public $twitteroauth;
    public $downloadPath;

    public function get_tweetOauth($oauth_token = null, $oauth_token_secret = null) {
        if ($oauth_token == null || $oauth_token_secret == null) {
            header('Location: clearsession.php');
        }
        $this->twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauth_token, $oauth_token_secret);
        return $this->twitteroauth;
    }

     public function tweetError($tweets) {

        /* ---------check for tweet errors--------- */
       if (isset($tweets->error) && $tweets->error == "Not authorized") {
        header('location:clearsession.php?unauthorized=' . true);
        }
    }
    public function userInfoError($user_info) {

        /* ---------check for user information errors--------- */
      if (isset($user_info->errors)) {
        header('location:clearsession.php?error=' . $user_info->errors[0]->message);
        }
    }
    
    public function searchUser($user,$oauth_token,$oauth_token_secret) {
        $this->get_tweetOauth($oauth_token, $oauth_token_secret);
        $user_list=array();
        for($i=1;$i<=5;$i++)
        {
            $user_list1 = $this->twitteroauth->get("https://api.twitter.com/1.1/users/search.json?q=".$user."&page=".$i."&count=20");
            $user_list=array_merge($user_list,$user_list1);
        }

        $this->tweetError($user_list);
        return $user_list;
        
    }
    

}
