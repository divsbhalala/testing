<?php
session_start();
ini_set("display_errors", "1");
error_reporting(E_ALL);
require_once('facebookapi/facebook.php');
echo '<pre>';
// configuration

 $appid = '1560505420851840';
 $appsecret = 'bdddcd25a93bb9ce8e5fcb564f9f9280';
 $pageId = '418234554858781';
 $msg = 'Automatically post to Facebook from Your Website';
 $title = 'Automatically post to Facebook from Your Website';
 $uri = 'http://magicwallpost.com';
 $desc = 'Thsting auto post by divyesh';
 //$pic = 'http://blog.phpinfinite.com/wp-content/uploads/2012/11/post_to_facebook_from_php.jpg';
 //$pic = 'https://upload.wikimedia.org/wikipedia/en/1/11/Varkala_Beach_High_Res.jpg';
 //$pic = 'http://www.hd-wallpapers9.com/gallery/Flowers/Flowers%20High%20Resolution%20Wallpapers/Flowers%20High%20Resolution%20Wallpapers-017.jpg';
 //$pic = 'http://www.hd-free-wallpapers.org/wp-content/uploads/2014/09/widescreen-wallpapers-high-resolution-470x300.jpg';
 //$pic = 'http://www.adecco.com/MediaLibrary/HonoraryPresidents/PhilippeFDestezet01_LowRes.jpg';
 $pic = 'http://d1k2jfc4wnfimc.cloudfront.net/assets/centralotago/skinimages/lowresbanner.jpg';
 $action_name = 'Go to MagicWallPost';
 $action_link = 'http://magicwallpost.com';

$facebook = new Facebook(array(
 'appId' => $appid,
 'secret' => $appsecret,
 'cookie' => false,
 ));

$user = $facebook->getUser();
print_r($user);//;exit;
// Contact Facebook and get token
 if ($user) {
 // you're logged in, and we'll get user acces token for posting on the wall
 try {
 $userlist = $facebook->api("/me/friends");
 $page_infoq = $facebook->api("/me?fields=friends");
 $friendlists = $facebook->api("/me/friendlists");
 $userlisttag = $facebook->api("/me/taggable_friends");
 $feed = $facebook->api("/me/feed");
 //$toc = $facebook->api("/me?fields=access_token");
 //print_r($toc);
 $toc=$facebook->getAccessToken();
 print_r($toc);
 print_r($userlist);
 print_r($page_infoq);
 print_r($friendlists);
 print_r($userlisttag);
 print_r($feed);;
 $page_info = $facebook->api("/$pageId?fields=access_token");
 
 print_r($page_info);//exit;
 if (!empty($page_info['access_token'])) {
 /*----------------------USers Post--------------------
  * $attachment1 = array(
 'access_token' =>$toc,
 'message' => $msg,
 'name' => $title,
 'link' => $uri,
 'description' => $desc,
 'picture'=>$pic,
 'actions' => json_encode(array('name' => $action_name,'link' => $action_link))
 );
 $status = $facebook->api("/me/feed", "post", $attachment1);*/
 $attachment = array(
 'access_token' => $page_info['access_token'],
 'message' => $msg,
 'name' => $title,
 'link' => $uri,
 'description' => $desc,
 'picture'=>$pic,
 'actions' => json_encode(array('name' => $action_name,'link' => $action_link))
 );

$status = $facebook->api("/$pageId/feed", "post", $attachment);
 } else {
 $status = 'No access token recieved';
 }
 } catch (FacebookApiException $e) {
 error_log($e);
 $user = null;
 }
 } else {
 // you're not logged in, the application will try to log in to get a access token
 header("Location:{$facebook->getLoginUrl(array('scope' => 'user_friends,publish_actions,user_friends,user_status,user_photos,manage_pages'))}");
 }

 print_r($status);
 ?>
